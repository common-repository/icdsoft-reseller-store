<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;
use ICD\Hosting\Services\AppException;

class ICD_Hosting_Shortcode_Thankyou {

	/**
	 * Output Thank you page
	 *
	 */
	public static function output() {
		global $wp;
		$order_id = empty( $wp->query_vars['order_id'] ) ? '' : $wp->query_vars['order_id'];

		try {
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );

			if ( ! empty( $options['ignore_pretty_links'] ) and empty( $_SERVER['QUERY_STRING'] ) and ! empty( $_SERVER['REQUEST_URI'] ) and ! empty( $order_id ) ) {
				$request_uri = $_SERVER['REQUEST_URI'];
				$redirect    = str_replace( $order_id, '', $request_uri );
				$redirect    = str_replace( '//', '/', $redirect );
				wp_redirect( esc_url( $redirect . '?order_id=' . $order_id ) );
			}

			$order             = ICD_Hosting::instance()->order_service->getOrderDetails( $order_id );
			$viewdata['order'] = ICD_Hosting::instance()->order_service->formatOrder( $order );

			icd_hosting_get_template( 'thankyou', $viewdata );
		} catch ( AppException $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
