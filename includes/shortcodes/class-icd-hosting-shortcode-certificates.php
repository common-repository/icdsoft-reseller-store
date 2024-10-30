<?php

namespace ICD\Hosting\Shortcodes;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\Services\AppException;
use ICD\Hosting\Services\OrderHelper;
use ICD\Hosting\Services\OrderService;

class ICD_Hosting_Shortcode_Certificates {

	/**
	 * Output SSL Order page
	 *
	 */
	public static function output() {
		global $wp;

		$product  = empty( $_GET['product'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['product'] ) );
		$order_id = empty( $wp->query_vars['order_id'] ) ? '' : sanitize_text_field( wp_unslash( $wp->query_vars['order_id'] ) );

		try {
			$viewdata = OrderHelper::domainsCertificatesInit( 'ssl', 'standalone' );

			$formdata = [
				'default_product' => $viewdata['default_product'],
				'contact'         => ICD_Hosting::instance()->order_service->getOrderContacts(),
				'ssl'             => [
					'ip_type' => null,
					'organization' => null,
					'organization_unit' => null,
					'country' => null,
					'state' => null,
					'city' => null,
					'address' => null,
					'zip' => null,
					'email' => null,
					'approver_email' => null,
					'common_name' => null,
				]
			];

			if ( ! empty( $product ) ) {
				$viewdata['default_product'] = $product;
			}

			// Existing order
			if ( $order_id ) {

				$order = ICD_Hosting::instance()->order_service->getOrderDetails( $order_id );

				if ( $order['paid'] == OrderService::PARTIALLY_PAID ) {
					wp_redirect( icd_hosting_url( 'payment', array( 'order_id' => $order_id ), true ) );
				}
				if ( $order['paid'] == OrderService::FULLY_PAID ) {
					wp_redirect( icd_hosting_url( 'thankyou', array( 'order_id' => $order_id ), true ) );
				}

				// OrderStatusEnum::NOT_PAID
				$formdata                    = ICD_Hosting::instance()->order_service->prefillFormData( $order, $formdata );
				$viewdata['default_product'] = $formdata['default_product'];
				$viewdata['payment_method']  = ICD_Hosting::instance()->order_service->getOrderProcessorId( $order['payment_method'] );
			}

			$formdata['order_create']    = 'icd_hosting_order_create';
			$viewdata['formdata']        = array_merge( $viewdata['formdata'], $formdata );
			$viewdata['approver_emails']     = 'icd_hosting_approver_emails';
			$viewdata['ajax_url']     = icd_hosting_admin_ajax_url();

			icd_hosting_get_template( 'certificates', $viewdata );
		} catch ( AppException $e ) {
			if ( ! is_admin() ) {
				icd_hosting_display_error( $e );
			}
		} catch ( \Exception $e ) {
			icd_hosting_display_error( 'error.failed_to_retrieve_order_data' );
		}
	}
}
