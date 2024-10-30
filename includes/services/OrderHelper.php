<?php

namespace ICD\Hosting\Services;

use ICD\Hosting\ICD_Hosting;

/**
 * Class OrderHelper
 * @package ICD\Hosting\Services
 */
class OrderHelper {

	/**
	 * Stores order data
	 *
	 * @var array
	 */
	protected static $orderData;

	/**
	 * @var CatalogHelper
	 */
	protected static $catalogHelper;

	/**
	 * Handle catalog by location
	 */
	protected static $byLocation;

	/**
	 * @var
	 */
	public static $order;

	/**
	 * @param $orderData
	 */
	public static function init( $orderData ) {
		self::$orderData     = $orderData;
		self::$catalogHelper = new CatalogHelper( $orderData['catalog'], $orderData['products'] );
	}

	/**
	 * Filter catalog by location
	 *
	 * @param array $filter
	 *
	 * @return mixed
	 */
	public static function prepareCatalog( $filter = [] ) {
		if ( self::$byLocation ) {
			return self::$byLocation;
		}

		$catalog          = self::$catalogHelper->filterCatalog( $filter );
		self::$byLocation = self::$catalogHelper->catalogByLocation( $catalog );

		foreach ( self::$byLocation as $location => $items ) {
			foreach ( $items as $product => $details ) {
				if ( ! isset( $details['order'] ) ) {
					unset( self::$byLocation[ $location ][ $product ] );
				}
			}
		}

		return self::$byLocation;
	}

	/**
	 * Group for hosting plans
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public static function groupPlans( $options = [] ) {
		$groups = [];
		foreach ( self::$byLocation as $location => $items ) {
			foreach ( $items as $product => $details ) {
				if ( ! empty( $options['has_order_price'] ) and empty( $details['order'] ) ) {
					continue;
				}

				if ( $details['product_type'] == 'server' ) {
					$groups[ $location ]['server'][ $product ] = $product;
				} else if ( $details['product_type'] == 'hosting' ) {
					if ( strpos( $product, 'firstclass' ) !== false ) {
						$groups[ $location ]['vps'][ $product ] = $product;
					} else if ( strpos( $product, 'sureapp' ) !== false ) {
						$groups[ $location ]['app'][ $product ] = $product;
					} else {
						$groups[ $location ]['shared'][ $product ] = $product;
					}
				}
			}
		}

		return $groups;
	}

	/**
	 * Hosting resources
	 *
	 * @param bool $extended
	 *
	 * @return array
	 */
	public static function groupResources( $extended = false ) {
		return [
			'shared' => [ 'storage', 'traffic' ],
			'app'    => [ 'dedicated_ram', 'storage', 'traffic' ],
			'vps'    => [ 'dedicated_cpu', 'dedicated_ram', 'storage', 'traffic' ],
			'server' => [ 'dedicated_cpu', 'dedicated_ram', 'storage', 'traffic' ],
		];
	}
	/**
	 * Translate product resources
	 *
	 * @return array
	 */
	public static function translateResources() {
		$resources = array();
		foreach ( self::$orderData['products'] as $key => $val ) {
			if ( ! empty( $val['resources'] ) ) {
				foreach ( $val['resources'] as $rid => $rd ) {
					$val['resources'][ $rid ]['label'] = icd_hosting_tr( 'resources.' . $rd['name'] );
					$val['resources'][ $rid ]['label_unit'] = $rd['unit'] == 'COUNT' ? '' : icd_hosting_tr( 'units.' . $rd['unit'] );
				}
				$resources[ $key ] = $val['resources'];
			}
		}

		return icd_hosting_sanitize_all( $resources );
	}

	/**
	 * Translate countries
	 *
	 * @param null $countries
	 *
	 * @return |null
	 */
	public static function translateCountries( $countries = null ) {
		if ( is_null( $countries ) ) {
			$countries = self::$orderData['countries'];
		}

		$translated = ICD_Hosting::instance()->getApp()->trans->find_key( 'countries' );
		if ( ! $translated ) {
			return $countries;
		}

		foreach ( $translated as $k => $v ) {
			$translated[ $k ] = array_merge( $countries[ $k ], [ 'country' => $v ] );
		}

		return $translated;
	}

	/**
	 * Get offered top level domains
	 *
	 * @return array
	 */
	public static function offeredTLDs() {
		$tlds                = array();
		$catalog_by_location = self::prepareCatalog(['actions' => ['order', 'renewal']]);
		foreach ( $catalog_by_location as $location => $items ) {
			foreach ( $items as $product => $details ) {
				if ( ! empty( $details['bonus:domain'] ) ) {
					$tlds = array_merge( $tlds, array_flip( array_keys( $details['bonus:domain'] ) ) );
				}
			}
		}

		return array_intersect_key( self::$orderData['tlds'], $tlds );
	}

	/**
	 * Get offered datacenters
	 * @return array
	 */
	public static function offeredDatacenters() {
		$result = array();
		foreach ( array_keys( self::prepareCatalog() ) as $dc ) {
			$result[ $dc ] = self::$orderData['datacenters'][ $dc ];
		}

		if ( isset( self::$orderData['datacenters'] ) ) {
			return array_intersect_key( $result, self::$orderData['datacenters'] );
		}

		return $result;
	}

	/**
	 * Format order
	 *
	 * @param $order
	 *
	 * @return mixed
	 */
	public static function formatOrder( $order ) {
		// Build Order items tree
		$order['total_paid'] = $order['payments_total'];
		$order['total_due'] = $order['payment_due'];

		return $order;
	}

	/**
	 * Sum total payment for order
	 *
	 * @param $order
	 *
	 * @return float|int
	 */
	public static function paymentsTotal( $order ) {
		return $order['payments_total'];
	}

	/**
	 * Create order
	 *
	 * @param $input
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public static function saveOrder( $input ) {
		if ( ! empty( $input['order_id'] ) ) {
			$order = self::getOrderDetails( $input['order_id'] );
			if ( $order['status'] == OrderStatusEnum::ORDER_NEW ) {
				return ICD_Hosting::instance()->getApp()->api->post( 'order-update', self::buildOrder( $input ) );
			}
		}

		return ICD_Hosting::instance()->getApp()->api->post( 'order-create', self::buildOrder( $input ) );
	}

	/**
	 * Create SSL order
	 *
	 * @param $input
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public static function buildSSLOrder($input) {
		// Get catalog & store data
		$order_data = ICD_Hosting::instance()->getApp()->api->get('order-data', ['flat' => 1]);

		if (empty($order_data['data'])) {
			error('error.failed_to_retrieve_order_data');
		}

		$order_data = $order_data['data'];

		self::init($order_data);

		// Get order & order items models
		self::$order = OrderDTO::getOrder();

		if (!empty($input['order_id'])) {
			self::$order['order_id'] = $input['order_id'];
		}

		$ssl_dto = OrderDTO::getSSLItem();

		if (empty($input['terms']))
			AppException::error(array('message' => 'required.you_must_agree_with_terms_of_use', 'field' => 'terms', 'subtype' => 'required'));

		if (AppException::hasErrors())
			throw new AppException;

		// Fill order main/meta data
		self::$order['currency'] = $order_data['store']['currency'];
		self::$order['ip'] = $input['ip'];
		self::$order['referer'] = $input['referer'];

		self::$order['payment_method'] = isset($input['payment_method']) ?
			ICD_Hosting::instance()->getApp()->payment->get($input['payment_method'])->paymentMethod($input['payment_method']) : 'Pending';

		self::$order['user_agent']          = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		self::$order['user_browser_locale'] = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';

		// Fill contact data where applicable
		foreach ($input['contact'] as $key => $val) {
			if (array_key_exists($key, self::$order))
				self::$order[$key] = $val;
		}

		// Fill ssl product props
		$ssl_dto['type'] = 'ssl';
		$ssl_dto['product_type'] = 'ssl';
		$ssl_dto['name'] = $order_data['catalog'][ $input['ssl']['catalog_id'] ]['name'];
		$ssl_dto['price'] = $order_data['catalog'][ $input['ssl']['catalog_id'] ]['prices']['order'][ $input['ssl']['period'] ];
		$ssl_dto['catalog_id'] = $input['ssl']['catalog_id'];
		$ssl_dto['action'] = 'order';
		$ssl_dto['period'] = $input['ssl']['period'];

		$ssl_dto['ssl.common_name'] = icd_hosting_dot_path($input, 'ssl.ssl.common_name' );
		$ssl_dto['ssl.approver_email'] = icd_hosting_dot_path( $input, 'ssl.ssl.approver_email' );
		$ssl_dto['ssl.zip'] = icd_hosting_dot_path( $input, 'ssl.ssl.zip');
		$ssl_dto['ssl.city'] = icd_hosting_dot_path( $input,'ssl.ssl.city' );
		$ssl_dto['ssl.state'] = icd_hosting_dot_path( $input,'ssl.ssl.state' );
		$ssl_dto['ssl.address'] = icd_hosting_dot_path($input, 'ssl.ssl.address' );
		$ssl_dto['ssl.country'] = icd_hosting_dot_path($input, 'ssl.ssl.country' );
		$ssl_dto['ssl.organization_unit'] = icd_hosting_dot_path($input, 'ssl.ssl.organization_unit' );
		$ssl_dto['ssl.organization'] = icd_hosting_dot_path($input, 'ssl.ssl.organization' );
		$ssl_dto['ssl.ip_type'] = 'noip';

		//create new or update existing hosting item
		$ssl_item_id = empty($input['ssl_item_id']) ? 0 : $input['ssl_item_id'];
		if ($ssl_item_id !== 0)
			$ssl_dto['item_id'] = $ssl_item_id;

		self::$order['items'][$ssl_item_id] = $ssl_dto;

		return self::$order;
	}

	/**
	 * Build order data
	 *
	 * @param $input
	 *
	 * @return array
	 * @throws AppException
	 */
	public static function buildOrder( $input ) {
		if ( !empty($input['sslorder']) ) {
			return OrderHelper::buildSSLOrder($input);
		}

		if ( ! icd_hosting_valid_domain( $input['domain'] ) ) {
			icd_hosting_error( array( 'message' => 'domain_search.invalid', 'field' => 'domain', 'subtype' => 'invalid' ) );
		}

		// Get catalog & store data
		$order_data = ICD_Hosting::instance()->getApp()->api->get( 'order-data' );
		if ( empty( $order_data['data'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}

		$order_data = $order_data['data'];

		self::init( $order_data );
		$catalog_by_location = self::prepareCatalog();

		if ( empty( $catalog_by_location[ $input['location'] ][ $input['plan'] ]['product_id'] ) ) {
			icd_hosting_error( 'error.product_not_found' );
		}

		// Get order & order items models
		self::$order = OrderDTO::getOrder();
		if ( ! empty( $input['order_id'] ) ) {
			self::$order['order_id'] = $input['order_id'];
		}

		$hosting_dto = OrderDTO::getHostingItem();

		if ( ! empty( $input['new_domain'] ) ) {
			$domain_dto = OrderDTO::getDomainItem();
			$sld_tld    = icd_hosting_sld_tld( $input['domain'] );

			if ( strpos( $input['plan'], 'multivps' ) !== false ) {
				if ( ! $sld_tld or empty( $catalog_by_location[ $input['location'] ][ $input['plan'] ]['extra:domain'][ $sld_tld['tld'] ]['order'][ $input['period'] ] ) ) {
					icd_hosting_error( 'error.tld_not_supported' );
				}
			} else if ( ! $sld_tld or empty( $catalog_by_location[ $input['location'] ][ $input['plan'] ]['bonus:domain'][ $sld_tld['tld'] ]['order'][ $input['period'] ] ) ) {
				icd_hosting_error( 'error.tld_not_supported' );
			}
		}

		if ( empty( $input['terms'] ) ) {
			AppException::error( array(
				'message' => 'required.you_must_agree_with_terms_of_use',
				'field'   => 'terms',
				'subtype' => 'required'
			) );
		}

		if ( ! empty( $input['new_domain'] ) and $order_data['tlds'][ $sld_tld['tld'] ]['icann'] and empty( $input['icann'] ) ) {
			AppException::error( array(
				'message' => 'required.you_must_agree_with_icann_terms',
				'field'   => 'icann',
				'subtype' => 'required'
			) );
		}

		if ( AppException::hasErrors() ) {
			throw new AppException;
		}

		// Fill order main/meta data
		self::$order['currency']       = $order_data['store']['currency'];
		self::$order['ip']             = $input['ip'];
		self::$order['referer']        = $input['referer'];
		self::$order['app']            = 'WORDPRESS';
		self::$order['payment_method'] = isset( $input['payment_method'] ) ?
			ICD_Hosting::instance()->getApp()->payment->get( $input['payment_method'] )->paymentMethod( $input['payment_method'] ) : 'Pending';

		self::$order['user_agent']          = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		self::$order['user_browser_locale'] = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';

		// Fill contact data where applicable
		foreach ( $input['contact'] as $key => $val ) {
			if ( array_key_exists( $key, self::$order ) ) {
				self::$order[ $key ] = $val;
			}
		}

		// Fill hosting product props
		$plan_item                       = $catalog_by_location[ $input['location'] ][ $input['plan'] ];
		$hosting_dto['hosting.hostname'] = icd_hosting_valid_domain( $input['domain'] );
		$hosting_dto['action']           = 'order';
		$hosting_dto['period']           = $input['period'];
		$hosting_dto['catalog_id']       = $plan_item['catalog_id'];
		$hosting_dto['type']             = $hosting_dto['product_type'] = $plan_item['product_type'];
		$hosting_dto['product']          = $hosting_dto['item'] = $plan_item['product'];
		$hosting_dto['name']             = $plan_item['name'] . ', ' . icd_hosting_tr( 'locations.' . $input['location'] ) . ': ' . $hosting_dto['hosting.hostname'];

		$hosting_dto['price'] = $plan_item['order'][ $input['period'] ]['price'];

		//create new or update existing hosting item
		$hosting_item_id = empty( $input['hosting_item_id'] ) ? 0 : $input['hosting_item_id'];
		if ( $hosting_item_id !== 0 ) {
			$hosting_dto['item_id'] = $hosting_item_id;
		}

		self::$order['items'][ $hosting_item_id ] = $hosting_dto;

		if ( isset( $domain_dto ) ) {
			if ( strpos( $input['plan'], 'multivps' ) !== false ) {
				$domain_item = $catalog_by_location[ $input['location'] ][ $input['plan'] ]['extra:domain'][ $sld_tld['tld'] ];
			} else {
				$domain_item = $catalog_by_location[ $input['location'] ][ $input['plan'] ]['bonus:domain'][ $sld_tld['tld'] ];
			}
			$domain_dto['domain.tld'] = $sld_tld['tld'];
			$domain_dto['domain.sld'] = $sld_tld['sld'];
			$domain_dto['action']     = 'order';
			$domain_dto['period']     = $input['period'];
			if ( $plan_item['periodicity'] == 'MO' ) {
				$domain_dto['period'] = $domain_dto['period'] <= 12 ? 1 : intdiv( $domain_dto['period'], 12 );
			}
			$domain_dto['catalog_id'] = $domain_item['catalog_id'];
			$domain_dto['type']       = $domain_dto['product_type'] = $domain_item['product_type'];
			$domain_dto['product']    = $domain_dto['item'] = $domain_item['product'];
			$domain_dto['name']       = $domain_item['name'] .
			                            ': ' . $sld_tld['sld'] . '.' . $sld_tld['tld'];

			$domain_dto['price'] = $domain_item['order'][ $input['period'] ]['price'];

			if ( ! empty( $input['domain_extra_attributes'] ) ) {
				$domain_dto['domain.extra_attributes'] = $input['domain_extra_attributes'];
			}

			//create new or update existing domain item
			$domain_item_id = empty( $input['domain_item_id'] ) ? 1 : $input['domain_item_id'];
			if ( $domain_item_id !== 1 ) {
				$domain_dto['item_id'] = $domain_item_id;
			}

			$domain_dto['parent']                    = $hosting_item_id;
			self::$order['items'][ $domain_item_id ] = $domain_dto;
		}

		return icd_hosting_sanitize_all( self::$order );
	}

	/**
	 * Get order details
	 *
	 * @param $order_id
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public static function getOrderDetails( $order_id ) {
		if ( ! preg_match( '/[A-Z0-9]{16}/i', $order_id ) ) {
			icd_hosting_error( 'invalid.order_id' );
		}

		$result = ICD_Hosting::instance()->getApp()->api->get( 'order-details', array( 'order_id' => $order_id ) );
		if ( empty( $result['data']['order'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}

		return icd_hosting_sanitize_all( $result['data']['order'] );
	}

	/**
	 * Get payment request details
	 *
	 * @param $request_id
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public static function getPaymentRequestDetails( $request_id ) {
		$result = ICD_Hosting::instance()->getApp()->api->get( 'payment-request-details', array( 'payment_request_id' => $request_id ) );
		if ( empty( $result['data']['payment_request'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}

		return  $result['data']['payment_request'];
	}

	/**
	 * Get order request details
	 *
	 * @param $request_id
	 *
	 * @return mixed
	 * @throws AppException
	 */
	public static function getRequestDetails( $request_id ) {
		if ( ! preg_match( '/[0-9]{1,16}/i', $request_id ) ) {
			icd_hosting_error( 'invalid.request_id:' . $request_id );
		}

		$result = ICD_Hosting::instance()->getApp()->api->get( 'order-request-details', [ 'id' => $request_id ] );
		if ( empty( $result['data']['order'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_request' );
		}

		return $result['data']['order'];
	}


	public static function domainsCertificatesInit( $ptype, $default_hosting_option = 'standalone', $default_plan = 'economy', $default_datacenter = 'centurylink' ) {
		$prices = $prices_map = $path_map = $offered = [];
		$result = ICD_Hosting::instance()->getApp()->api->get( 'order-data' );

		if ( empty( $result['data'] ) ) {
			icd_hosting_error( 'error.failed_to_retrieve_order_data' );
		}

		OrderHelper::init( $result['data'] );

		$viewdata                   = array(
			//'datacenters' => OrderHelper::offeredDatacenters(),
			'countries'  => OrderHelper::translateCountries(),
			'processors' => ICD_Hosting::instance()->getApp()->payment->processors(),
			//'preselected_tlds' => $this->preselectedTLDs(),
		);
		$viewdata['payment_method'] = key( $viewdata['processors'] );

		$catalog_params = [
			'extended'    => 1,
			'flat'        => '1',
			'store_id'    => $result['data']['store']['store_id'],
			'reseller_id' => $result['data']['store']['reseller_id'],
			'currency'    => $result['data']['store']['currency']
		];
		$global_catalog = ICD_Hosting::instance()->getApp()->api->get( 'catalog', $catalog_params );
		$catalog        = $global_catalog['data']['catalog'];
		$products       = $result['data']['products'];
		$has_plans      = 0;

		foreach ( $catalog as $cid => $node ) {
			$prod = $products[ $node['product_id'] ];
			if ( in_array( $prod['type'], [ 'hosting', 'server' ] ) and ! empty( $node['prices']['order'] ) ) {
				$has_plans = 1;
			}

			if ( $prod['type'] == $ptype ) {
				$pnode = $catalog[ $node['parent_id'] ];
				$pprod = $products[ $pnode['product_id'] ];
				$hnode = $pnode && isset( $catalog[ $pnode['parent_id'] ] ) ? $catalog[ $pnode['parent_id'] ] : null;
				$hprod = $hnode && isset( $products[ $hnode['product_id'] ] ) ? $products[ $hnode['product_id'] ] : null;

				$prices[ $node['parent_id'] ]['group']                                    = $pprod['product'];
				$prices[ $node['parent_id'] ]['name']                                     = $catalog[ $node['parent_id'] ]['name'];
				$prices[ $node['parent_id'] ]['prices'][ $prod['product'] ]['catalog_id'] = $cid;
				$prices[ $node['parent_id'] ]['prices'][ $prod['product'] ]['name']       = $node['name'];

				$prices[ $node['parent_id'] ]['prices'][ $prod['product'] ]['price'][1] = [
					'price'       => $node['prices']['order'][1]['price'],
					'periodicity' => icd_hosting_tr_choice( "periodicity.{$prod['periodicity']}", 1 )
				];

				if (
					$hprod and $pprod and $hprod['product'] == $default_plan and $hprod['datacenter'] == $default_datacenter and
					                                                             ( $pprod['product'] == "bonus:$ptype" or $pprod['product'] == "extra:$ptype" and ! isset( $prices_map['new'] ) )
				) {
					$prices_map['new'] = $node['parent_id'];
				} else if ( $pprod['product'] == "standalone:$ptype" ) {
					$prices_map['standalone'] = $node['parent_id'];
				}

				$offered[ $prod['product'] ]    = $node['name'];
				$path_map[ $node['parent_id'] ] = $pnode['path'];
			}
		}

		$path_map_reverse = array_flip( $path_map );

		foreach ( $prices as $group_id => $list ) {
			if ( $list['group'] == "bonus:$ptype" ) {
				$bonus_path = $path_map[ $group_id ];
				$extra_path = str_replace( ",bonus:$ptype,", ",extra:$ptype,", $bonus_path );
				if ( isset( $path_map_reverse[ $extra_path ] ) ) {
					foreach ( $prices[ $path_map_reverse[ $extra_path ] ]['prices'] as $prod => $details ) {
						if ( ! isset( $prices[ $group_id ]['prices'][ $prod ] ) ) {
							$prices[ $group_id ]['prices'][ $prod ] = $details;
						} else {
							$prices[ $group_id ]['prices'][ $prod ]['price'][1]['extra_price'] = $details['price'][1]['price'];
						}
					}
				}
			}
		}
		$accounts = $cart_ips = $dedicated_ips = $new_ips = [];

		if ( ! $has_plans ) {
			$default_hosting_option = 'standalone';
		}

		$viewdata['idx']                    = 0;//icd_hosting_rand_hash();
		$viewdata['offered']                = $offered;
		$viewdata['accounts']               = [];
		$viewdata['default_product']        = key( $offered );
		$viewdata['has_plans']              = $has_plans;
		$viewdata['default_hosting_option'] = $default_hosting_option;

		$viewdata['formdata']['currency']               = $result['data']['store']['currency'];
		$viewdata['formdata']['prices']                 = icd_hosting_filter_keys( $prices, $prices_map );
		$viewdata['formdata']['prices_map']             = $prices_map;
		$viewdata['formdata']['default_hosting_option'] = $default_hosting_option;

		if ( $ptype == 'ssl' ) {
			$viewdata['countries']                 = OrderHelper::translateCountries( $result['data']['countries'] );
			$viewdata['formdata']['cart_ips']      = $cart_ips;
			$viewdata['formdata']['new_ips']       = $new_ips;
			$viewdata['formdata']['dedicated_ips'] = $dedicated_ips;
		}
		$formdata                   = $viewdata['formdata'];
		$default_prices             = $formdata['prices'][ $formdata['prices_map'][ $formdata['default_hosting_option'] ] ]['prices'];
		$viewdata['default_prices'] = $default_prices;

		return $viewdata;
	}
}
