<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;
use ICD\Hosting\Services\AppException;
use ICD\Hosting\Services\OrderService;

class ICD_Hosting_Shortcode_Hosting_Order {

	/**
	 * Output Hosting Order page
	 *
	 */
	public static function output() {
		global $wp;
		$dc       = empty( $_GET['dc'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['dc'] ) );
		$plan     = empty( $_GET['plan'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['plan'] ) );
		$domain   = empty( $_GET['domain'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['domain'] ) );
		$order_id = empty( $wp->query_vars['order_id'] ) ? '' : sanitize_text_field( wp_unslash( $wp->query_vars['order_id'] ) );

		$dc = ICD_Hosting::instance()->order_service->parseLocationParams( $dc );

		try {
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );

			if ( ! empty( $options['ignore_pretty_links'] ) and empty( $_SERVER['QUERY_STRING'] ) and ! empty( $_SERVER['REQUEST_URI'] ) and ! empty( $order_id ) ) {
				$request_uri = $_SERVER['REQUEST_URI'];
				$redirect    = str_replace( $order_id, '', $request_uri );
				$redirect    = str_replace( '//', '/', $redirect );
				wp_redirect( esc_url( $redirect . '?order_id=' . $order_id ) );
			}
			$result = ICD_Hosting::instance()->order_service->getOrderData();


			if ( empty( $result['data'] ) ) {
				icd_hosting_error( 'error.failed_to_retrieve_order_data' );
			}

			$viewdata = ICD_Hosting::instance()->order_service->getOrderViewData();
			if ( ! $viewdata['datacenters'] ) {
				icd_hosting_error( 'no_offered_hosting_plans' );
			}

			$formdata = ICD_Hosting::instance()->order_service->getFormData( $domain );
			$viewdata['tlds_extra_attributes'] = [];
			foreach ( $viewdata['tlds'] as $tld => $tld_info ) {
				$viewdata['tlds_extra_attributes'][ $tld ] = $tld_info['extra_attributes'];
			}

			// Existing order
			if ( $order_id ) {
				$order = ICD_Hosting::instance()->order_service->getOrderDetails( $order_id );
				foreach ($order['items'] as $order_item) {
					if ($order_item['product_type'] == 'ssl') {
						wp_redirect( icd_hosting_url( 'certificates', array( 'order_id' => $order_id ), true ) );
					}
				}

				// Restrict edit for not paid orders
				if ( in_array( $order['paid'], [ OrderService::PARTIALLY_PAID, OrderService::NOT_PAID ] ) ) {
					wp_redirect( icd_hosting_url( 'payment', array( 'order_id' => $order_id ), true ) );
				}

				if ( $order['paid'] == OrderService::FULLY_PAID ) {
					wp_redirect( icd_hosting_url( 'thankyou', array( 'order_id' => $order_id ), true ) );
				}

				$formdata                   = ICD_Hosting::instance()->order_service->prefillFormData( $order, $formdata, $viewdata['catalog'] );
				$viewdata['payment_method'] = ICD_Hosting::instance()->order_service->getOrderProcessorId( $order['payment_method'] );
			}

			$formdata['product_id']   = $viewdata['catalog'][ $formdata['location'] ][ $formdata['plan'] ]['product_id'];
			$formdata['order_create'] = 'icd_hosting_order_create';

			if ( $dc && isset($viewdata['catalog'][ $dc ])) {
				$formdata['location'] = $dc;
			}

			if ( $dc && $plan && isset( $viewdata['catalog'][ $dc ][ $plan ] ) ) {
				$formdata['plan']     = $plan;
			}

			$viewdata['ajax_url'] = icd_hosting_admin_ajax_url();
			$viewdata['formdata'] = $formdata;

			icd_hosting_get_template( 'hosting_order', $viewdata );
		} catch ( AppException $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
