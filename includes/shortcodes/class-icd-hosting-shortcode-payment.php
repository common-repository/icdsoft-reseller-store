<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;
use ICD\Hosting\Services\AppException;

class ICD_Hosting_Shortcode_Payment {

	/**
	 * Output Payment page
	 */
	public static function output() {
		global $wp;
		$order_id       = empty( $wp->query_vars['order_id'] ) ? '' : sanitize_text_field( wp_unslash( $wp->query_vars['order_id'] ) );
		$payment_method = empty( $wp->query_vars['icd_hosting_payment_method'] ) ? '' : sanitize_text_field( wp_unslash( $wp->query_vars['icd_hosting_payment_method'] ) );

		try {
			$options    = get_option( ICD_Hosting_Config::OPTION_NAME );

			if ( ! empty( $options['ignore_pretty_links'] ) and empty( $_SERVER['QUERY_STRING'] ) and ! empty( $_SERVER['REQUEST_URI'] ) and ! empty( $order_id ) ) {
				$request_uri = $_SERVER['REQUEST_URI'];
				$redirect    = str_replace( $order_id, '', $request_uri );
				$redirect    = str_replace( '//', '/', $redirect );
				wp_redirect( esc_url( $redirect . '?order_id=' . $order_id ) );
			}
			$order = ICD_Hosting::instance()->order_service->getOrderDetails( $order_id );

			if ( $order['total'] <= 0 or $order['paid'] == 'fully_paid' ) {
				wp_redirect( icd_hosting_url( 'thankyou', array( 'order_id' => $order['order_id'] ), true ) );
			}

			$viewdata['order']      = ICD_Hosting::instance()->order_service->formatOrder( $order );
			$viewdata['processors'] = ICD_Hosting::instance()->order_service->getPaymentProcessors( true );

			// Export payment processor related translations for use in the JavaScript code.
			$js_trans = array();
			foreach ( $viewdata['processors'] as $key => &$processor ) {
				$trans_key    = strtolower( $processor['name'] );
				$translations = icd_hosting_tr( $trans_key );

				if ( $translations !== $trans_key ) {
					$js_trans[ $trans_key ] = $translations;
				}

				if ( $processor['name'] == 'Braintree' ) {
					try {
						ICD_Hosting::instance()->load_braintree_lib();

						$braintree_gateway = new \Braintree\Gateway( [
							'environment' => $processor['test'] == '1' ? 'sandbox' : 'production',
							'merchantId'  => $processor['options']['id'],
							'publicKey'   => $processor['options']['public_key'],
							'privateKey'  => $processor['options']['private_key']
						] );

						$clientToken                          = $braintree_gateway->clientToken()->generate();
						$processor['options']['client_token'] = $clientToken;
					} catch ( \Braintree\Exception $exception ) {
						unset( $viewdata['processors'][ $key ] );
					}
				}

				$pr = ICD_Hosting::instance()->order_service->getPaymentProcessor( $processor['processor_id'] );
				$processor['onsite'] = $pr->isOnsite();
				if ($assets = $pr->loadAssets()) {
					foreach ($assets as $type => $assets) {
						foreach ($assets as $asset) {
							$viewdata['payment_assets'][$type][$asset] = $asset;
						}
					}
				}
			}

			if ( ! empty( $js_trans ) ) {
				$viewdata['js_trans'] = wp_json_encode( $js_trans );
			}

			$formdata['pay']      = 'icd_hosting_pay';
			$formdata['order_id'] = $order_id;
			$viewdata['formdata'] = $formdata;
			if ( isset( $order['payment_processor_id'] ) ) {
				$viewdata['payment_method'] = $order['payment_processor_id'];
			} else {
				$viewdata['payment_method'] = ICD_Hosting::instance()->order_service->getOrderProcessorId( $payment_method ? $payment_method : $order['payment_method'] );
			}
			$viewdata['ajax_url'] = icd_hosting_admin_ajax_url();
			$viewdata = icd_hosting_sanitize_all( $viewdata );

			icd_hosting_get_template( 'payment', $viewdata );
		} catch ( AppException $e ) {
			if ( is_admin() ) {
				//wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			} else {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
