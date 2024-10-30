<?php

namespace ICD\Hosting\Services;

use ICD\Hosting\ICD_Hosting;

/**
 * Class DomainService
 *
 * @package ICD\Hosting\Services
 */
class DomainService extends Service {
	/**
	 * Get offered tlds
	 *
	 * @return array
	 * @throws AppException
	 */
	public function getOfferedDomainTLDs() {
		$result = $this->getOrderData();
		if ( empty( $result['data'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}
		OrderHelper::init( $result['data'] );

		return OrderHelper::offeredTLDs();
	}

	/**
	 * Check domain name availability
	 *
	 * @param $tld
	 * @param $sld
	 *
	 * @return array|bool|mixed|object|string
	 */
	public function checkDomain( $tld, $sld ) {
		return $this->api->get( 'domaincheck', array( 'tld' => $tld, 'sld' => $sld ) );
	}

	/**
	 * Execute commands call
	 *
	 * @param $commands
	 *
	 * @return array
	 */
	public function getCommands( $commands ) {
		return $this->api->get( 'commands', $commands );
	}

	/**
	 * Get preselected tlds
	 *
	 * @return array
	 */
	public function preselectedTLDs() {
		$settings = ICD_Hosting::instance()->getApp()->settings;
		if ( ! empty( $settings['preselected_tlds'] ) and is_array( $settings['preselected_tlds'] ) ) {
			return $settings['preselected_tlds'];
		}

		return [ 'com', 'net', 'org', 'info', 'biz', 'us' ];
	}
}
