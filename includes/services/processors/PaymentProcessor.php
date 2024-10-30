<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentProcessor
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentProcessor {

	/**
	 * @var
	 */
	protected $config;

	/**
	 * @var
	 */
	protected $url;

	/**
	 * @var
	 */
	protected $request_id;

	/**
	 * Postback attributes
	 *
	 * @var array
	 */
	protected $postback_data = array(
		'payment_processor_id' => '',
		'order_id'             => '',
		'payment_request_id'   => null,
		'payment_type'         => '',
		'payment_method'       => '',
		'transaction_id'       => '',
		'payment_date'         => '',
		'merchant_id'          => '',
		'total'                => '',
		'ipn'                  => array(),
	);

	/**
	 * Postback response
	 *
	 * @var string
	 */
	protected $postback_response = '';

	/**
	 * Exchange rate
	 *
	 * @var int
	 */
	protected $exchange_rate = 1;

	/**
	 * PaymentProcessor constructor.
	 *
	 * @param $config
	 * @param null $url
	 */
	public function __construct( $config, $url = null ) {
		$this->config                                = $config;
		$this->url                                   = $url;
		$this->postback_data['payment_processor_id'] = $this->config['processor_id'];
		$this->postback_data['payment_method']       = $this->paymentMethod();
	}

	/**
	 * Get postback response
	 *
	 * @return string
	 */
	public function getPostBackResponse() {
		return $this->postback_response;
	}

	/**
	 * PaymentMethod class name
	 *
	 * @param null $processorId
	 *
	 * @return string
	 */
	public function paymentMethod( $processorId = null ) {
		return preg_replace( '/^ICD\\\\Hosting\\\\Services\\\\Processors\\\\Payment/', '', get_class( $this ) ) . ( is_null( $processorId ) ? '' : ":$processorId" );
	}

	/**
	 * Return URL
	 *
	 * @param array $params
	 *
	 * @return false|string
	 */
	public function returnUrl( $params = array() ) {
		return icd_hosting_url( 'thankyou', $params, true );
	}

	/**
	 * Cancel URL
	 *
	 * @param array $params
	 *
	 * @return false|string
	 */
	public function cancelUrl( $params = array() ) {
		/*return icd_hosting_url( 'hostingorder', $params, true );*/
		return icd_hosting_url( 'payment', $params, true );

	}

	/**
	 * Payment URL
	 *
	 * @param array $params
	 *
	 * @return false|string
	 */
	public function paymentUrl($params = array()) {
		return icd_hosting_url('payment', $params, true);
	}

	/**
	 * Postback URL
	 *
	 * @param array $params
	 *
	 * @return false|string
	 */
	public function postbackUrl( $params = array() ) {
		return ! empty( $this->config['postback_url'] ) ? $this->config['postback_url'] : icd_hosting_url( 'postback', $params, true );
	}

	/**
	 * Is online
	 *
	 * @return bool
	 */
	public function isOnline() {
		return $this->url || $this->config['onsite'] ? true : false;
	}

	/**
	 * Is on site
	 *
	 * @return bool
	 */
	public function isOnsite() {
		return (bool) $this->config['onsite'];
	}

	/**
	 * Detect valid payment
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function detectPayment( $input ) {
		return $this->validPayment( $input );
	}

	/**
	 * For payment method with token
	 * @see PaymentStripe
	 *
	 */
	public function requirePaymentToken() {
	}
	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function validPayment( $input ) {
		return false;
	}

	/**
	 * Set request ID
	 *
	 * @param $request_id
	 */
	public function setRequestId( $request_id ) {
		$this->request_id = $request_id;
	}

	/**
	 * Set exchange rate
	 *
	 * @param $rate
	 */
	public function setExchangeRate( $rate ) {
		$this->exchange_rate = $rate;
	}

	/**
	 * Calculate exchange amount
	 *
	 * @param $amount
	 *
	 * @return string
	 */
	protected function exchangeAmount( $amount ) {
		return round($amount * $this->exchange_rate, 2);
	}

	/**
	 * Load assets
	 * @return array
	 */
	public function loadAssets(){
		return [];
	}

	/**
	 * Get Hosting Order Label Translation
	 * @param $order_id
	 *
	 * @return mixed
	 */
	public function orderLabel($order_id) {
		return icd_hosting_tr('label.hosting_order_due', ['order_id' => $order_id]);
	}
}
