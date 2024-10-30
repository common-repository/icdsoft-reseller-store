<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentCash
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentCash extends PaymentProcessor {

	/**
	 * PaymentCash constructor.
	 *
	 * @param $config
	 */
	public function __construct( $config ) {
		parent::__construct( $config );
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
		return array(
			'url'    => '',
			'type'   => 'offline',
			'params' => array(
				'payment_processor_id' => $this->config['processor_id'],
				'mode'                 => 'Offline',
				'order_id'             => $order_id,
				'total'                => $amount - $discount,
				'currency'             => $currency,
			),
		);
	}
}