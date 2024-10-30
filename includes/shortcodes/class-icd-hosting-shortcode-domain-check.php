<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\Services\AppException;

class ICD_Hosting_Shortcode_Domain_Check {

	/**
	 * Output the Domain Check page.
	 */
	public static function output() {
		try {
			$viewdata['tlds']             = ICD_Hosting::instance()->domain_service->getOfferedDomainTLDs();
			$viewdata['preselected_tlds'] = ICD_Hosting::instance()->domain_service->preselectedTLDs();
			$viewdata['ajax_url']         = icd_hosting_admin_ajax_url();

			icd_hosting_get_template( 'domain_check', $viewdata );
		} catch ( AppException $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
