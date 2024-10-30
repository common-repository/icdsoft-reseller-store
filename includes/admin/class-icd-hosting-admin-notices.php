<?php

namespace ICD\Hosting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class ICD_Hosting_Admin_Notices
 *
 * @package ICD\Hosting
 */
class ICD_Hosting_Admin_Notices {

	/**
	 * @var array
	 */
	private static $notification = array();

	/**
	 * Stores main notification and callbacks
	 *
	 * @var array
	 */
	private static $core_notifications = array(
		'install' => 'install_notice',
	);

	/**
	 * Initializes current notifications
	 */
	public static function init() {
		self::$notification = get_option( 'ICD_Hosting_Admin_Notices', array() );
	}

	/**
	 * Update notifications
	 */
	public static function update_notices() {
		update_option( 'ICD_Hosting_Admin_Notices', self::get_notification() );
	}

	/**
	 * Getter for notifications
	 *
	 * @return array
	 */
	public static function get_notification() {
		return self::$notification;
	}

	/**
	 * Reset notification array
	 */
	public static function delete_all_notification() {
		self::$notification = array();
	}

	/**
	 * Add notification
	 *
	 * @param $name
	 */
	public static function push_notification( $name ) {
		self::$notification = array_unique( array_merge( self::get_notification(), array( $name ) ) );
	}

	/**
	 * Delete notification
	 *
	 * @param $name
	 */
	public static function delete_notification( $name ) {
		self::$notification = array_diff( self::get_notification(), array( $name ) );
		delete_option( 'icd_hosting_admin_notice_' . $name );
	}

	/**
	 *
	 * Check if notification exists
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public static function has_notification( $name ) {
		return in_array( $name, self::get_notification(), true );
	}

	/**
	 * Handle dissmis notifications
	 */
	public static function hide_notification() {
		if ( isset( $_GET['icd-hosting-hide-notice'] ) && isset( $_GET['_icd_hosting_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_icd_hosting_notice_nonce'] ) ), 'icd_hosting_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['icd-hosting-hide-notice'] ) );

			self::delete_notification( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'icd_hosting_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * Add all notifications
	 */
	public static function push_notifications() {
		$notification = self::get_notification();

		if ( empty( $notification ) ) {
			return;
		}

		foreach ( $notification as $notice ) {
			if ( ! empty( self::$core_notifications[ $notice ] ) && apply_filters( 'icd_hosting_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_notifications[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}

	/**
	 * Add custom notification
	 *
	 * @param $name
	 * @param $notice_html
	 */
	public static function add_custom_notification( $name, $notice_html ) {
		self::push_notification( $name );
		update_option( 'icd_hosting_admin_notice_' . $name, wp_kses_post( $notice_html ) );
	}

	/**
	 * Include custom notifications
	 */
	public static function output_custom_notifications() {
		$notices = self::get_notification();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notifications[ $notice ] ) ) {
					$notice_html = get_option( 'icd_hosting_admin_notice_' . $notice );

					if ( $notice_html ) {
						include dirname( __FILE__ ) . '/views/html-notice-custom.php';
					}
				}
			}
		}
	}

	/**
	 * Show notificatiion for registration
	 */
	public static function install_notice() {
		include dirname( __FILE__ ) . '/views/html-notice-install.php';
	}

	/**
	 * Show notification after registration is completed
	 */
	public static function resend_email_notice() {
		include dirname( __FILE__ ) . '/views/html-resend-email.php';
	}

}

ICD_Hosting_Admin_Notices::init();
