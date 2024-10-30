<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentPayDollar
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentPayDollar extends PaymentProcessor {
	/**
	 * @var
	 */
	protected $merchantId;


	/**
	 * PaymentPayDollar constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://test.paydollar.com/b2cDemo/eng/payment/payForm.jsp' : 'https://www.paydollar.com/b2c2/eng/payment/payForm.jsp' );

		$this->merchantId = $config['options']['id'];

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
		$currency_code = null;
		//PayDollar e-commerce merchants can accept payments in HKD, USD, RMB (CNY) and range of currencies of Asian countries
		$currency_map = array(
			'HKD' => 344,
			'USD' => 840,
			'BGN' => 975,
			'AUD' => 036,
			'EUR' => 978,
			'CAD' => 124,
			'GBP' => 826,
			'CNY' => 156,
		);

		if ( array_key_exists( $currency, $currency_map ) ) {
			$currency_code = $currency_map[ $currency ];
		}

		return array(
			'url'    => $this->url,
			'type'   => 'online',
			'params' => array(
				'merchantId' => $this->merchantId,
				'orderRef'   => $order_id,
				'currCode'   => $currency_code,
				'amount'     => $amount - $discount,
				'lang'       => 'E',
				//'mpsMode' => 'NIL',
				'payMethod'  => 'ALL',
				'payType'    => 'N',
				// The payment type:  ”N” – Normal Payment (Sales)  ”H” – Hold Payment (Authorize only)
				'successUrl' => $this->returnUrl( array( 'order_id' => $order_id ) ),
				// For display purpose only
				'failUrl'    => $this->cancelUrl( array( 'order_id' => $order_id ) ),
				// For display purpose only
				'cancelUrl'  => $this->cancelUrl( array( 'order_id' => $order_id ) ),
				// For display purpose only
			)
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
		if ( isset( $input['successcode'] ) and isset( $input['PayRef'] ) and isset( $input['Ref'] ) ) {
			$this->postback_response = 'OK';

			return array(
				'payment_processor_id' => $this->config['processor_id'],
				'order_id'             => null,
				'payment_request_id'   => $input['Ref'],
				'payment_type'         => $this->paymentType( $input ),
				'payment_method'       => $this->paymentMethod(),
				'transaction_id'       => $input['PayRef'],
				'payment_date'         => date( 'Y-m-d H:i:s T', strtotime( $input['TxTime'] ) ),
				'merchant_id'          => $this->merchantId,
				'total'                => $this->paymentTotal( $input ),
				'ipn'                  => $input,
			);
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
		//0- succeeded, 1- failure, Others - error
		if ( $ipn['successcode'] == 0 ) {
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
		return $ipn['Amt'];
	}


}