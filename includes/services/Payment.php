<?php

namespace ICD\Hosting\Services;

/**
 * Class Payment
 * @package ICD\Hosting\Services
 */
class Payment {

	/**
	 * @var bool
	 */
	public $testMode;

	/**
	 * Stores payment processors
	 *
	 * @var array|mixed
	 */
	public $processors;

	/**
	 * Store postback response
	 *
	 * @var string
	 */
	protected $postback_response;

	/**
	 * Payment constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings = array() ) {
		$this->testMode   = ! empty( $settings['test_mode'] ) ? true : false;
		$this->processors = ! empty( $settings['processors'] ) ? $settings['processors'] : array();
	}

	/**
	 * Get payment method ID
	 *
	 * @param $paymentMethod
	 *
	 * @return mixed
	 */
	public function orderProcessorId( $paymentMethod ) {
		$parts = explode( ':', $paymentMethod );
		foreach ( $this->processors as $p ) {
			if ( isset( $parts[1] ) and $parts[1] == $p['processor_id'] or ! isset( $parts[1] ) and $parts[0] == $p['name'] ) {
				return $p['processor_id'];
			}
		}

		$processor = reset( $this->processors );

		return $processor['processor_id'];
	}

	/**
	 * Get selected processor
	 *
	 * @param null $processor
	 * @param null $payment_request_id
	 *
	 * @return null
	 */
	public function get( $processor = null, $payment_request_id = null ) {
		$config       = isset( $this->processors[ $processor ] ) ? $this->processors[ $processor ] : reset( $this->processors );
		$paymentClass = "\ICD\Hosting\Services\Processors\Payment{$config['name']}";
		$processor    = new $paymentClass( $config );
		$processor->setRequestId( $payment_request_id );

		return $processor;
	}

	/**
	 * Prepare payment processors
	 *
	 * @param bool $full
	 *
	 * @return array
	 */
	public function processors( $full = false ) {
		$result = array();
		foreach ( $this->processors as $processor => $config ) {
			if ( $full ) {
				$result[ $processor ] = $config;
			} else {
				$result[ $processor ] = $config['display_name'];
			}
		}

		return $result;
	}

	/**
	 * Detect payment method
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function detectPayment( $input ) {
		foreach ( $this->processors as $p => $config ) {
			$processor = $this->get( $p );
			if ( $details = $processor->detectPayment( $input ) ) {
				$this->postback_response = $processor->getPostBackResponse();

				return $details;
			}
		}
	}

	/**
	 * Get postback response
	 *
	 * @return mixed
	 */
	public function getPostBackResponse() {
		return $this->postback_response;
	}
}
