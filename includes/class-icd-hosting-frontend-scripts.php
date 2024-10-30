<?php

namespace ICD\Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ICD_Hosting_Frontend_Scripts handles frontend setup.
 */
class ICD_Hosting_Frontend_Scripts {

	private static $scripts = array();

	private static $styles = array();

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ), 100 );
	}

	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = ICD_HOSTING_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = ICD_HOSTING_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	private static function register_style( $handle, $path, $deps = array(), $version = ICD_HOSTING_VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = ICD_HOSTING_VERSION, $media = 'all' ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}

	public static function load_scripts() {
		$assets_path = str_replace( array( 'http:', 'https:' ), '', ICD_Hosting()->plugin_url() ) . '/assets/';

		self::register_script( 'bootstrap', $assets_path . 'js/bootstrap.min.js', array( 'jquery' ), '3.4.1' );
		self::register_script( 'widget', $assets_path . 'js/widget.js', array( 'jquery' ) );

		self::enqueue_script( 'bootstrap', $assets_path . 'js/bootstrap.min.js', array( 'jquery' ), '3.4.1' );
		self::enqueue_script( 'widget', $assets_path . 'js/widget.js', array( 'jquery' ) );

		self::register_style( 'bootstrap', $assets_path . 'css/bootstrap.css' );
		self::enqueue_style( 'bootstrap', $assets_path . 'css/bootstrap.css' );

		$config = ICD_Hosting_Config::get_config();
		if ( $config['use_widget_css'] ) {
			self::register_style( 'widget', $assets_path . 'css/widget.css' );
			self::enqueue_style( 'widget', $assets_path . 'css/widget.css' );
		}
	}
}

ICD_Hosting_Frontend_Scripts::init();
