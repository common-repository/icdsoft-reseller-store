<?php

namespace ICD\Hosting\Admin;

use ICD\Hosting\ICD_Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ICD_Hosting_Admin_Setup_Wizard
 *
 * @package ICD\Hosting
 */
class ICD_Hosting_Admin_Setup_Wizard {

	/**
	 * ICD_Hosting_Admin_Setup_Wizard constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add menu option page
	 */
	public function admin_menus() {
		add_options_page( '', '', 'manage_options', 'icd-hosting-register', '' );
	}

	/**
	 * Output registration form and terminates
	 */
	public function setup_wizard() {
		$options                = get_option( ICD_Hosting_Admin_Settings::OPTION_NAME );
		$registration_form_send = get_transient( ICD_Hosting_Admin_Settings::REGISTER_FORM_SEND );

		if ( empty( $_GET['page'] ) || 'icd-hosting-register' !== $_GET['page'] || ! empty( $options['api_key'] ) || $registration_form_send ) {
			wp_safe_redirect( esc_url( admin_url( 'admin.php?page=icd-hosting-settings' ) ) );
		}

		ob_start();
		$this->icd_hosting_register_header();
		$this->icd_hosting_register_content();
		$this->icd_hosting_register_footer();
		exit;
	}

	/**
	 * Load scripts needed for registration page
	 */
	public function enqueue_scripts() {
		$assets_path = str_replace( array(
				'http:',
				'https:'
			), '', ICD_Hosting::instance()->plugin_url() ) . '/assets/';

		wp_register_script( 'bootstrap', $assets_path . 'js/bootstrap.min.js' );
		wp_register_script( 'widget', $assets_path . 'js/widget.js', array( 'bootstrap' ) );

		wp_enqueue_style( 'bootstrap', $assets_path . 'css/bootstrap.css' );
		wp_enqueue_style( 'icd-hosting-register', $assets_path . 'css/icd-hosting-register.css', array(
			'dashicons',
			'install'
		), 1.0 );
	}

	/**
	 * Output registration page header
	 */
	public function icd_hosting_register_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_html_e( 'ICDSoft Registration Wizard', 'icd-hosting' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'jquery' ); ?>
			<?php wp_print_scripts( 'bootstrap' ); ?>
			<?php wp_print_scripts( 'widget' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="icd-hosting-register wp-core-ui">
		<?php
	}

	/**
	 * Output registration page content
	 */
	public function icd_hosting_register_content() {
		?>
		<div class="hosting-widget">
			<form method="post" action="<?php echo icd_hosting_admin_ajax_url(); ?>" class="box inline-error"
				  id="hosting-register-form">
				<?php wp_nonce_field( 'icd-hosting-register' ); ?>
				<input type="hidden" value="" name="token" id="token">
				<input type="hidden" name="action" value="icd_hosting_register_store">
				<div id="hosting-widget-errors"></div>

				<h3> <?php esc_html_e( 'ICDSoft Registration Wizard', 'icd-hosting' ) ?></h3>
				<table>
					<tbody>
					<tr>
						<th><label for="username"> <?php esc_html_e( 'Username', 'icd-hosting' ) ?>: </label></th>
						<td>
							<input type="text" value="" name="username" id="username" data-field="username" class="form-control " maxlength="20" autofocus="">
						</td>
					</tr>
					<tr>
						<th><label for="password"><?php esc_html_e( 'Password', 'icd-hosting' ) ?>: </label></th>
						<td>
							<input type="password" value="" name="password" id="password" data-field="password" class="form-control caps-warn">
						</td>
					</tr>
					<tr>
						<th><label for="full_name"><?php esc_html_e( 'Full Name', 'icd-hosting' ) ?>: </label></th>
						<td>
							<input type="text" value="" name="full_name" id="full_name" data-field="full_name" class="form-control " maxlength="120">
						</td>
					</tr>
					<tr>
						<th><label for="email"><?php esc_html_e( 'Email', 'icd-hosting' ) ?>: </label></th>
						<td>
							<input type="email" value="" name="email" id="email" data-field="email" class="form-control " maxlength="128">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr>
							<h3><?php esc_html_e( 'Store Details', 'icd-hosting' ) ?></h3>
						</td>
					</tr>
					<tr>
						<th><label for="store-name"><?php esc_html_e( 'Store Brand Name', 'icd-hosting' ) ?>:</label>
						</th>
						<td>
							<input type="text" value="" name="store_title" id="store-title" placeholder="e.g. Pro Web Hosting" data-field="store_title" class="form-control " maxlength="128">
						</td>
					</tr>
					<tr>
						<th><label for="store-title"><?php esc_html_e( 'Store Slug', 'icd-hosting' ) ?>:</label></th>
						<td>
							<input type="text" value="" name="store_name" id="store-name" placeholder="e.g. pro-web-hosting" data-field="store_name" class="form-control " maxlength="128">
						</td>
					</tr>
					<tr>
						<th><label for="store-currency"><?php esc_html_e( 'Currency', 'icd-hosting' ) ?></label></th>
						<td>
							<select id="store-currency" name="store_currency" required class="form-control ">
								<option value="USD"><?php esc_html_e( 'USD' ); ?></option>
								<option value="EUR"><?php esc_html_e( 'EUR' ); ?></option>
								<option value="HKD"><?php esc_html_e( 'HKD' ); ?></option>
								<option value="BGN"><?php esc_html_e( 'BGN' ); ?></option>
								<option value="CAD"><?php esc_html_e( 'CAD' ); ?></option>
								<option value="AUD"><?php esc_html_e( 'AUD' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label>
								<input type="checkbox" name="terms" value="1" id="terms" data-field="terms" checked="checked">
								<span class="small"><?php esc_html_e( 'I have read and will abide by the', 'icd-hosting' ) ?>
									<a href="https://www.icdsoft.com/en/reseller/terms"	target="_blank"> <?php esc_html_e( 'Terms of Use', 'icd-hosting' ) ?> </a>
								</span>
							</label>

							<label class="error hide" for="terms"></label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label>
								<input type="checkbox" name="news_subscribe" value="1" data-field="news_subscribe"
									   checked="checked">
								<span class="small"><?php esc_html_e( 'I want to receive news and information about products and services offered by ICDSoft', 'icd-hosting' ) ?> </span>
							</label>
						</td>
					</tr>
					</tbody>
					<tfoot>
					<tr>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=icd-hosting-welcome' ) ) ?>"
							   class="button btn-sm small pull-left"><?php esc_html_e( 'Back', 'icd-hosting' ) ?></a>
						</td>
						<td>
							<button type="submit" id="register-submit" class="button-primary button button-large button-next pull-right" ><?php esc_html_e( 'Create new account', 'icd-hosting' ) ?></button>
						</td>
					</tr>
					</tfoot>
				</table>
			</form>
		</div>
		<?php
	}

	/**
	 * Output registration page footer
	 */
	public function icd_hosting_register_footer() {
		?>
		<script>
			$ = jQuery = window.jQuery.noConflict(true);
			$(function () {
				RegisterForm.init([]);
			});
		</script>
		</body>
		</html>
		<?php
	}
}

new ICD_Hosting_Admin_Setup_Wizard();
