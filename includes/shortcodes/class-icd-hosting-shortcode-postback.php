<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\Services\AppException;

class ICD_Hosting_Shortcode_Postback {

	/**
	 * Postback page for payment processors callback
	 */
	public static function output() {
		try {
			$input    = $_POST;
			$debug    = wp_json_encode( $input, JSON_PRETTY_PRINT );
			$settings = ICD_Hosting::instance()->getApp()->settings;

			if ( ! empty( $settings['ipn_log'] ) ) {
				file_put_contents( $settings['ipn_log'], gmdate( 'Y-m-d H:i:s' ) . " UTC\n" . $debug . "\n\n", FILE_APPEND );
			}

			$payment = ICD_Hosting::instance()->order_service->detectPayment( $input );

			if ( ! $payment ) {
				icd_hosting_error( 'error.detecting_payment' );
			}

			$route           = 'payment';
			$redirect_params = [];

			if ( $payment['payment_type'] != 'error' ) {
				$result = ICD_Hosting::instance()->order_service->addPayment( $payment );
				$debug  .= "\n" . wp_json_encode( $payment, JSON_PRETTY_PRINT ) . "\n" . wp_json_encode( $result, JSON_PRETTY_PRINT );
				if ( empty( $result['status'] ) ) {
					icd_hosting_error( 'error.adding_payment' );
				}

				$order                       = ICD_Hosting::instance()->order_service->getOrderDetails( $result['data']['order_id'] );
				$route                       = 'thankyou';
				$redirect_params['order_id'] = $order['order_id'];
			} else if ( ! empty( $payment['payment_request_id'] ) ) {
				$result                            = ICD_Hosting::instance()->order_service->getPaymentRequestDetails( $payment['payment_request_id'] );
				$order                             = $result['order'];
				$redirect_params['order_id']       = $order['order_id'];
				$redirect_params['error']          = 'payment_error.payment_declined';
				$redirect_params['payment_method'] = $order['payment_method'] . ':' . $order['payment_processor_id'];
			} else if ( ! empty( $payment['order_id'] ) ) {
				$order                             = ICD_Hosting::instance()->order_service->getOrderDetails( $payment['order_id'] );
				$redirect_params['order_id']       = $order['order_id'];
				$redirect_params['payment_method'] = $order['payment_method'] . ':' . $order['payment_processor_id'];
				$redirect_params['error']          = 'payment_error.payment_declined';
			}

			$postback_response = ICD_Hosting::instance()->order_service->getPostBackResponse();
			if ( ! empty( $postback_response ) ) {
				echo $postback_response;
				exit;
			}

			if ( ! empty( $postback_response ) ) {
				echo $postback_response;
				exit;
			}

			echo "<script>location.href = '" . icd_hosting_url( $route, $redirect_params, true ) . "'</script>";
			exit;
		} catch ( AppException $e ) {
			if ( is_admin() ) {
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			}
			icd_hosting_notification_email( '[WORDPRESS_PAYMENT] ' . $e->getMessage(), $debug );
			icd_hosting_display_error( $e );
		} catch ( \Exception $e ) {
			icd_hosting_notification_email( '[WORDPRESS_PAYMENT] error.payment_postback', $debug . "\n\n" . $e );
			icd_hosting_display_error( 'error.payment_postback' );
		}
	}
}
