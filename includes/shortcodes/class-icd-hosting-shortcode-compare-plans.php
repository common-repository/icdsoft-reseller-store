<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;
use ICD\Hosting\Services\AppException;

class ICD_Hosting_Shortcode_Compare_Plans {

	/*
	 * Output block listed hosting plans for concrete location
	 *
	 * @param $atts array User supplied shortcode arguments. ["location" ]
	 */
	public static function output( $atts ) {
		try {
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );
			$order_data = ICD_Hosting::instance()->order_service->getOrderViewData();
			$location   = key( $order_data['datacenters'] );

			$attrs = shortcode_atts( array(
				'location' => $location,
			), $atts );

			$location = mb_strtolower( $attrs['location'] );
			$location =	ICD_Hosting::instance()->order_service->parseLocationParams( $location );

			if ( isset ( $order_data['catalog'][ $location ] ) ) {
				foreach ( $order_data['catalog'][ $location ] as $plan => $info ) {
					$viewdata['plans'][ $plan ] = ICD_Hosting::instance()->order_service->getPlanInfo( $location, $plan, $order_data );
				}
			}

			$viewdata['plans']              = ! empty( $viewdata['plans'] ) ? $viewdata['plans'] : [];
			$viewdata['currency']           = ! empty( $order_data['store']['currency'] ) ? $order_data['store']['currency'] : 'USD';
			$viewdata['plans_per_row']      = ! empty( $options['plans_per_row'] ) ? $options['plans_per_row'] : 3;
			$viewdata['show_renew_price']   = ! empty( $options['show_renew_price'] ) ? true : false;
			$viewdata['show_monthly_price'] = ! empty( $options['show_monthly_price'] ) ? true : false;

			icd_hosting_get_template( 'compare_plans', $viewdata );
		} catch ( AppException $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
