<?php

namespace ICD\Hosting\Services;

/**
 * Class OrderDTO
 *
 * @package ICD\Hosting\Services
 */
class OrderDTO {

	/**
	 * Order contacts
	 *
	 * @var array
	 */
	private static $order_contacts = array(
		'firstname'          => null,
		'lastname'           => null,
		'company'            => null,
		'job'                => null,
		'address'            => null,
		'address2'           => null,
		'city'               => null,
		'state'              => null,
		'zip'                => null,
		'country'            => null,
		'email'              => null,
		'email2'             => null,
		'phone_country'      => null,
		'phone_country_code' => null,
		'phone'              => null,
		'fax_country'        => null,
		'fax_country_code'   => null,
		'fax'                => null,

	);

	/**
	 * Order properties
	 *
	 * @var array
	 */
	private static $order_props = array(
		'currency' => null,
		'referer'  => null,
		'ip'       => null,
		'items'    => array(),
	);

	/**
	 * Product properties
	 *
	 * @var array
	 */
	private static $product_props = array(
		'type'         => null,
		'product_type' => null,
		'product'      => null,
		'item'         => null,
		'name'         => null,
		'price'        => null,
	);

	/**
	 * Service properties
	 *
	 * @var array
	 */
	private static $service_props = array(
		'catalog_id' => null,
		'action'     => null,
		'period'     => null,
		'parent'     => null,
	);

	/**
	 * Hosting item
	 *
	 * @var array
	 */
	private static $hosting_item = array(
		'hosting.hostname' => null, // e.g example.com
	);

	/**
	 * Domain item
	 *
	 * @var array
	 */
	private static $domain_item = array(
		'domain.sld'              => null,
		'domain.tld'              => null, // e.g com, net
		'domain.extra_attributes' => null,
	);

	/**
	 * Certificate item
	 *
	 * @var array
	 */
	private static $ssl_item = array(
		'ssl.ip_type' => null,
		'ssl.organization' => null,
		'ssl.organization_unit' => null,
		'ssl.country' => null,
		'ssl.state' => null,
		'ssl.city' => null,
		'ssl.address' => null,
		'ssl.zip' => null,
		'ssl.email' => null,
		'ssl.approver_email' => null,
		'ssl.common_name' => null,
	);


	/**
	 * @return array
	 */
	public static function getOrderContacts() {
		return self::$order_contacts;
	}

	/**
	 * Get order contacts and order properties
	 *
	 * @return array
	 */
	public static function getOrder() {
		return array_merge( self::$order_contacts, self::$order_props );
	}

	/**
	 * Get domain item, service and product properties
	 *
	 * @return array
	 */
	public static function getDomainItem() {
		return array_merge( self::$product_props, self::$service_props, self::$domain_item );
	}

	/**
	 * Get certificate item, service and product properties
	 *
	 * @return array
	 */
	public static function getSSLItem() {
		return array_merge(self::$product_props, self::$service_props, self::$ssl_item);
	}

	/**
	 * Get hosting item, service and product properties
	 *
	 * @return array
	 */
	public static function getHostingItem() {
		return array_merge( self::$product_props, self::$service_props, self::$hosting_item );
	}
}