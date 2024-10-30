<?php

namespace ICD\Hosting;

use Exception;
use ICD\Hosting\Admin\ICD_Hosting_Admin_Settings;
use ICD\Hosting\Services\AppException;
use ICD\Hosting\Services\CatalogHelper;
use ICD\Hosting\Services\OrderHelper;
use ICD\Hosting\Services\Processors\ProcessorException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX Event Handler
 *
 * Class ICD_Hosting_AJAX
 */
class ICD_Hosting_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * AJAX handlers
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'domain_check'    => true,
			'order_create'    => true,
			'pay'             => true,
			'request_submit'  => true,
			'request_prices'  => true,
			'approver_emails' => true,
			'register_store'  => true,
			'process_payment' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_icd_hosting_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_icd_hosting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Check domain name availability
	 */
	public static function domain_check() {
		try {
			if ( ! wp_verify_nonce( $_GET['_domain_check_nonce'], 'domain-check' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$domain  = sanitize_text_field( wp_unslash( $_GET['domain'] ) );
			$sld_tld = icd_hosting_sld_tld( $domain );
			if ( ! $sld_tld ) {
				icd_hosting_error( array(
					'message' => 'invalid.domain',
					'field'   => 'domain',
					'subtype' => 'invalid'
				) );
			}

			$render_extra_attributes = ! empty( $_GET['render_extra_attributes'] ) ? true : false;
			$response                = array(
				'status' => true,
				'data'   => array( 'label' => icd_hosting_tr( 'domain_check.error' ) )
			);

			$commands = array(
				array(
					'command' => 'domaincheck',
					'params'  => array( 'tld' => $sld_tld['tld'], 'sld' => $sld_tld['sld'], 'extra_attributes' => 1 )
				),
			);

			if ( $render_extra_attributes ) {
				$commands[] = array( 'command' => 'countries' );
			}
			$results = ICD_Hosting()->getApp()->api->commands( $commands );

			if ( isset( $results[0]['data']['available'] ) ) {

				if ( $results[0]['data']['available'] ) {
					$response['data']['available']  = 'register';
					$response['data']['icann']      = $results[0]['data']['icann'];
					$response['data']['epp']        = $results[0]['data']['epp'];
					$response['data']['order_link'] = icd_hosting_url( 'hostingorder' ) . '?domain=' . $domain;
				} else if ( ! $results[0]['data']['available'] and $results[0]['data']['transfer'] ) {
					$response['data']['available'] = 'transfer';
					$response['data']['icann']     = $results[0]['data']['icann'];
					$response['data']['epp']       = $results[0]['data']['epp'];
				} else if ( ! $results[0]['data']['available'] and ! $results[0]['data']['transfer'] ) {
					$response['data']['available'] = 'unavailable';
				}
				if ( $render_extra_attributes and ! empty( $results[0]['data']['extra_attributes'] ) ) {
					$idx                                       = isset( $_GET['idx'] ) ? sanitize_text_field( wp_unslash( $_GET['idx'] ) ) : substr( sha1( uniqid( time() . wp_rand(), true ) ), 0, 8 );
					$prefix                                    = isset( $_GET['prefix'] ) ? sanitize_text_field( wp_unslash( $_GET['prefix'] ) ) : "order[items][$idx][domain][extra_attributes]";
					$countries                                 = OrderHelper::translateCountries( $results[1]['data']['countries'] );
					$setup                                     = isset( $_GET['setup'] ) ? sanitize_text_field( wp_unslash( $_GET['setup'] ) ) : "order";
					$response['data']['extra_attributes_html'] = icd_hosting_get_template_html( 'extra-attributes', [
						'idx'              => $idx,
						'prefix'           => $prefix,
						'extra_attributes' => $results[0]['data']['extra_attributes'],
						'countries'        => $countries,
						'action'           => $response['data']['available'],
						'tld'              => $sld_tld['tld'],
						'setup'            => $setup
					] );
				}
			}

			icd_hosting_json_out( $response );
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'error.domain_search_error' );
		}
	}

	/**
	 * Process order form
	 */
	public static function order_create() {

		try {
			if ( ! wp_verify_nonce( $_POST['_order_check_nonce'], 'order-create' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$input            = icd_hosting_sanitize_all( $_POST );
			$input['ip']      = $_SERVER['REMOTE_ADDR'];
			$input['referer'] = icd_hosting_url( 'hostingorder', empty( $input['order_id'] ) ? array() : array( 'order_id' => $input['order_id'] ), true );
			$input['referer'] = str_replace( '?order_id=', '', $input['referer'] );

			$create_order_result = ICD_Hosting()->order_service->saveOrder( $input );

			if ( ! $create_order_result ) {
				icd_hosting_error( 'error.order_create_failed' );
			}

			if ( ! empty( $create_order_result['status'] ) ) {
				extract( $create_order_result['data'] );

				if ( isset( $input['payment_method'] ) and $total > 0 ) {
					$processor = ICD_Hosting()->order_service->getPaymentProcessor( $input['payment_method'], $payment_request['id'], $payment_request['exchange_rate'] );
					if ( $processor->isOnline() && ! $processor->isOnsite() ) {
						$payment_data                        = $processor->paymentData( $order_id, $payment_request['amount'], $payment_request['currency'], $order );
						$create_order_result['payment_form'] = icd_hosting_get_template_html( 'payment_form', $payment_data );
					} else {
						$create_order_result['redirect_to'] = icd_hosting_url( 'payment', array(
							'order_id'       => $order_id,
							'payment_method' => $processor->paymentMethod( $input['payment_method'] )
						), true );
					}
				} else {
					$create_order_result['redirect_to'] = icd_hosting_url( 'thankyou', array( 'order_id' => $order_id ), true );
				}
				unset( $create_order_result['data'] );
			}

			icd_hosting_json_out( $create_order_result );
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'error.order_create_failed' );
		}
	}

	/**
	 *  Check item prices
	 */
	public static function request_prices() {
		try {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'payment-request-check' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$input                            = icd_hosting_sanitize_all( $_POST );
			$icd_hosting_app                  = ICD_Hosting()->getApp();
			$input['order']['payment_method'] = isset( $input['order']['payment_method'] ) ?
				$icd_hosting_app->payment->get( $input['order']['payment_method'] )->paymentMethod( $input['order']['payment_method'] ) : 'Pending';

			$request          = isset( $input['request'] ) ? [ $input['request'] ] : null;
			$validate_item_id = isset( $input['validate_item_id'] ) ? $input['validate_item_id'] : '';
			$commands         = array(
				array( 'command' => 'store' ),
				array( 'command' => 'countries' ),
				array( 'command' => 'tlds' ),
				array(
					'command' => 'order-request-build',
					'params'  => array(
						'order'       => $input['order'],
						'request'     => $request,
						'get_catalog' => 1,
						'options'     => [ 'validate_item_id' => $validate_item_id ]
					)
				),
			);
			$results          = $icd_hosting_app->api->commands( $commands, 'POST' );
			foreach ( $results as $result ) {
				if ( ! $result['status'] ) {
					icd_hosting_json_out( $result );
				}
			}

			$catalog_helper             = new CatalogHelper( $results[3]['data']['catalog'], $results[3]['data']['products'] );
			$viewdata                   = array(
				'request_id'     => $results[3]['data']['order']['request_id'],
				'store'          => $results[0]['data']['store'],
				'countries'      => OrderHelper::translateCountries( $results[1]['data']['countries'] ),
				'order'          => $results[3]['data']['order'],
				'catalog'        => $results[3]['data']['catalog'],
				'products'       => $results[3]['data']['products'],
				'catalog_helper' => $catalog_helper,
				'processors'     => ICD_Hosting()->order_service->getPaymentProcessors(),
				'tld_info'       => $results[2]['data']['tlds'],
				'terms'          => empty( $input['terms'] ) ? 0 : 1,
				'icann'          => empty( $input['icann'] ) ? 0 : 1,
			);
			$viewdata['payment_method'] = $icd_hosting_app->payment->orderProcessorId( $viewdata['order']['payment_method'] );

			$viewdata = ICD_Hosting()->order_service->requestCommonViewData( $viewdata );

			$viewdata['ajax_url']            = icd_hosting_admin_ajax_url();
			$viewdata['request_prices']      = 'icd_hosting_request_prices';
			$viewdata['domain_check']        = 'icd_hosting_domain_check';
			$viewdata['approver_emails']     = 'icd_hosting_approver_emails';
			$viewdata['request_submit']      = 'icd_hosting_request_submit';
			$viewdata['price_change_action'] = 'icd_hosting_request_prices';

			$data = [ 'html' => icd_hosting_get_template_html( 'request', $viewdata ) ];
			icd_hosting_json_out( $data );
			wp_die();
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'error.order_request_details' );
		}
	}

	/**
	 * Process Payment request form submission
	 */
	public static function request_submit() {
		try {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'payment-request-check' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$order            = icd_hosting_sanitize_all( $_POST['order'] );
			$processor_id     = isset( $order['payment_method'] ) ? $order['payment_method'] : null;
			$order['ip']      = $_SERVER['REMOTE_ADDR'];
			$order['referer'] = icd_hosting_url( 'request', [ 'request_id' => $order['request_id'] ], true );
			$order['referer'] = str_replace( '?request_id=', '', $order['referer'] );
			$icd_hosting_app  = ICD_Hosting()->getApp();

			$order['payment_method'] = isset( $order['payment_method'] ) ?
				$icd_hosting_app->payment->get( $order['payment_method'] )->paymentMethod( $order['payment_method'] ) : 'Pending';

			// validate terms and conditions
			if ( ! $_POST['terms'] ) {
				AppException::error( array(
					'message' => 'required.you_must_agree_with_terms_of_use',
					'field'   => 'terms',
					'subtype' => 'required'
				) );
			}

			$tlds = $icd_hosting_app->api->get( 'tlds' );
			foreach ( $order['items'] as $id => $item ) {
				if ( empty( $item['checked'] ) or $item['checked'] != 1 ) {
					unset( $order['items'][ $id ] );
				} else if (
					$item['action'] == 'order' and ! empty( $item['domain'] ) and
					                               ! empty( $tlds['data']['tlds'][ $item['domain']['tld'] ]['icann'] ) and ! $_POST['icann']
				) {
					AppException::error( array(
						'message' => 'required.you_must_agree_with_icann_terms',
						'field'   => 'icann',
						'subtype' => 'required'
					) );
					break;
				}
			}

			if ( AppException::hasErrors() ) {
				throw new AppException;
			}
			//$create_order_result = OrderHelper::saveOrder($input);
			$create_order_result = $icd_hosting_app->api->post( 'order-create', $order );
			if ( ! $create_order_result ) {
				icd_hosting_error( 'error.order_create_failed' );
			}

			if ( ! empty( $create_order_result['status'] ) ) {
				extract( $create_order_result['data'] );

				if ( $processor_id and $total > 0 ) {
					$processor = ICD_Hosting()->order_service->getPaymentProcessor( $processor_id, $payment_request['id'], $payment_request['exchange_rate'] ); //TODO: check rate
					if ( $processor->isOnline() && ! $processor->isOnsite() ) {
						$payment_data                        = $processor->paymentData( $order_id, $payment_request['amount'], $payment_request['currency'], $order );
						$create_order_result['payment_form'] = icd_hosting_get_template_html( 'payment_form', $payment_data );
					} else {
						$create_order_result['redirect_to'] = icd_hosting_url( 'payment', array(
							'order_id'       => $order_id,
							'payment_method' => $processor->paymentMethod( $order['payment_method'] )
						), true );
					}
				} else {
					$create_order_result['redirect_to'] = icd_hosting_url( 'thankyou', array( 'order_id' => $order_id ), true );
				}
				unset( $create_order_result['data'] );
			}

			icd_hosting_json_out( $create_order_result );
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( \Exception $e ) {
			icd_hosting_ajax_error( 'error.order_create_failed:' . $e->getMessage() );
		}
	}

	/**
	 * Check SSL Certificate approval email
	 */
	public static function approver_emails() {
		try {
			if ( ! wp_verify_nonce( $_GET['_approver_email_nonce'], 'approver-emails' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$hostname = icd_hosting_sanitize_all( $_GET['hostname'] );
			$type     = icd_hosting_sanitize_all( $_GET['type'] );

			$result = ICD_Hosting()->getApp()->api->get( 'ssl-approver-emails', array(
				'hostname' => $hostname,
				'type'     => $type
			) );

			if ( empty( $result['data'] ) ) {
				throw new Exception;
			}

			icd_hosting_json_out( array( 'data' => $result['data'] ) );
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'error.getting_approver_email' );
		}
	}

	/**
	 * Mark payment as done
	 */
	public static function pay() {
		try {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'payment-check' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
			}

			$input                  = icd_hosting_sanitize_all( $_POST );
			$order                  = ICD_Hosting()->order_service->getOrderDetails( $input['order_id'] );
			$payment_request_params = ICD_Hosting()->order_service->getPaymentRequestParams( $order, $input );

			$payment_request = ICD_Hosting()->order_service->createPaymentRequest( $payment_request_params );
			$payment_request = $payment_request['data']['payment_request'];

			if ( ! empty( $payment_request['token_id'] ) ) {
				icd_hosting_json_out( [ 'payment_request' => $payment_request ] );
			}

			$processor = ICD_Hosting()->order_service->getPaymentProcessor( $input['payment_method'], $payment_request['id'], $payment_request['exchange_rate'] );

			if ( $processor->requirePaymentToken() ) {
				try {

					if ( ! empty( $input['session'] ) ) {
						$session = $processor->createPaymentSession( array_replace( $order, [
							'amount'             => $payment_request['amount'],
							'currency'           => $payment_request['currency'],
							'payment_request_id' => $payment_request['id'],
						] ) );

						icd_hosting_json_out( [ 'session' => $session ] );
					} else if ( empty( $payment_request['token_id'] ) ) {
						$token                       = $processor->createPaymentToken( array_replace( $order, [
							'amount'             => $payment_request['amount'],
							'currency'           => $payment_request['currency'],
							'payment_request_id' => $payment_request['id'],
						] ) );
						$payment_request['token_id'] = $token['token_id'];
						icd_hosting_json_out( [ 'order' => $order, 'payment_request' => $payment_request ] );
					}

					return;
				} catch ( \Exception $e ) {
					icd_hosting_ajax_error( $e->getMessage() );
				}
			}
			else if ( $processor->isOnsite() ) {
				// Processors with on-site checkout
				try {
					$payment = $processor->makePayment(
						$order['order_id'],
						$payment_request['amount'],
						$payment_request['currency'],
						$order,
						$input
					);

					// Add payment request ID.
					$payment['payment_request_id'] = $payment_request['id'];

					// We can now call 'payment-add' directly.
					$result = ICD_Hosting()->order_service->addPayment( $payment );
					icd_hosting_json_out( array( 'redirect_to' => icd_hosting_url( 'thankyou', array( 'order_id' => $order['order_id'] ), true ) ) );
				} catch ( \Exception $e ) {
					icd_hosting_ajax_error( $e->getMessage() );
				}
			}
			else {
				// Processors with hosted checkout
				$payment_data = $processor->paymentData(
					$order['order_id'],
					$payment_request['amount'],
					$payment_request['currency'],
					$order
				);

				$viewdata['payment_form'] = icd_hosting_get_template_html( 'payment_form', $payment_data );

				icd_hosting_json_out( $viewdata );
			}
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( ProcessorException $e ) {
			icd_hosting_notification_email( "Payment processor error [{$e->getProcessor()}]", $e->getMessage() );

			icd_hosting_ajax_error( 'payment_error.payment_declined' );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'payment_error.payment_declined' );
		}
	}

	/**
	 * Register new reseller account with store
	 */
	public static function register_store() {
		try {
			$input = icd_hosting_sanitize_all( $_POST );

			if ( ! wp_verify_nonce( $input['_wpnonce'], 'icd-hosting-register' ) ) {
				icd_hosting_error( 'error.bad_nonce' );
				wp_die();
			}

			$data = [
				'username'       => sanitize_text_field( wp_unslash( $input['username'] ) ),
				'password'       => sanitize_text_field( wp_unslash( $input['password'] ) ),
				'full_name'      => sanitize_text_field( wp_unslash( $input['full_name'] ) ),
				'email'          => sanitize_text_field( wp_unslash( $input['email'] ) ),
				'store_name'     => sanitize_text_field( wp_unslash( $input['store_name'] ) ),
				'store_title'    => sanitize_text_field( wp_unslash( $input['store_title'] ) ),
				'store_currency' => sanitize_text_field( wp_unslash( $input['store_currency'] ) ),
				'terms'          => sanitize_text_field( wp_unslash( $input['terms'] ) ),
				'news_subscribe' => sanitize_text_field( wp_unslash( $input['news_subscribe'] ) ),
			];

			$register_store_result = ICD_Hosting()->registration_service->registerStore( $data );

			if ( ! empty( $register_store_result['data']['api_key_request'] ) ) {
				set_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND, $register_store_result['data']['api_key_request'], 24 * HOUR_IN_SECONDS );
				icd_hosting_json_out( [ 'redirect_to' => esc_url_raw( admin_url( 'admin.php?page=icd-hosting-settings' ) ) ] );
				exit;
			}

			icd_hosting_json_out( $register_store_result );
		} catch ( AppException $e ) {
			icd_hosting_ajax_error( $e );
		} catch ( Exception $e ) {
			icd_hosting_ajax_error( 'error.register_store_failed' );
		}
	}

	/**
	 * Payment Process Webhook
	 * @return void
	 */
	public static function process_payment() {
		$input = icd_hosting_sanitize_all( $_POST );

		ICD_Hosting()->getApp()->api->post( 'payment-process-webhook', [
			'processor_id' => $input['processor_id'],
			'data'         => $input['data']
		] );

		icd_hosting_json_out( [ 'redirect_to' => esc_url_raw( icd_hosting_url( 'thankyou', [ 'order_id' => $input['order_id'] ], true ) ) ] );
	}


}

ICD_Hosting_AJAX::init();
