<?php

namespace ICD\Hosting\Admin;

use ICD\Hosting\ICD_Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ICD_Hosting_Admin_Settings' ) ) :

	/**
	 * Class ICD_Hosting_Admin_Settings
	 *
	 * @package ICD\Hosting
	 */
	class ICD_Hosting_Admin_Settings {
		/**
		 * Option name to DB
		 */
		const OPTION_NAME = ICD_HOSTING_PLUGIN_NAME;
		/**
		 * Group option name
		 */
		const OPTION_GROUP = 'icd-hosting-options';
		/**
		 * Option for registration wizard
		 */
		const REGISTER_FORM_SEND = 'icd-hosting-register-options';

		/**
		 * Stores options
		 *
		 * @var mixed|void
		 */
		private static $options;

		/**
		 * Stores reseller store if present
		 *
		 * @var
		 */
		private static $store;

		/**
		 * Language set
		 *
		 * @var array
		 */
		private $locales = array(
			'en' => 'en',
		);

		/**
		 * ICD_Hosting_Admin_Settings constructor.
		 */
		public function __construct() {
			self::$options = get_option( self::OPTION_NAME );

			if ( empty( self::$options ) ) {
				update_option( self::OPTION_NAME, [
					'use_widget_css'    => 'on',
					'plans_per_row'     => 3,
					'show_renew_price'  => 'on',
					'payment_test_mode' => 'on',
				] );
			}

			add_action( 'admin_menu', array( $this, 'welcome_page' ), 11 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
			add_action( 'admin_menu', array( $this, 'settings_menu' ), 10 );
			add_action( 'admin_head', array( $this, 'remove_submenus' ) );

			self::$options['registration_form_send'] = get_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND );
		}

		/**
		 * Add welcome menu page
		 */
		public function welcome_page() {
			add_submenu_page(
				'icd_hosting',
				esc_html__( 'ICDSoft Hosting Settings', 'icd-hosting' ),
				esc_html__( 'Settings', 'icd-hosting' ),
				'activate_plugins',
				'icd-hosting-welcome',
				array( $this, 'welcome_page_content' )
			);
		}

		/**
		 * Show welcome menu page content
		 */
		public function welcome_page_content() {
			if ( ! empty( self::$options['api_key'] ) ) {
				wp_redirect( esc_url( admin_url( 'admin.php?page=icd-hosting-settings' ) ) );
			}

			$valid_page_types = [ 'settings', 'register' ];
			$type             = isset( $_GET['icd_welcome_choice'] ) ? sanitize_text_field( wp_unslash( $_GET['icd_welcome_choice'] ) ) : '';

			if ( in_array( $type, $valid_page_types ) ) {
				wp_redirect( esc_url( admin_url( 'admin.php?page=icd-hosting-' . $type ) ) );
			}

			?>
			<style type="text/css">
				.icd-welcome-page-wrapper {
					margin: auto;
					width: 50%;
				}

				.icd-welcome-page-wrapper img {
					float: left;
					width: 160px;
					height: auto;
					margin-right: 50px;
				}

				.icd-welcome-page-header {
					margin-bottom: 50px;
				}

				.icd-welcome-page-body,
				.icd-welcome-page-footer {
					text-align: center;
				}

				.icd-welcome-page-body .button {
					margin-right: 15px;
					min-width: 170px;
					min-height: 50px;
					padding: 10px;
				}
			</style>
			<div class="hosting-widget">
				<div class="icd-welcome-page-wrapper">
					<div class="icd-welcome-page-header">
						<img src="<?php echo ICD_Hosting::instance()->plugin_url() . '/assets/img/welcome-logo.png' ?>"
							 alt="ICDsoft Hosting Welcome">
						<h2><?php esc_html_e( "You're almost ready!", 'icd-hosting' ); ?></h2>
						<br>
						<p>
							<strong>
								<?php esc_html_e( "An ICDSoft reseller account is required to use the plugin and start selling web hosting services.", 'icd-hosting' ); ?>
								<br>
								<?php esc_html_e( "Signing up is free of charge and obligations.", 'icd-hosting' ); ?>
							</strong>
						</p>
					</div>
					<div class="icd-welcome-page-body">
						<br>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=icd-hosting-welcome&icd_welcome_choice=settings' ) ); ?>"
						   class="button button-primary"><?php esc_html_e( 'I HAVE AN ACCOUNT', 'icd-hosting' ) ?></a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=icd-hosting-welcome&icd_welcome_choice=register' ) ); ?>"
						   class="button button-primary"><?php esc_html_e( 'FREE SIGN-UP', 'icd-hosting' ) ?></a>
						<p><strong>
								<?php esc_html_e( 'If you have an existing reseller account with us, you will just have to enter the API key and HMAC secret on the next page.', 'icd-hosting') ?>
							 	<?php esc_html_e( 'They can be obtained from the Main Settings page of your online store in the ', 'icd-hosting' ) ?>
							 	<?php echo ' <a target="_blank" href="https://reseller.icdsoft.com">' . esc_html__( 'ICDSoft Account Panel.', 'icd-hosting' ) . '</a>' ?>
							 	<?php esc_html_e( 'Otherwise, you can just sign up, and the API key and HMAC secret will be configured automatically for you.', 'icd-hosting' ) ?>
							</strong>
						</p>
					</div>
					<div class="icd-welcome-page-footer">
						<p>
							<strong><?php _e( 'Need help? Send us an email to <a href="mailto:support@icdsoft.com">support@icdsoft.com,</a> or submit a support ticket via the ICDSoft Account Panel -> 24/7 Support. We respond within 15 minutes.', 'icd-hosting' ); ?></strong>
						</p>
					</div>
				</div>
			</div>
			<?php

		}

		/**
		 * Add ICD Hosting main menu page
		 */
		public function admin_menu() {
			add_menu_page( __( 'ICDSoft hosting', 'icd-hosting' ), __( 'ICDSoft Hosting', 'icd-hosting' ), 'manage_options', 'icd_hosting', null, ICD_Hosting::instance()->plugin_url() . '/assets/img/favicon.png', '25.55' );
		}

		/**
		 * Add ICD Hosting settings menu page
		 */
		public function settings_menu() {
			$settings_page = add_submenu_page(
				'icd_hosting',
				__( 'ICDSoft Hosting Settings', 'icd-hosting' ),
				__( 'Settings', 'icd-hosting' ),
				'manage_options',
				'icd-hosting-settings',
				array( $this, 'create_plugin_settings_page' )
			);
			add_action( 'load-' . $settings_page, array( $this, 'settings_init' ) );
		}

		/**
		 * Remove icd_hosting submenu to avoid duplication
		 *
		 * Show/Hide Welcome page
		 */
		public function remove_submenus() {
			global $submenu;

			if ( isset( $submenu['icd_hosting'] ) ) {
				unset( $submenu['icd_hosting'][0] );

				foreach ( $submenu['icd_hosting'] as $key => $submenu_item ) {
					if ( ! empty( self::$options['api_key'] ) ) {
						// Hide Welcome submenu
						if ( $submenu_item[2] == 'icd-hosting-welcome' ) {
							unset( $submenu['icd_hosting'][ $key ] );
						}
					} else {
						// Hide Settings submenu
						if ( $submenu_item[2] == 'icd-hosting-settings' ) {
							unset( $submenu['icd_hosting'][ $key ] );
						}
					}
				}
			}
		}

		/**
		 * Generate form with settings fields and sections
		 */
		public function create_plugin_settings_page() {
			$store = ICD_Hosting::instance()->registration_service->getStore();

			if ( ! empty( $store['data'] ) ) {
				self::$store = $store['data'];
			}

			?>
			<div class="wrap">
				<h2><?php __( 'WP Hosting Settings' ) ?></h2>
				<form method="post" action="options.php">
					<?php
					settings_fields( self::OPTION_GROUP );
					do_settings_sections( 'icd-hosting-settings' );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Add settings fields and sections for a settings page
		 */
		public function settings_init() {
			register_setting(
				self::OPTION_GROUP,
				self::OPTION_NAME,
				array( $this, 'sanitize' )
			);

			add_settings_section(
				'main_settings', // ID
				__( 'Basic Settings', 'icd-hosting' ), // Title
				array( $this, 'print_main_settings' ), // Callback
				'icd-hosting-settings' // Page
			);

			/*add_settings_field(
				'locale', // ID
				__( 'Locale', 'icd-hosting' ), // Title
				array( $this, 'locale_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);*/

			add_settings_field(
				'use_widget_css', // ID
				__( 'Use widget CSS', 'icd-hosting' ), // Title
				array( $this, 'use_widget_css_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);

			add_settings_field(
				'plans_per_row', // ID
				__( 'Plans per row', 'icd-hosting' ), // Title
				array( $this, 'plans_per_row_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);

			add_settings_field(
				'show_renew_price', // ID
				__( 'Show renew price', 'icd-hosting' ), // Title
				array( $this, 'show_renew_price_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);

			add_settings_field(
				'show_monthly_price', // ID
				__( 'Show monthly price', 'icd-hosting' ), // Title
				array( $this, 'show_monthly_price_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);

			add_settings_field(
				'ignore_pretty_links', // ID
				__( 'Ignore pretty links', 'icd-hosting' ), // Title
				array( $this, 'ignore_pretty_links_callback' ), // Callback
				'icd-hosting-settings', // Page
				'main_settings' // Section
			);

			add_settings_section(
				'api_settings', // ID
				__( 'API Settings', 'icd-hosting' ), // Title
				array( $this, 'print_api_settings' ), // Callback
				'icd-hosting-settings' // Page
			);
			/*
			add_settings_field(
				'api_url', // ID
				__( 'URL', 'icd-hosting' ), // Title
				array( $this, 'api_url_callback' ), // Callback
				'icd-hosting-settings', // Page
				'api_settings' // Section
			);*/

			add_settings_field(
				'store_name', // ID
				__( 'Store Brand Name', 'icd-hosting' ), // Title
				array( $this, 'store_name_callback' ), // Callback
				'icd-hosting-settings', // Page
				'api_settings' // Section
			);

			add_settings_field(
				'api_key', // ID
				__( 'Authentication Key', 'icd-hosting' ), // Title
				array( $this, 'api_key_callback' ), // Callback
				'icd-hosting-settings', // Page
				'api_settings' // Section
			);

			add_settings_field(
				'api_sec', // ID
				__( 'HMAC Secret', 'icd-hosting' ), // Title
				array( $this, 'api_sec_callback' ), // Callback
				'icd-hosting-settings', // Page
				'api_settings' // Section
			);

			add_settings_section(
				'payment_settings', // ID
				__( 'Payment Settings', 'icd-hosting' ), // Title
				array( $this, 'print_payment_settings' ), // Callback
				'icd-hosting-settings' // Page
			);

			add_settings_field(
				'payment_test_mode', // ID
				__( 'Test mode', 'icd-hosting' ), // Title
				array( $this, 'payment_test_mode_callback' ), // Callback
				'icd-hosting-settings', // Page
				'payment_settings' // Section
			);
		}

		/**
		 * Sanitize input
		 *
		 * @param $input
		 *
		 * @return mixed
		 */
		public function sanitize( $input ) {
			if ( isset( $input['api_key'] ) ) {
				$input['api_key'] = trim( $input['api_key'] );
			}

			if ( isset( $input['api_sec'] ) ) {
				$input['api_sec'] = trim( $input['api_sec'] );
			}

			return $input;
		}

		/**
		 * Basic settings
		 */
		public function print_main_settings() {
		}

		/**
		 * Print locale options
		 */
		public function locale_callback() {
			$html = '';
			$html .= '<select id="locale" name="icd_hosting[locale]" style="width: 15em;">';
			foreach ( $this->locales as $key => $locale ) {
				$selected = isset( self::$options['locale'] ) && self::$options['locale'] == $key ? 'selected="selected"' : '';
				$html     .= '<option value="' . $key . '" ' . $selected . '>' . $locale . '</option>';
			}
			$html .= '</select>';

			printf( $html );
		}

		/**
		 * Print plans per row options
		 */
		public function plans_per_row_callback() {
			printf(
				'<input type="number" min="1" max="8" id="plans_per_row" name="icd_hosting[plans_per_row]" value="%s" placeholder="%s" style="width: 30em;"> <br>
				<p class="text-gray">' . __( 'The number of rows to show when compare_plans shortcode is used.', 'icd-hosting' ) . '</p>',
				isset( self::$options['plans_per_row'] ) ? esc_attr( self::$options['plans_per_row'] ) : '', '3'
			);
		}

		/**
		 * Show/gide plans renewal price
		 */
		public function show_renew_price_callback() {
			printf(
				'<input type="checkbox" id="show_renew_price" name="icd_hosting[show_renew_price]" %s> %2$s <br>
				<p class="text-gray">' . __( 'Show/hide renew price in compare_plans shortcode', 'icd-hosting' ) . '</p>',
				( isset( self::$options['show_renew_price'] ) && self::$options['show_renew_price'] == 'on' ) ? 'checked="checked"' : '',
				__( 'Enable', 'icd-hosting' )
			);
		}

		/**
		 * Ignore pretty links. Add ?param= in url
		 */
		public function ignore_pretty_links_callback() {
			printf(
				'<input type="checkbox" id="ignore_pretty_links" name="icd_hosting[ignore_pretty_links]" %s> %2$s <br>
				<p class="text-gray">' . __( 'Ignore pretty link structure and add ?order_id= to the page URL. Use if you have plugins that require a parameter in permalinks (e.g. translation plugins).', 'icd-hosting' ) . '</p>',
				( isset( self::$options['ignore_pretty_links'] ) && self::$options['ignore_pretty_links'] == 'on' ) ? 'checked="checked"' : '',
				__( 'Enable', 'icd-hosting' )
			);
		}

		/**
		 * Show/gide plans renewal price
		 */
		public function show_monthly_price_callback() {
			printf(
				'<input type="checkbox" id="show_monthly_price" name="icd_hosting[show_monthly_price]" %s> %2$s <br>
				<p class="text-gray">' . __( 'Show/hide monthly price instead of annual price in compare_plans shortcode.', 'icd-hosting' ) . '</p>',
				( isset( self::$options['show_monthly_price'] ) && self::$options['show_monthly_price'] == 'on' ) ? 'checked="checked"' : '',
				__( 'Enable', 'icd-hosting' )
			);
		}

		/**
		 * Print use widget css options
		 */
		public function use_widget_css_callback() {
			printf(
				'<input type="checkbox" id="use_widget_css" name="icd_hosting[use_widget_css]" %s> %2$s <br>
				<p class="text-gray">' . __( 'By default, the plugin uses its own CSS. If you uncheck this option, the plugin will use the CSS of your WordPress theme.', 'icd-hosting' ) . '</p>',
				( isset( self::$options['use_widget_css'] ) && self::$options['use_widget_css'] == 'on' ) ? 'checked="checked"' : '',
				__( 'Enable', 'icd-hosting' )
			);
		}

		/**
		 * Print api settings warning
		 */
		public function print_api_settings() {
			if ( empty( self::$store ) and ! empty( self::$options['api_key'] ) ) {
				echo '<div class="error-message error inline notice-warning notice-alt"><p>';
				_e( 'Authentication Key or HMAC Secret incorrect.', 'icd-hosting' );
				echo '</p></div>';
			}
		}

		/**
		 * Print api url option
		 * It`s hidden for now.
		 */
		public function api_url_callback() {
			printf(
				'<input type="text" id="api_url" name="icd_hosting[api_url]" value="%s" style="width: 30em;" />',
				isset( self::$options['api_url'] ) ? esc_attr( self::$options['api_url'] ) : ''
			);
		}

		/**
		 * Print reseller store name for entered api_key/api_sec
		 */
		public function store_name_callback() {
			if ( isset( self::$store['store']['title'] ) ) {
				printf( "<b>%s</b>", self::$store['store']['title'] );
			} else {
				echo '-';
			}
		}

		/**
		 * Print api_key input
		 */
		public function api_key_callback() {
			$disabled = self::$options['registration_form_send'] ? 'disabled="disabled"' : '';
			printf(
				'<input type="text" id="api_key" name="icd_hosting[api_key]" value="%s" ' . $disabled . ' style="width: 30em;" /> <br>
				<p class="text-gray">' . __( 'Enter the Authentication Key for your store. It can be obtained from the Account Panel -> Online Stores -> Management -> Edit -> Main Settings -> API Settings.', 'icd-hosting' ) . '</p>',
				isset( self::$options['api_key'] ) ? esc_attr( self::$options['api_key'] ) : ''
			);
		}

		/**
		 * Print api_sec input
		 */
		public function api_sec_callback() {
			$disabled = self::$options['registration_form_send'] ? 'disabled="disabled"' : '';
			printf(
				'<input type="text" id="api_sec" name="icd_hosting[api_sec]" value="%s" ' . $disabled . ' style="width: 30em;" /> <br>
				<p class="text-gray">' . __( 'Enter the HMAC Secret for your store. It can be obtained from the Account Panel -> Online Stores -> Management -> Edit -> Main Settings -> API Settings.', 'icd-hosting' ) . '</p>',
				isset( self::$options['api_sec'] ) ? esc_attr( self::$options['api_sec'] ) : ''
			);
		}

		/**
		 * Print payment settings section
		 */
		public function print_payment_settings() {
		}

		/**
		 * Print payment test mode option
		 */
		public function payment_test_mode_callback() {
			printf(
				'<input type="checkbox" id="payment_test_mode" name="icd_hosting[payment_test_mode]" %s> %2$s  <br> 
				 <p class="text-gray">' . __( 'Test mode allows you to test the API calls between your store and the payment processor without conducting real payment transactions.', 'icd-hosting' ) . '</p>',
				( isset( self::$options['payment_test_mode'] ) && self::$options['payment_test_mode'] == 'on' ) ? 'checked="checked"' : '',
				__( 'Enable', 'icd-hosting' )
			);
		}
	}

endif;

return new ICD_Hosting_Admin_Settings();
