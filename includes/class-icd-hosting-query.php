<?php

namespace ICD\Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ICD_Hosting_Query' ) ) :

	/**
	 * Handle URL parameters
	 *
	 * Class ICD_Hosting_Query
	 */
	class ICD_Hosting_Query {

		/**
		 * ICD_Hosting_Query constructor.
		 */
		public function __construct() {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

			add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );
		}

		/**
		 * Add query vars for plugin pages
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars[] = 'order_id';
			$vars[] = 'request_id';
			$vars[] = 'icd_hosting_payment_method';

			return $vars;
		}

		/**
		 *  Transforms a URL structure to match query vars
		 */
		public static function add_endpoint() {
			$page_hostingorder_uri = get_page_uri( get_option( 'icd_hosting_hostingorder_page_id' ) );
			$page_request_uri      = get_page_uri( get_option( 'icd_hosting_request_page_id' ) );
			$page_thankyou_uri     = get_page_uri( get_option( 'icd_hosting_thankyou_page_id' ) );
			$page_payment_uri      = get_page_uri( get_option( 'icd_hosting_payment_page_id' ) );
			$page_certificate_uri  = get_page_uri( get_option( 'icd_hosting_certificates_page_id' ) );

			$locale = get_locale();
			$locale = explode("_", $locale);
			$locale = isset($locale[0]) ? $locale[0] : "en";

			add_rewrite_rule( '^' . $locale . '/' . $page_hostingorder_uri . '-' . $locale . '/([^/]+)/?$', 'index.php?pagename=' . $page_hostingorder_uri . '-' . $locale . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $locale . '/' . $page_hostingorder_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_hostingorder_uri . '&order_id=$matches[1]', 'top' );

			add_rewrite_rule( '^' . $locale . '/' . $page_request_uri . '-' . $locale . '/([^/]+)/?$', 'index.php?pagename=' . $page_request_uri . '-' . $locale . '&request_id=$matches[1]&lang=' . $locale, 'top' );
			add_rewrite_rule( '^' . $locale . '/' . $page_request_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_request_uri . '&request_id=$matches[1]&lang=' . $locale, 'top' );

			add_rewrite_rule( '^' . $locale . '/' . $page_thankyou_uri . '-' . $locale . '/([^/]+)/?$', 'index.php?pagename=' . $page_thankyou_uri . '-' . $locale . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $locale . '/' . $page_thankyou_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_thankyou_uri . '&order_id=$matches[1]', 'top' );

			add_rewrite_rule( '^' . $locale . '/' . $page_payment_uri . '-' . $locale . '/([^/]+)/?$', 'index.php?pagename=' . $page_payment_uri . '-' . $locale . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $locale . '/' . $page_payment_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_payment_uri . '&order_id=$matches[1]', 'top' );

			add_rewrite_rule( '^' . $locale . '/' . $page_certificate_uri . '-' . $locale . '/([^/]+)/?$', 'index.php?pagename=' . $page_certificate_uri . '-' . $locale . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $locale . '/' . $page_certificate_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_certificate_uri . '&order_id=$matches[1]', 'top' );


			add_rewrite_rule( '^' . $page_hostingorder_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_hostingorder_uri . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $page_request_uri . '/(.+)$', 'index.php?pagename=' . $page_request_uri . '&request_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $page_thankyou_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_thankyou_uri . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $page_payment_uri . '/([^/]+)/([^/]+)?$', 'index.php?pagename=' . $page_payment_uri . '&order_id=$matches[1]&icd_hosting_payment_method=$matches[2]', 'top' );
			add_rewrite_rule( '^' . $page_payment_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_payment_uri . '&order_id=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $page_certificate_uri . '/([^/]+)/?$', 'index.php?pagename=' . $page_certificate_uri . '&order_id=$matches[1]', 'top' );

			flush_rewrite_rules();
		}
	}

endif;

return new ICD_Hosting_Query();
