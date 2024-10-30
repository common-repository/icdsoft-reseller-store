<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;
use ICD\Hosting\Services\AppException;
use ICD\Hosting\Services\CatalogHelper;
use ICD\Hosting\Services\OrderHelper;

class ICD_Hosting_Shortcode_Request {

	/**
	 * Output Payment Request page
	 */
	public static function output() {
		global $wp;
		try {
			$id = empty( $wp->query_vars['request_id'] ) ? '' : $wp->query_vars['request_id'];
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );

			if ( ! empty( $options['ignore_pretty_links'] ) and empty( $_SERVER['QUERY_STRING'] ) and ! empty( $_SERVER['REQUEST_URI'] ) and ! empty( $id ) ) {
				$request_uri = $_SERVER['REQUEST_URI'];
				$redirect    = str_replace( $id, '', $request_uri );
				$redirect    = str_replace( '//', '/', $redirect );
				wp_redirect( esc_url( $redirect . '?request_id=' . $id ) );
			}

			$results = ICD_Hosting::instance()->order_service->getRequestData( $id );

			if (empty($results[3]['status'])) {
				icd_hosting_error('error.invalid_order_request');
			}

			foreach ( $results as $result ) {
				if ( empty( $result['status'] ) ) {
					icd_hosting_error( $result['messages'][0]['message'] );
				}
			}

			$request_details = $results['3']['data']['order'];
			if ( $request_details['status'] == 'completed' ) {
				wp_redirect( icd_hosting_url( 'payment', array( 'order_id' => $request_details['order_id'] ), true ) );
			}
			if ( isset( $request_details['expired'] ) and $request_details['expired'] ) {
				icd_hosting_error( 'error.link_expired' );
			}

			$catalog_helper = new CatalogHelper( $results[3]['data']['catalog'], $results[3]['data']['products'] );
			$viewdata       = array(
				'request_id'     => $id,
				'store'          => $results[0]['data']['store'],
				'countries'      => OrderHelper::translateCountries( $results[1]['data']['countries'] ),
				'order'          => $request_details,
				'catalog'        => $results[3]['data']['catalog'],
				'products'       => $results[3]['data']['products'],
				'catalog_helper' => $catalog_helper,
				'processors'     => ICD_Hosting::instance()->order_service->getPaymentProcessors(),
				'tld_info'       => $results[2]['data']['tlds'],
				'terms'          => 1,
				'icann'          => 0,
				'icann_show'     => 0,
				'tlds'           => array(),
			);

			$viewdata['payment_method'] = isset( $viewdata['processors'][ $viewdata['order']['payment_method'] ] ) ?
				$viewdata['processors'][ $viewdata['order']['payment_method'] ] : key( $viewdata['processors'] );

			$viewdata = ICD_Hosting::instance()->order_service->requestCommonViewData($viewdata);

			$viewdata['ajax_url']            = icd_hosting_admin_ajax_url();
			$viewdata['request_prices']      = 'icd_hosting_request_prices';
			$viewdata['domain_check']        = 'icd_hosting_domain_check';
			$viewdata['approver_emails']     = 'icd_hosting_approver_emails';
			$viewdata['request_submit']      = 'icd_hosting_request_submit';
			$viewdata['price_change_action'] = 'icd_hosting_request_prices';

			icd_hosting_get_template( 'request', $viewdata );

		} catch ( AppException $e ) {
			if ( is_admin() ){
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			}
			icd_hosting_display_error( $e );
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
