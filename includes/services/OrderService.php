<?php

namespace ICD\Hosting\Services;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;

/**
 * Class OrderService
 * @package ICD\Hosting\Services
 */
class OrderService extends Service {
	/**
	 * Order status when partial payment is made
	 */
	const PARTIALLY_PAID = OrderStatusEnum::PARTIALLY_PAID;
	/**
	 * Order status when full payment is made
	 */
	const FULLY_PAID = OrderStatusEnum::FULLY_PAID;

	/**
	 * Order status when not paid
	 */
	const NOT_PAID = OrderStatusEnum::NOT_PAID;

	/**
	 * Handle payment object
	 *
	 * @var Payment
	 */
	protected $payment;

	/**
	 * OrderService constructor.
	 *
	 * @param $api
	 * @param $payment
	 */
	public function __construct( $api, $payment ) {
		parent::__construct( $api );

		$this->payment = is_array( $payment ) ? $this->getPaymentObject( $payment ) : $payment;
	}

	/**
	 * Handle payment methods upon settings in config
	 *
	 * @see ICD_Hosting_Config
	 *
	 * @param $paymentSettings
	 *
	 * @return Payment
	 */
	private function getPaymentObject( $paymentSettings ) {
		$settings   = array_merge( $paymentSettings, array( 'processors' => array() ) );
		$processors = $this->api->get( 'payment-processors', array( 'test' => (int) $paymentSettings['test_mode'] ) );
		if ( ! empty( $processors['data']['processors'] ) ) {
			foreach ( $processors['data']['processors'] as $p ) {
				$settings['processors'][ $p['processor_id'] ] = $p;
			}
		}

		return new Payment( $settings );
	}

	/**
	 * Get order data needed for views
	 *
	 * @return array
	 * @throws AppException
	 */
	public function getOrderViewData() {
		$result = $this->getOrderData();

		if ( empty( $result['data'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}

		OrderHelper::init( $result['data'] );
		$preselected_tlds = ICD_Hosting::instance()->domain_service->preselectedTLDs();

		$viewdata = array(
			'store'            => $result['data']['store'],
			'catalog'          => OrderHelper::prepareCatalog(['actions' => ['order', 'renewal']]),
			'groups'           => OrderHelper::groupPlans(['has_order_price' => 1]),
			'group_resources'  => OrderHelper::groupResources(),
			'resources'        => OrderHelper::translateResources(),
			'tlds'             => OrderHelper::offeredTLDs(),
			'datacenters'      => OrderHelper::offeredDatacenters(),
			'countries'        => OrderHelper::translateCountries(),
			'processors'       => $this->getPaymentProcessors(),
			'preselected_tlds' => $preselected_tlds,
		);

		$viewdata['payment_method'] = key( $viewdata['processors'] );

		return $viewdata;
	}

	/**
	 * Get data needed for forms
	 *
	 * @param $domain
	 *
	 * @return array
	 * @throws AppException
	 */
	public function getFormData( $domain ) {
		$formdata = array(
			'domain'     => $domain,
			'domain_item_id' => 1,
			'new_domain' => 1,
			'product_id' => '',
			'terms'      => 1,
		);

		$viewdata = $this->getOrderViewData();

		$formdata['tld']      = ! empty( $formdata['domain'] ) ? icd_hosting_sld_tld( $formdata['domain'] )['tld'] : '';
		$formdata['location'] = key( $viewdata['datacenters'] );
		$formdata['plan']     = key( $viewdata['catalog'][ $formdata['location'] ] );
		$formdata['period']   = isset( $viewdata['catalog'][ $formdata['location'] ][ $formdata['plan'] ]['order'] ) ? key( $viewdata['catalog'][ $formdata['location'] ][ $formdata['plan'] ]['order'] ) : 1;
		$formdata['contact']  = $this->getOrderContacts();

		return $formdata;
	}

	/**
	 * Prefill form data
	 *
	 * @param $order
	 * @param $formdata
	 *
	 * @return mixed
	 */
	public function prefillFormData( $order, $formdata ) {
		foreach ( $formdata['contact'] as $key => $val ) {
			if ( isset( $order[ $key ] ) ) {
				$formdata['contact'][ $key ] = $order[ $key ];
			}
		}

		$formdata['order_id'] = $order['order_id'];
		$formdata['icann']    = 1;
		$formdata['terms']    = 1;

		// Prefill product data, we are limited here to hosting(+domain)? catalog only
		$hosting_product_id = null;
		foreach ( $order['items'] as $key => $item ) {
			if ( in_array( $item['product_type'], [ 'hosting', 'server' ] ) && empty( $hosting_product_id ) ) {
				$hosting_product_id          = $key;
				$sld_tld                     = icd_hosting_sld_tld( $item['hosting']['hostname'] );
				$formdata['plan']            = $item['product'];
				$formdata['location']        = $item['datacenter'];
				$formdata['period']          = $item['period'];
				$formdata['domain']          = $item['hosting']['hostname'];
				$formdata['tld']             = $sld_tld['tld'];
				$formdata['hosting_item_id'] = $key;
			}

			if ( $item['product_type'] == 'domain' ) {
				if ( ! empty( $item['domain']['extra_attributes'] ) ) {
					$formdata['extra_attributes'] = $item['domain']['extra_attributes'];
				}
			}

			if ($item['product_type'] == 'ssl') {
				$formdata['period'] = $item['period'];
				$formdata['default_product'] = $item['product'];
				$formdata['ssl_item_id'] = $key;
				$formdata['ssl']['common_name'] = $item['ssl']['common_name'];
				$formdata['ssl']['approver_email'] = $item['ssl']['approver_email'];
				$formdata['ssl']['organization'] = $item['ssl']['organization'];
				$formdata['ssl']['organization_unit'] = $item['ssl']['organization_unit'];
				$formdata['ssl']['country'] = $item['ssl']['country'];
				$formdata['ssl']['city'] = $item['ssl']['city'];
				$formdata['ssl']['state'] = $item['ssl']['state'];
				$formdata['ssl']['email'] = $item['ssl']['email'];
				$formdata['ssl']['address'] = $item['ssl']['address'];
				$formdata['ssl']['zip'] = $item['ssl']['zip'];
			}
		}

		$formdata['new_domain'] = 0;
		foreach ( $order['items'] as $key => $item ) {
			if ( $item['product_type'] == 'domain' && $item['parent'] == $hosting_product_id ) {
				$formdata['new_domain']     = 1;
				$formdata['domain_item_id'] = $key;
			}
		}

		return $formdata;
	}

	/**
	 * Get order contacts
	 *
	 * @return array
	 */
	public function getOrderContacts() {
		return OrderDTO::getOrderContacts();
	}

	/**
	 * Get order details
	 *
	 * @param $order_id
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public function getOrderDetails( $order_id ) {
		return OrderHelper::getOrderDetails( $order_id );
	}

	/**
	 * Get payment request details
	 *
	 * @param $request_id
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public function getPaymentRequestDetails( $request_id ) {
		return OrderHelper::getPaymentRequestDetails( $request_id );
	}

	/**
	 * Get order payment processor ID
	 *
	 * @param $payment_method
	 *
	 * @return mixed
	 */
	public function getOrderProcessorId( $payment_method ) {
		return $this->payment->orderProcessorId( $payment_method );
	}

	/**
	 * Save order
	 *
	 * @param $input
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public function saveOrder( $input ) {
		return OrderHelper::saveOrder( $input );
	}

	/**
	 * Get payment processor
	 *
	 * @param null $payment_method
	 * @param null $payment_request_id
	 * @param bool $exchange_rate
	 *
	 * @return |null
	 */
	public function getPaymentProcessor( $payment_method = null, $payment_request_id = null, $exchange_rate = false ) {
		$processor = $this->payment->get( $payment_method, $payment_request_id );
		if ( $exchange_rate ) {
			$processor->setExchangeRate( $exchange_rate );
		}

		return $processor;
	}

	/**
	 * Format order
	 *
	 * @param $order
	 *
	 * @return mixed
	 */
	public function formatOrder( $order ) {
		return OrderHelper::formatOrder( $order );
	}

	/**
	 * Get all payment processors
	 *
	 * @param bool $full
	 *
	 * @return array
	 */
	public function getPaymentProcessors( $full = false ) {
		return $this->payment->processors( $full );
	}

	/**
	 * Get total payment for order
	 *
	 * @param $order
	 *
	 * @return float|int
	 */
	public function getPaymentsTotal( $order ) {
		return OrderHelper::paymentsTotal( $order );
	}

	/**
	 * Save payment request
	 *
	 * @param $params
	 *
	 * @return array|bool|mixed|object|string
	 */
	public function createPaymentRequest( $params ) {
		return $this->api->get( 'payment-request-create', $params );
	}

	/**
	 * Add payment
	 *
	 * @param $payment
	 *
	 * @return array|bool|mixed|object|string
	 */
	public function addPayment( $payment ) {
		return $this->api->post( 'payment-add', $payment );
	}

	/**
	 * Detect payment
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function detectPayment( $input ) {
		return $this->payment->detectPayment( $input );
	}

	/**
	 * Get payment postback response
	 *
	 * @return mixed
	 */
	public function getPostBackResponse() {
		return $this->payment->getPostBackResponse();
	}

	/**
	 * Get payment request params
	 *
	 * @param $order
	 * @param $input
	 *
	 * @return array
	 */
	public function getPaymentRequestParams( $order, $input ) {
		$paid           = $this->getPaymentsTotal( $order );
		$payment_method = $this->getPaymentProcessor( $input['payment_method'] )->paymentMethod( $input['payment_method'] );

		/* $order = OrderHelper::getOrderDetails($order_id); */
		/* $paid  = OrderHelper::paymentsTotal($order); */
		/* $payment_method = $this->app->payment->get($input['payment_method'])->paymentMethod($input['payment_method']); */

		$payment_request_params = array(
			'order_id'       => $order['order_id'],
			'amount'         => bcsub( (string) $order['total'], (string) $paid, 2 ),
			'currency'       => $order['currency'],
			'payment_method' => $payment_method
		);

		return icd_hosting_sanitize_all( $payment_request_params );
	}

	/**
	 * Get hosting plan information
	 *
	 * @param $location
	 * @param $plan
	 * @param null $order_data
	 *
	 * @return array
	 * @throws AppException
	 */
	public function getPlanInfo( $location, $plan, $order_data = null ) {
		if ( ! $order_data ) {
			$order_data = $this->getOrderViewData();
		}

		$product_id = isset( $order_data['catalog'][ $location ][ $plan ]['product_id'] ) ? $order_data['catalog'][ $location ][ $plan ]['product_id'] : '';
		if ( empty( $product_id ) ) {
			return [];
		}
		$resources  = $order_data['resources'][ $product_id ];

		$resources_names   = array( 'storage', 'traffic', 'subdomain', 'mysql_db', 'domain_parking', 'ftp_account' );
		$resources_to_show = array_filter( $resources, function ( $resource ) use ( $resources_names ) {
			return in_array( $resource['name'], $resources_names );
		} );

		return array(
			'dc'            => $location,
			'plan'          => $plan,
			'dc_name'       => $order_data['datacenters'][ $location ]['name'],
			'plan_name'     => $order_data['catalog'][ $location ][ $plan ]['name'],
			'price'         => $order_data['catalog'][ $location ][ $plan ]['order'],
			'periodicity'   => $order_data['catalog'][ $location ][ $plan ]['periodicity'],
			'renewal_price' => isset( $order_data['catalog'][ $location ][ $plan ]['renewal'] ) ? $order_data['catalog'][ $location ][ $plan ]['renewal'] : [],
			'resources'     => $resources_to_show,
			'currency'      => $order_data['store']['currency']
		);
	}

	/**
	 * Get common data for payment request views
	 *
	 * @param $viewdata
	 *
	 * @return mixed
	 */
	public function requestCommonViewData( &$viewdata ) {
		$viewdata['tlds'] = array();
		foreach ( $viewdata['catalog'] as $catalog_id => $item ) {
			$product = $viewdata['products'][ $item['product_id'] ];
			if ( $product['type'] == 'domain' and ! empty( $item['prices']['order'][1]['active'] ) ) {
				$tld = ( $pos = strpos( $product['product'], ':' ) ) ? substr( $product['product'], 0, $pos ) : $product['product'];
				$viewdata['tlds'][ $item['parent_id'] ][ $catalog_id ] = array(
					'tld' => $tld,
					'price' => current( $item['prices']['order'] ),
					'epp' => (int) $viewdata['tld_info'][ $tld ]['epp'],
					'icann' => (int) $viewdata['tld_info'][ $tld ]['icann'],
				);
			}
		}

		$viewdata['preselected_tlds'] = ICD_Hosting::instance()->domain_service->preselectedTLDs();

		$viewdata['icann_show'] = 0;
		foreach ( $viewdata['order']['items'] as $i ) {
			if (
				$i['checked'] and $i['action'] == 'order' and $i['product_type'] == 'domain' and ! empty( $viewdata['tld_info'][ $i['domain']['tld'] ]['icann'] )
			) {
				$viewdata['icann_show'] = 1;
				break;
			}
		}

		return $viewdata;
	}

	/**
	 * Parse shortcode parameters to datacenters
	 *
	 * @param $location
	 *
	 * @return string
	 */
	public function parseLocationParams( $location ) {
		switch ( $location ) {
			case 'us':
				$location = 'centurylink';
				break;
			case 'eu':
				$location = 'neterra';
				break;
			case 'hk':
				$location = 'iadvantage';
				break;
		}

		return $location;
	}

}
