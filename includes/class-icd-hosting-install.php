<?php

namespace ICD\Hosting;

use ICD\Hosting\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ICD_Hosting_Install {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}

	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'icd_hosting_version' ) !== ICD_Hosting()->version ) {
			self::install();
		}
	}

	public static function install() {
		global $wpdb;

		if ( ! defined( 'ICD_HOSTING_INSTALLING' ) ) {
			define( 'ICD_HOSTING_INSTALLING', true );
		}

		self::create_pages();
		self::create_upload_dir();

		add_option( 'icd_hosting_version',  ICD_Hosting()->version);

		ICD_Hosting_Query::add_endpoint();

		flush_rewrite_rules();
	}

	public static function create_pages() {
		include_once( 'admin/icd-hosting-admin-functions.php' );

		$pages = array(
			'domaincheck'  => array(
				'name'    => _x( 'domaincheck', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Domain Check', 'Page title', 'icd-hosting' ),
				'content' => '[domaincheck]'
			),
			'hostingorder' => array(
				'name'    => _x( 'hostingorder', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Hosting Order', 'Page title', 'icd-hosting' ),
				'content' => '[hostingorder]'
			),
			'certificates' => array(
				'name'    => _x( 'certificates', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Certificates', 'Page title', 'icd-hosting' ),
				'content' => '[certificates]'
			),
			'thankyou'     => array(
				'name'    => _x( 'thankyou', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Thank you', 'Page title', 'icd-hosting' ),
				'content' => '[thankyou]'
			),
			'terms'        => array(
				'name'    => _x( 'terms', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Terms of Use', 'Page title', 'icd-hosting' ),
				'content' => '[terms]'
			),
			'payment'      => array(
				'name'    => _x( 'payment', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Payment', 'Page title', 'icd-hosting' ),
				'content' => '[payment]'
			),
			'request'      => array(
				'name'    => _x( 'request', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Payment Request', 'Page title', 'icd-hosting' ),
				'content' => '[request]'
			),
			'postback'     => array(
				'name'    => _x( 'postback', 'Page slug', 'icd-hosting' ),
				'title'   => _x( 'Postback', 'Page title', 'icd-hosting' ),
				'content' => '[postback]'
			),
		);

		$exclude_page_ids = [];

		foreach ( $pages as $key => $page ) {
			$page_id = Admin\icd_hosting_create_page(
				esc_sql( $page['name'] ),
				'icd_hosting_' . $key . '_page_id',
				$page['title'],
				$page['content'],
				! empty( $page['parent'] ) ? icd_hosting_get_page_id( $page['parent'] ) : ''
			);

			if ( in_array( $key, [ 'thankyou', 'payment', 'request', 'postback' ] ) ) {
				$exclude_page_ids[] = $page_id;
			}
		}

		update_option( 'icd_hosting_excluded_pages_ids', $exclude_page_ids );
		add_filter( 'get_pages', 'icd_hosting_exclude_pages' );
	}

	public static function deactivate() {
		wp_trash_post( get_option( 'icd_hosting_domaincheck_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_hostingorder_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_thankyou_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_terms_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_payment_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_postback_page_id' ) );
		wp_trash_post( get_option( 'icd_hosting_request_page_id' ) );

		wp_cache_flush();
	}

	private static function create_upload_dir() {
		try {
			$upload_dir = wp_upload_dir();
			$user_dirname = untrailingslashit( wp_normalize_path( $upload_dir['basedir'] ) ) . '/' . 'icd-hosting';

			if ( ! file_exists( $user_dirname ) ) {
				wp_mkdir_p( $user_dirname );
			}
		} catch (\Exception $exception) {
			// silent
		}
	}
}

ICD_Hosting_Install::init();
