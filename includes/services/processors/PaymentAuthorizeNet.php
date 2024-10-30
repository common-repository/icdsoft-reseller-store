<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentAuthorizeNet
 * @package ICD\Hosting\Services\Processors
 */
class PaymentAuthorizeNet extends PaymentProcessor {
	/**
	 * @var
	 */
	protected $merchantId;

	/**
	 * @var
	 */
	protected $transactionKey;

	/**
	 * @var
	 */
	protected $secret;

	/**
	 * PaymentAuthorizeNet constructor.
	 *
	 * Initialize ID, Transaction Key, Secret and sandbox gateway
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://test.authorize.net/gateway/transact.dll' : 'https://secure2.authorize.net/gateway/transact.dll' );

		$this->merchantId     = $config['options']['id'];
		$this->transactionKey = $config['options']['transactionKey'];
		$this->secret         = $config['options']['secret'];
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
	public function paymentData( $order_id, $amount, $currency, $order = array(), $discount = 0 ) {
		$item_params = array();
		$tstamp      = time();
		$i           = 1;

		$total       = $amount - $discount;
		$fingerprint = hash_hmac( "md5", $this->merchantId . "^" . $order_id . "^" . $tstamp . "^" . $total . "^" . $currency, $this->transactionKey );
		$phone_code  = '(' . $order['phone_country_code'] . ')';
		$fax_code    = '(' . $order['fax_country_code'] . ')';
		$phone       = $phone_code . $order['phone'];
		$fax         = $fax_code . $order['fax'];
		$cart_total  = 0;
		$cart_items  = array();
		foreach ( $order['items'] as $item ) {
			//$quantity = isset($item['quantity']) ? $item['quantity'] : 1;
			$quantity     = 1;
			$price = $this->exchangeAmount( $item['price'] + $item['price'] * $item['vat'] / 100 );
			$product_type = ! empty( $item['product_type'] ) ? $item['product_type'] : 'Service';
			$value_item = "{$i}<|>{$product_type}<|>" . icd_hosting_format_item_label( $item ) . "<|>{$quantity}<|>{$price}<|>N";
			$cart_items[] = array( 'key' => 'x_line_item', 'value' => $value_item );
			$cart_total += $price;
			$i ++;
		}
		$item_params['x_description'] = icd_hosting_tr( 'hosting_order_due', array( 'order_id' => $order_id ) );
		if ( $cart_total > $amount ) {
			$cart_items = array();

		} else {
			$item_params = $item_params + $cart_items;
		}

		return array(
			'url'    => $this->url,
			'type'   => 'online',
			'params' => array(
				            'x_version'        => '3.1',
				            'x_login'          => $this->merchantId,
				            'x_type'           => "AUTH_CAPTURE",
				            // auth_capture, auth_only
				            'x_amount'         => $total,
				            'x_show_form'      => 'PAYMENT_FORM',
				            'x_currency_code'  => $currency,
				            // Setting this field to a currency that is not supported by the payment processor results in an error.
				            'x_fp_hash'        => $fingerprint,
				            // The unique transaction fingerprint
				            'x_fp_sequence'    => $order_id,
				            // The merchant-assigned sequence number for the transaction
				            'x_invoice_num'    => str_pad( $this->request_id, 11, '0', STR_PAD_LEFT ),
				            // The merchant-assigned invoice number for the transaction
				            'x_fp_timestamp'   => $tstamp,
				            // The timestamp at the time of fingerprint generation
				            'x_relay_response' => 'TRUE',
				            // Indicates whether a relay response is desired.
				            'x_relay_url'      => $this->postbackUrl(),
				            'x_first_name'     => $order['firstname'],
				            'x_last_name'      => $order['lastname'],
				            'x_address'        => $order['address'],
				            'x_city'           => $order['city'],
				            'x_state'          => $order['state'],
				            'x_zip'            => $order['zip'],
				            'x_country'        => $order['country'],
				            'x_email'          => $order['email'],
				            'x_phone'          => $phone,
				            'x_fax'            => $fax,
			            ) + $item_params

		);
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function validPayment( $input ) {
		if (isset($this->secret) and isset($input['x_trans_id']) and isset($input['x_amount'])) {
			$textToHash = $this->textToHsh($input);
			$sig = hash_hmac('sha512', $textToHash, hex2bin($this->secret));

			if ($input['x_SHA2_Hash'] == strtoupper($sig)) {
				return array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'=> null,
					'payment_request_id' => intval($input['x_invoice_num']),
					'payment_type' => $this->paymentType($input),
					'payment_method' => $this->paymentMethod(),
					'transaction_id' => !empty($input['x_trans_id']) ? $input['x_trans_id'] : $input['x_trans_id'].'-'.$input['x_invoice_num'],
					'payment_date' => date('Y-m-d H:i:s T'),
					'merchant_id' => $this->merchantId,
					'total' => $this->paymentTotal($input),
					'ipn' => $input,
				);
			}
		}
	}

	/**
	 * Get payment type
	 *
	 * @param $ipn
	 *
	 * @return string
	 */
	protected function paymentType( $ipn ) {
		//1 - Approved, 2 - Decilined, 3 - Error, 4 - Held for Review
		if ( $ipn['x_response_code'] == 1 ) {
			return 'incoming';
		}

		return 'error';
	}

	/**
	 * Total payment amount
	 *
	 * @param $ipn
	 *
	 * @return mixed
	 */
	protected function paymentTotal( $ipn ) {
		return $ipn['x_amount'];
	}

	private function textToHsh($input) {
		$sign_fields = [
			'x_trans_id',
			'x_test_request',
			'x_response_code',
			'x_auth_code',
			'x_cvv2_resp_code',
			'x_cavv_response',
			'x_avs_code',
			'x_method',
			'x_account_number',
			'x_amount',
			'x_company',
			'x_first_name',
			'x_last_name',
			'x_address',
			'x_city',
			'x_state',
			'x_zip',
			'x_country',
			'x_phone',
			'x_fax',
			'x_email',
			'x_ship_to_company',
			'x_ship_to_first_name',
			'x_ship_to_last_name',
			'x_ship_to_address',
			'x_ship_to_city',
			'x_ship_to_state',
			'x_ship_to_zip',
			'x_ship_to_country',
			'x_invoice_num',
		];

		$textToHash1 = '';
		foreach ($sign_fields as $field) {
			if (isset($input[ $field ])) {
				$textToHash1 .= '^' . $input[$field];
			}
		}

		$textToHash1 .= '^';

		return $textToHash1;
	}

}
