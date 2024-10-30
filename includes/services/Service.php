<?php

namespace ICD\Hosting\Services;

/**
 * Class Service
 *
 * @package ICD\Hosting\Services
 */
class Service {
	/**
	 * @var ApiClient
	 */
	protected $api;

	protected $order_data = null;
	/**
	 * Service constructor.
	 *
	 * @param $api
	 */
	public function __construct( $api ) {
		$this->api = is_array( $api ) ? new ApiClient( $api ) : $api;
	}

	/**
	 * Get order data from API
	 *
	 * @return array
	 */
	public function getOrderData($params = []) {
		if (empty ($this->order_data)) {
			$this->order_data = $this->api->get( 'order-data' , $params);
		}
		return  $this->order_data;
	}

	/**
	 * Get data for payment request
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function getRequestData( $id ) {
		$commands = array(
			array( 'command' => 'store' ),
			array( 'command' => 'countries' ),
			array( 'command' => 'tlds' ),
			array( 'command' => 'order-request-details', 'params' => array( 'id' => $id, 'get_catalog' => 1 ) ),
		);
		$results  = $this->api->commands( $commands, 'GET' );

		return $results;
	}
}
