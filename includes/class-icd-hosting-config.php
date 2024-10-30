<?php

namespace ICD\Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ICD_Hosting_Config stores plugin configuration.
 *
 * @package ICD\Hosting
 */
class ICD_Hosting_Config {
	const OPTION_NAME = ICD_HOSTING_PLUGIN_NAME;
	const API_ENDPOINT = 'https://api.suresupport.com';
	const DEMO_STORE_KEY = '5eMLDoTSaipQN13HfREp5CtuAYKpHUHg';
	const DEMO_STORE_SEC = 'xtUoWH4lJL1FXuKBMHNqTY7hALAwIQQT';

	private static $options;

	public static function get_config() {
		self::$options = get_option( self::OPTION_NAME );

		return array(
			'locale'             => isset( self::$options['locale'] ) ? self::$options['locale'] : get_locale(),
			'use_widget_css'     => ( isset( self::$options['use_widget_css'] ) && self::$options['use_widget_css'] == 'on' ) ? true : false,
			'timezone'           => get_option( 'timezone_string' ),
			'views'              => array(
				'path'   => '',
				'smarty' => array(
					'cache_dir'   => '',
					'compile_dir' => '',
				),
			),
			'api'                => array(
				'url' => self::API_ENDPOINT,
				'key' => ! empty( self::$options['api_key'] ) ? self::$options['api_key'] : self::DEMO_STORE_KEY,
				'sec' => ! empty( self::$options['api_sec'] ) ? self::$options['api_sec'] : self::DEMO_STORE_SEC,
				'cache' => true
			),
			'payment'            => array(
				'test_mode' => ( isset( self::$options['payment_test_mode'] ) && self::$options['payment_test_mode'] == 'on' ) ? true : false,
			),
			'widget_routes'      => array( 'domaincheck', 'hostingorder', 'thankyou', 'terms', 'payment', 'request' ),
			'preselected_tlds'   => array( 'com', 'net', 'org', 'info', 'biz', 'us' ),
			'ipn_log'            => '',
			'notification_email' => '',
		);
	}
}
