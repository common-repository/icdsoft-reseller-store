<?php

namespace ICD\Hosting\Services;

use ICD\Hosting\Admin\ICD_Hosting_Admin_Settings;

/**
 * Class RegistrationService
 *
 * @package ICD\Hosting\Services
 */
class RegistrationService extends Service {

	/**
	 * Request new reseller account with store
	 *
	 * @param $data
	 *
	 * @return array|mixed
	 */
	public function registerStore( $data ) {
		return $this->api->get( 'requestResellerAccount', $data );
	}

	/**
	 * Check for account activation and apply API settings
	 *
	 * @return bool
	 */
	public function checkActivation() {
		$register_form_send = get_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND );

		if ( $register_form_send ) {
			$result = $this->api->get( 'getResellerWithStoreApiSettings', [ 'hash' => $register_form_send ] );

			if ( ! empty( $result['data']['store_details'] ) ) {
				$current_options            = get_option( ICD_Hosting_Admin_Settings::OPTION_NAME );
				$current_options['api_key'] = $result['data']['store_details']['apikey'];
				$current_options['api_sec'] = $result['data']['store_details']['apisecret'];
				update_option( ICD_Hosting_Admin_Settings::OPTION_NAME, $current_options );
				delete_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND );
				return true;
			}
		}

		return false;
	}

	/**
	 * Get reseller store
	 *
	 * @param array $data
	 *
	 * @return array|mixed
	 */
	public function getStore ( $data = [] ) {
		return $this->api->get( 'store', $data );
	}
}
