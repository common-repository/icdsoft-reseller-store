<?php

namespace ICD\Hosting\Admin;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\ICD_Hosting_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ICD_Hosting_Admin {

	/**
	 * ICD_Hosting_Admin constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'show_admin_notices' ), 100 );
	}

	/**
	 * Includes needed classes
	 */
	public function includes() {
		include_once( 'class-icd-hosting-admin-settings.php' );

		if ( ! empty( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'icd-hosting-register':
					include_once dirname( __FILE__ ) . '/class-icd-hosting-admin-setup-wizard.php';
					break;
			}
		}
	}

	/**
	 * Handle admin notification
	 */
	public function show_admin_notices() {
		$account_activated = ICD_Hosting::instance()->registration_service->checkActivation();
		if ( $account_activated and empty( $_GET['activated'] ) ) {
			wp_redirect( admin_url( 'admin.php?page=icd-hosting-settings&activated=1' ) );
		}

		$options            = get_option( ICD_Hosting_Config::OPTION_NAME );
		$register_form_send = get_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND );
		$new_install        = true;

		if ( ! empty( $options['api_key'] ) or ! empty( $options['api_sec'] ) ) {
			$new_install = false;
		}

		if ( empty( $options['api_key'] ) and ! empty( $register_form_send ) and ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'icd-hosting-settings' ) ) ) {
			ICD_Hosting_Admin_Notices::resend_email_notice();
		}

		if ( $new_install and empty( $register_form_send ) and ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'icd-hosting-settings' ) ) ) {
			ICD_Hosting_Admin_Notices::install_notice();
		}

		if ( isset( $_GET['activated'] ) && isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'icd-hosting-settings' ) ) ) {
			add_settings_error( 'icd-hosting', 'settings_updated', __( 'API Settings activated.', 'icd-hosting' ), 'updated' );
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			echo '<div class="message updated"><p>' . esc_html__( 'Your changes have been saved.', 'icd-hosting' ) . '</p></div>';
		}
	}

}

return new ICD_Hosting_Admin();
