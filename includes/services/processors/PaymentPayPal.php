<?php

namespace ICD\Hosting\Services\Processors;

use ICD\Hosting\ICD_Hosting;

/**
 * Class PaymentPayPal
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentPayPal extends PaymentProcessor {
	/**
	 * PayPal REST API
	 * @var
	 */
	protected $api;
	/**
	 * Merchant Account ID
	 * @var mixed
	 */
	protected $merchantId;
	/**
	 * Checkout using PayPal API
	 * @var bool
	 */
	protected $api_checkout = false;

	/**
	 * PaymentPayPal constructor.
	 *
	 * Initialize PayPal merchant ID and sandbox gateway, API checkout
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr' );

		$this->merchantId = $config['options']['id'];

		if ( ! empty( $config['options']['client_id'] ) && ! empty( $config['options']['secret'] ) ) {
			$this->api['url']       = $config['test'] ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';
			$this->api['auth']      = base64_encode( "{$config['options']['client_id']}:{$config['options']['secret']}" );
			$this->api_checkout     = true;
			$this->config['onsite'] = true;
		}


	}

	/**
	 * Check if payment method required payment token
	 * @return bool
	 */
	public function requirePaymentToken()
	{
		return $this->api_checkout;
	}

	/**
	 * Create order to PayPal
	 *
	 * @param $details
	 *
	 * @return array|mixed
	 */
	public function createOrder($details)
	{
		try {
			$request_id = icd_hosting_array_get($details, 'payment_request_id', $this->request_id);
			$request_id = str_pad($request_id, 11, '0', STR_PAD_LEFT);
			$order_amount = round($details['amount'], 2);
			$currency = $details['currency'];

			$request = [
				'intent' => 'CAPTURE',
				'purchase_units' => [
					[
						'amount' => [
							'currency_code' => $currency,
							'value' => $order_amount,
							'breakdown' => [
								'item_total' => [
									'currency_code' => $currency,
									'value' => $order_amount,
								]
							],
						],
						'invoice_id' => $request_id,
						'custom_id' => $details['order_id'],
						'reference_id' => $request_id,
						'description' => $this->orderLabel($details['order_id']),
						'items' => [
							[
								'name' => $this->orderLabel($details['order_id']),
								'quantity' => 1,
								'unit_amount' => [
									'currency_code' => $currency,
									'value' => $order_amount,
								]
							],
						],
					],
				],
				'payment_source' => [
					'paypal' => [
						'experience_context' => [
							'payment_method_selected' => 'PAYPAL',
							'landing_page' => 'NO_PREFERENCE',
							'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
							'user_action' => 'PAY_NOW',
							'shipping_preference' => 'NO_SHIPPING',
							'return_url' => $this->returnUrl(['order_id' => $details['order_id']]),
							'cancel_url' => $this->cancelUrl(['order_id' => $details['order_id']])
						]
					]
				],
			];

			$result = $this->apiExec('/v2/checkout/orders', 'POST', $request);
			$result['token_id'] = $result['id'];
			return $result;
		} catch (Exception $e) {
			throw new Exception('paypal_order_create_failed');
		}
	}

	/**
	 * Process payment webhook
	 *
	 * @param $details
	 *
	 * @return array|void
	 */
	public function processWebhook($details)
	{
		$result = [];
		$order_details = [];
		$transaction = [];
		$payment_type = 'incoming';
		$event_type = $details['event_type'];
		if (empty($details['resource']['id'])) {
			return;
		}
		if (in_array($event_type, ['PAYMENT.CAPTURE.COMPLETED', 'PAYMENT.CAPTURE.REVERSED', 'PAYMENT.CAPTURE.REFUNDED'])) {
			$transaction = $this->getPayment($details['resource']['id']);
		}
		if ($event_type == 'PAYMENT.CAPTURED.SELF') { // Client/Self post from interface (RCP/Renew/Site)
			$transaction = $this->getPayment($details['resource']['purchase_units'][0]['payments']['captures'][0]['id']);
		}
		if (empty($transaction)) {
			return;
		}
		if (in_array($event_type, ['PAYMENT.CAPTURE.REVERSED', 'PAYMENT.CAPTURE.REFUNDED'])) {
			$payment_type = 'refund';
		}

		$order_details = $this->getOrder($transaction['supplementary_data']['related_ids']['order_id']);
		$result = [
			'payment_processor_id' => $this->config['processor_id'],
			'order_id' => $transaction['custom_id'],
			'payment_request_id' => $transaction['invoice_id'],
			'buyer_id' => array_get($order_details, 'payer.email_address'),
			'payment_type' => $payment_type,
			'payment_method' => $this->paymentMethod(),
			'transaction_id' => $transaction['id'],
			'payment_date' => date('Y-m-d H:i:s T', strtotime($transaction['create_time'])),
			'merchant_id' => $this->config['options']['id'],
			'total' => $transaction['amount']['value'],
			'fee' => $transaction['seller_receivable_breakdown']['paypal_fee']['value'],
			'currency' => $transaction['amount']['currency_code'],
			'ipn' => $details,
		];
		return $result;
	}

	/**
	 * Refund payment method
	 *
	 * @param $params
	 *
	 * @return void
	 */
	public function refundPayment($params)
	{
	}
	/**
	 * Detect a valid payment
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function detectPayment($input)
	{

		// Check for webhook data
		if (
			!empty($this->api) and
			!empty($input['event_type']) and
			!empty($input['resource_type'])
		) {
			return $this->processWebhook($input);
		}

		if (
			empty($input['payment_status']) or empty($input['txn_id']) or empty($input['payer_email']) or
			empty($input['invoice']) or empty($input['ipn_track_id']) or empty($input['mc_gross'])
		) {
			return;
		}

		if ($this->validPayment($input)) {
			if (preg_match('/^\d{1,14}$/', $input['invoice'])) {
				$payment_request_id = $input['invoice'];
				$order_id = '';
			} else {
				$payment_request_id = '';
				$order_id = $input['invoice'];
			}

			return array(
				'payment_processor_id' => $this->config['processor_id'],
				'payment_request_id' => $payment_request_id,
				'order_id' => $order_id,
				'payment_type' => $this->paymentType($input),
				'payment_method' => $this->paymentMethod(),
				'transaction_id' => $input['txn_id'],
				'payment_date' => date('Y-m-d H:i:s T', strtotime($input['payment_date'])),
				'merchant_id' => isset($input['receiver_email']) ? $input['receiver_email'] : '',
				'buyer_id' => isset($input['payer_email']) ? $input['payer_email'] : '',
				'total' => $this->paymentTotal($input),
				'fee' => isset($input['mc_fee']) ? -$input['mc_fee'] : 0,
				'currency' => isset($input['mc_currency']) ? $input['mc_currency'] : '',
				'ipn' => $input,
			);
		}
	}

	/**
	 * Detect event notification
	 *
	 * @param $input
	 *
	 * @return array|void
	 */
	public function detectNotification($input)
	{

		// Check for webhook data
		if (!empty($input['event_type']) && !empty($input['id'])) {
			return $this->checkWebhook($input);
		}

		if (empty($input['ipn_track_id'])) {
			return;
		}

		if ($this->validPayment($input)) {
			return array(
				'payment_processor_id' => $this->config['processor_id'],
				'payment_type' => 'notification',
				'payment_method' => $this->paymentMethod(),
				'ipn' => $input,
			);
		}
	}

	/**
	 * Checkout PayPal order
	 *
	 * @param $order_id
	 *
	 * @return false|mixed
	 */
	public function getOrder($order_id)
	{
		$details = $this->apiExec("/v2/checkout/orders/{$order_id}", 'GET');
		if (!$details)
			return false;

		return $details;
	}

	/**
	 * Load PayPal JS SDK
	 * @return array|array[]
	 */
	public function loadAssets()
	{
		if (empty($this->api_checkout)) {
			return [];
		}
		return [
			'js' => [
				'https://www.paypal.com/sdk/js?intent=capture&commit=true&currency='
				. $this->config['options']['currency']
				. '&client-id=' . $this->config['options']['client_id'],
			]
		];
	}

	/**
	 * Create Payment Session
	 *
	 * @see createOrder()
	 * @param $details
	 *
	 * @return array|mixed
	 */
	public function createPaymentSession($details)
	{
		return $this->createOrder($details);
	}

	/**
	 * Create Payment Session
	 *
	 * @see createOrder()
	 * @param $details
	 *
	 * @return array|mixed
	 */
	public function createPaymentToken($details)
	{
		if (empty($this->api_checkout)) return;
		return $this->createOrder($details);
	}

	/**
	 * Payment method specific attributes
	 *
	 * @param $order_id
	 * @param $amount
	 * @param $currency
	 * @param array $order
	 * @param int $discount
	 *
	 * @return array
	 */
	public function paymentData($order_id, $amount, $currency, $order = array(), $discount = 0) {
		$i = 0;
		$item_params = array();
		$cart_total = 0;
		foreach ($order['items'] as $item) {
			$i++;
			$item_params["item_name_$i"] = icd_hosting_format_item_label($item);
			//$item_params["quantity_$i"] = isset($item['quantity']) ? $item['quantity'] : 1;
			$item_params["amount_$i"] = $this->exchangeAmount($item['price'] + $item['price'] * $item['vat'] / 100);
			$cart_total += $item_params["amount_$i"];
		}

		if ($cart_total > $amount) {
			$discount = $cart_total - $amount;
		}

		// clean up exchange differences
		else if ($cart_total != $amount) {
			$diff = $cart_total - $amount;
			for ($j = 1; $j < $i; $j++) {
				if ($item_params["amount_$j"] + $diff > 0) {
					$item_params["amount_$j"] = $item_params["amount_$j"] + $diff;
					break;
				}
			}
		}

		return array(
			'url' => $this->url,
			'type' => 'online',
			'params' => array(
				            'cmd' => '_cart',
				            'upload' => 1,
				            'business' => $this->merchantId,
				            'amount' => $amount,
				            'currency_code' => $currency,
				            'discount_amount_cart' => $discount,
				            'invoice' => str_pad($this->request_id, 11, '0', STR_PAD_LEFT),
				            'no_note' => 1,
				            'no_shipping' => 1,
				            'return' => $this->returnUrl(array('order_id' => $order_id)),
				            'cancel_return' => $this->cancelUrl(array('order_id' => $order_id)),
				            'notify_url' => $this->postbackUrl(),
				            'charset' => 'utf-8'
			            ) + $item_params
		);
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function validPayment($input) {
		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		$get_magic_quotes_exists = function_exists('get_magic_quotes_gpc');
		foreach ($input as $key => $value) {
			if ($get_magic_quotes_exists && get_magic_quotes_gpc())
				$value = stripslashes($value);

			$value = urlencode($value);
			$req .= "&$key=$value";
		}

		// Step 2: POST IPN data back to PayPal to validate
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: IPN Verification'));

		$res = curl_exec($ch);
		curl_close($ch);

		return $res === 'VERIFIED' ? true : false;
	}

	/**
	 * Get payment type based on IPN payment status
	 *
	 * @param $ipn
	 *
	 * @return string
	 */
	public function paymentType($ipn) {
		if (in_array($ipn['payment_status'], ['Denied', 'Expired', 'Failed', 'Refunded', 'Reversed', 'Voided']))
			return in_array($ipn['reason_code'], ['chargeback', 'guarantee', 'buyer-complaint']) ? 'chargeback' : 'refund';

		if ($ipn['payment_status'] == 'Completed' and isset($ipn['txn_type']) and $ipn['txn_type'] == 'adjustment' and $ipn['mc_gross'] < 0)
			return 'refund';

		if (in_array($ipn['payment_status'], ['Completed', 'Created', 'Processed', 'Canceled_Reversal']))
			return 'incoming';

		return 'error';
	}

	/**
	 * Total payment amount
	 *
	 * @param $ipn
	 *
	 * @return mixed
	 */
	protected function paymentTotal($ipn) {
		return $ipn['mc_gross'];
	}

	/**
	 * Shell formatted command
	 *
	 * @see apiExec()
	 * @param $cmd
	 * @param $method
	 * @param $data
	 *
	 * @return string
	 */
	public function apiCmd($cmd, $method = 'GET', $data = [])
	{
		if ($data) {
			$shell = "curl -X $method " . escapeshellarg("{$this->api['url']}$cmd") . " \
-H 'Content-Type: application/json' \
-H 'Authorization: Basic {$this->api['auth']}' \
-d " . escapeshellarg(json_encode($data));
		} else {
			$shell = "curl -X $method " . escapeshellarg("{$this->api['url']}$cmd") . " \
-H 'Content-Type: application/json' \
-H 'Authorization: Basic {$this->api['auth']}'";
		}

		return $shell;
	}

	/**
	 * Execute commands to PayPal API
	 *
	 * @param $cmd
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|void
	 */
	public function apiExec($cmd, $method = 'GET', $data = [])
	{
		if (!$this->api)
			icd_hosting_error('api_not_configured');

		if (!in_array($method, ['GET', 'POST', 'PATCH']))
			icd_hosting_error('invalid_method');

		//$cmd = '/' . ltrim($cmd, '/');

		$settings = ICD_Hosting::instance()->getApp()->settings;

		if ( ! empty( $settings['ipn_log'] ) ) {
			icd_hosting_custom_log( $settings['ipn_log'], $this->apiCmd($cmd, $method, $data));
		}

		$ch = curl_init("{$this->api['url']}$cmd");
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Basic {$this->api['auth']}"]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ($method != 'GET') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			if ($method == 'PATCH')
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		}

		$response = curl_exec($ch);
		$http_code =  curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ( ! empty( $settings['ipn_log'] ) ) {
			icd_hosting_custom_log( $settings['ipn_log'], "Response:\n" . "$http_code: $response\n");
		}

		if (in_array($http_code, [200, 201])) {
			$result = json_decode($response, true);
			return $result;
		}

		if (in_array($http_code, [204, 404]))
			return;

		icd_hosting_error('paypal_api_error');
	}
}
