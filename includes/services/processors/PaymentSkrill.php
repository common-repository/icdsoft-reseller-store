<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentSkrill
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentSkrill extends PaymentProcessor {
	/**
	 * @var
	 */
	protected $merchantId;

	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * PaymentSkrill constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://pay.skrill.com' : 'https://pay.skrill.com' );

		$this->merchantId = $config['options']['id'];
		$this->secret     = $config['options']['secret'];
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
		$i           = 1;
		$item_params = array();
		foreach ( $order['items'] as $item ) {
			if ( $i <= 5 ) {
				/**
				 * You can show up to five additional
				 * details about the product in the More
				 * information section in the header of
				 * Quick Checkout.
				 */
				$item_params["detail{$i}_description"] = icd_hosting_tr( 'product_type.' . $item['product_type'] ) . ': ';
				$item_params["detail{$i}_text"]        = $item['item'];
			}
			$i ++;
		}

		return array(
			'url'    => $this->url,
			'type'   => 'online',
			'params' => array(
				            'pay_to_email'   => $this->merchantId,
				            // Email address of Skrill acc
				            'return_url'     => $this->returnUrl( array( 'order_id' => $order_id ) ),
				            // URL to which the customer is returned once the payment is made
				            'status_url'     => $this->postbackUrl(),
				            // URL to which the transaction details are posted after the payment process is complete.
				            // Alternatively, you may specify an email address where the results are sent.
				            // If the status_url is omitted, no transaction details are sent.
				            'transaction_id' => $order_id,
				            // unique reference or identification  number for the transaction
				            'cancel_url'     => $this->cancelUrl( array( 'order_id' => $order_id ) ),
				            'language'       => "EN",
				            // Can be any of BG, CS, DA, DE, EL, EN, ES, FI, FR, IT, ZH, NL, PL, RO, RU, SV, TR, or JA
				            'amount'         => $amount,
				            'currency'       => $currency,
				            'firstname'      => $order['firstname'],
				            'lastname'       => $order['lastname'],
				            'address'        => $order['address'],
				            'postal_code'    => $order['zip'],
				            'city'           => $order['city'],
				            'state'          => $order['state'],
				            'country'        => $order['country'],
			            ) + $item_params

		);
	}

	/**
	 * Detect a valid payment
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function detectPayment( $input ) {
		return $this->validPayment( $input );
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function validPayment( $input ) {
		$secretMD5 = strtoupper( md5( $this->secret ) );
		if ( empty( $input['transaction_id'] ) ) {
			// mail('web-dev-notify@suresupport.com', 'NO TMP ORDER POST Skrill IPN ', print_r($_POST, true));
			exit;
		}
		if ( isset( $this->secret ) and isset( $input['mb_amount'] ) and isset( $input['status'] ) ) {
			$md5 = strtoupper( md5( $input['merchant_id'] . $input['transaction_id'] . $secretMD5 . $input['mb_amount'] . $input['mb_currency'] . $input['status'] ) );

			if ( $input['md5sig'] == $md5 ) {
				echo "OK";

				return array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'             => null,
					'payment_request_id'   => $input['transaction_id'],
					'payment_type'         => $this->paymentType( $input ),
					'payment_method'       => $this->paymentMethod(),
					'transaction_id'       => $input['transaction_id'],
					'payment_date'         => date( 'Y-m-d H:i:s T' ),
					'merchant_id'          => $this->merchantId,
					'total'                => $this->paymentTotal( $input ),
					'ipn'                  => $input,
				);
			}
		}

	}

	/**
	 * Get payment type based on IPN payment status
	 *
	 * @param $ipn
	 *
	 * @return string
	 */
	protected function paymentType( $ipn ) {
		// -2 failed, 2 processed, 0 pending, -1 cancelled, -3 chargeback
		if ( $ipn['status'] == 2 ) {
			return 'incoming';
		}
		if ( $ipn['status'] == - 3 ) {
			return 'chargeback';
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
		return $ipn['amount'];
	}

}