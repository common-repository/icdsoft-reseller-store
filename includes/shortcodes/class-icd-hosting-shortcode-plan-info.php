<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;

class ICD_Hosting_Shortcode_Plan_Info {

	/**
	 * Output plan info page
	 *
	 * @param $atts array User supplied shortcode arguments. ["location" , "plan"]
	 */
	public static function output( $atts ) {
		try {
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );
			$order_data = ICD_Hosting::instance()->order_service->getOrderViewData();
			$location   = isset( $atts['location'] ) ? $atts['location'] : key( $order_data['datacenters'] );
			$location = ICD_Hosting::instance()->order_service->parseLocationParams( $location );

			$plan       = isset( $order_data['catalog'][ $location ] ) ? key( $order_data['catalog'][ $location ] ) : '';

			if ( empty( $plan ) ) {
				return false;
			}

			$attrs = shortcode_atts( array(
				'plan'     => $plan,
				'location' => $location,
			), $atts );

			$plan     = mb_strtolower( $attrs['plan'] );
			$location = mb_strtolower( $attrs['location'] );

			$location = ICD_Hosting::instance()->order_service->parseLocationParams( $location );
			$viewdata = ICD_Hosting::instance()->order_service->getPlanInfo( $location, $plan, $order_data );

			$viewdata['show_renew_price'] = ! empty( $options['show_renew_price'] ) ? true : false;
			$viewdata['show_monthly_price'] = ! empty( $options['show_monthly_price'] ) ? true : false;

			if ( empty( $viewdata['price'] ) ) {
				return false;
			}

			icd_hosting_get_template( 'plan_info', $viewdata );
		} catch ( \Exception $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		}
	}
}
