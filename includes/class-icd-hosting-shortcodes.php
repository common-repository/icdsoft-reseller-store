<?php

namespace ICD\Hosting;

/**
 * Class ICD_Hosting_Shortcodes take care of all shortcodes needed for ICD Hosting plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ICD_Hosting_Shortcodes {

	/**
	 * Initialize all shortcodes with their callbacks.
	 */
	public static function init() {
		$shortcodes = array(
			'domaincheck'   => __CLASS__ . '::domain_check',
			'hostingorder'  => __CLASS__ . '::hosting_order',
			'certificates'  => __CLASS__ . '::certificates',
			'thankyou'      => __CLASS__ . '::thankyou',
			'terms'         => __CLASS__ . '::terms',
			'payment'       => __CLASS__ . '::payment',
			'request'       => __CLASS__ . '::request',
			'postback'      => __CLASS__ . '::postback',
			'plan_info'     => __CLASS__ . '::plan_info',
			'compare_plans' => __CLASS__ . '::compare_plans',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( $shortcode, $function );
		}
	}

	/**
	 * Wrapper for all shortcodes
	 *
	 * @param $function
	 * @param array $atts
	 * @param array $wrapper
	 *
	 * @return false|string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'hosting-widget',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	public static function domain_check( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Domain_Check', 'output' ), $atts );
	}

	public static function hosting_order( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Hosting_Order', 'output' ), $atts );
	}

	public static function certificates( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Certificates', 'output' ), $atts );
	}

	public static function thankyou( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Thankyou', 'output' ), $atts );
	}

	public static function terms( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Terms', 'output' ), $atts );
	}

	public static function payment( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Payment', 'output' ), $atts );
	}

	public static function request( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Request', 'output' ), $atts );
	}

	public static function plan_info( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Plan_Info', 'output' ), $atts );
	}

	public static function compare_plans( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Compare_Plans', 'output' ), $atts );
	}

	public static function postback( $atts ) {
		return self::shortcode_wrapper( array( '\ICD\Hosting\Shortcodes\ICD_Hosting_Shortcode_Postback', 'output' ), $atts );
	}
}
