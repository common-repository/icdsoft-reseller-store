<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;

class ICD_Hosting_Shortcode_Terms {

	/**
	 * Output Terms page
	 *
	 */
	public static function output() {
		try {
			ICD_Hosting::instance()->app->getTrans()->overload( 'terms' );
			icd_hosting_get_template( 'terms' );
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
