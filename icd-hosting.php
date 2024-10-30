<?php
/**
 * Plugin Name: ICDSoft Reseller Store
 * Plugin URI: https://www.icdsoft.com/reseller
 * Description: This plugin allows you to add hosting order forms on your website, accept hosting service orders and payments from your customers, and then  have these orders opened on servers hosted by ICDSoft.
 * Version: 2.4.5
 * Author: ICDSoft Hosting
 * Author URI: https://icdsoft.com
 *
 * Text Domain: icd-hosting
 * Domain Path: /lang/language/
 */

namespace ICD\Hosting;

use ICD\Hosting\Services\DomainService;
use ICD\Hosting\Services\OrderService;
use ICD\Hosting\Services\RegistrationService;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ICD_Hosting' ) ) :

/**
 * Main ICD Hosting class
 *
 * @class ICD_Hosting
 */
final class ICD_Hosting {

	/**
	 * ICD Hosting version
	 *
	 * @var string
	 */
	public $version = '2.4.5';

	/**
	 * ICD Hosting app instance
	 *
	 * @var ICD_Hosting_App
	 */
	public $app = null;

	/**
	 * Domain Service instance
	 *
	 * @var DomainService
	 */
	public $domain_service = null;

	/**
	 * Order Service instance
	 *
	 * @var OrderService
	 */
	public $order_service = null;

	/**
	 * Registration service instance
	 *
	 * @var RegistrationService
	 */
	public $registration_service = null;

	/**
	 * The single instance of the main class.
	 *
	 * @var ICD_Hosting
	 */
	protected static $_instance = null;

	/**
	 * Main ICD_Hosting Instance.
	 *
	 * @return ICD_Hosting - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * ICD_Hosting constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Initial hooks
	 */
	private function init_hooks() {
		// activation and deactivation hooks
		register_activation_hook( __FILE__, array( 'ICD\Hosting\ICD_Hosting_Install', 'install' ) );
		register_deactivation_hook( __FILE__, array( 'ICD\Hosting\ICD_Hosting_Install', 'deactivate' ) );

		// Init hook and shortcodes
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'ICD\Hosting\ICD_Hosting_Shortcodes', 'init' ) );

		// Exclude pages from frontend menu
		add_filter( 'get_pages', 'icd_hosting_exclude_pages' );

		// Exclude pages from WordPress Search
		add_filter( 'pre_get_posts', 'icd_hosting_search_filter' );
	}

	/**
	 * Define ICD Hosting Constants.
	 */
	private function define_constants() {
		define( 'ICD_HOSTING_VERSION', $this->version );
		define( 'ICD_HOSTING_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'ICD_HOSTING_PLUGIN_FILE', __FILE__ );
		define( 'ICD_HOSTING_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'ICD_HOSTING_PLUGIN_NAME', 'icd_hosting' );

		define( 'WIDGET_VERSION', '4.28' );
	}

	/**
	 * Include required files used in admin and on the frontend.
	 */
	private function includes() {
		// Core
		include_once( 'includes/class-icd-hosting-autoloader.php' );
		include_once( 'includes/icd-hosting-core-functions.php' );
		include_once( 'includes/class-icd-hosting-install.php' );
		include_once( 'includes/class-icd-hosting-config.php' );
		include_once( 'includes/class-icd-hosting-app.php' );

		// Shortcodes
		include_once( 'includes/class-icd-hosting-shortcodes.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-domain-check.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-hosting-order.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-thankyou.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-terms.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-payment.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-request.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-postback.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-plan-info.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-compare-plans.php' );
		include_once( 'includes/shortcodes/class-icd-hosting-shortcode-certificates.php' );

		// Frontend script, ajax handler, query params
		include_once( 'includes/class-icd-hosting-frontend-scripts.php' );
		include_once( 'includes/class-icd-hosting-ajax.php' );
		include_once( 'includes/class-icd-hosting-query.php' );

		// Admin pages
		if ( is_admin() ) {
			include_once( 'includes/admin/class-icd-hosting-admin.php' );
			include_once( 'includes/admin/class-icd-hosting-admin-notices.php' );
		}

		// Services
		include_once( 'includes/services/OrderStatusEnum.php' );
		include_once( 'includes/services/CurlClient.php' );
		include_once( 'includes/services/ApiClient.php' );
		include_once( 'includes/services/AppExeption.php' );
		include_once( 'includes/services/Payment.php' );
		include_once( 'includes/services/OrderDTO.php' );
		include_once( 'includes/services/OrderHelper.php' );
		include_once( 'includes/services/TreeHelper.php' );
		include_once( 'includes/services/CatalogHelper.php' );
		include_once( 'includes/services/Translate.php' );
		include_once( 'includes/services/helpers.php' );
		include_once( 'includes/services/Service.php' );
		include_once( 'includes/services/DomainService.php' );
		include_once( 'includes/services/RegistrationService.php' );
		include_once( 'includes/services/OrderService.php' );
		include_once( 'includes/services/Markdown.php' );
		include_once( 'includes/services/processors/PaymentProcessor.php' );
		include_once( 'includes/services/processors/Payment2CheckOut.php' );
		include_once( 'includes/services/processors/PaymentAuthorizeNet.php' );
		include_once( 'includes/services/processors/PaymentBank.php' );
		include_once( 'includes/services/processors/PaymentCash.php' );
		include_once( 'includes/services/processors/PaymentCheck.php' );
		include_once( 'includes/services/processors/PaymentEpay.php' );
		include_once( 'includes/services/processors/PaymentOffline.php' );
		include_once( 'includes/services/processors/PaymentPayDollar.php' );
		include_once( 'includes/services/processors/PaymentPayPal.php' );
		include_once( 'includes/services/processors/PaymentPayU.php' );
		include_once( 'includes/services/processors/PaymentSkrill.php' );
		include_once( 'includes/services/processors/PaymentStripe.php' );
		include_once( 'includes/services/processors/PaymentBraintree.php' );
		include_once( 'includes/services/processors/PaymentWesternUnion.php' );
	}

	/**
	 * Init ICD Hosting when WordPress Initialises.
	 */
	public function init() {
		ob_start();
		$this->load_plugin_textdomain();

		$config = ICD_Hosting_Config::get_config();
		if ( empty( $this->app ) ) {
			$this->app                  = new ICD_Hosting_App( $config, $config['locale'], $this->plugin_path() . '/lang' );
			$this->domain_service       = new DomainService( $config['api'] );
			$this->registration_service = new RegistrationService( $config['api'] );
			$this->order_service        = new OrderService( $config['api'], $this->app->payment );
		}
	}

	/**
	 * @return ICD_Hosting_App
	 */
	public function getApp() {
		//$this->init();
		return $this->app;
	}

	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'icd-hosting' );

		unload_textdomain( 'icd-hosting' );
		load_textdomain( 'ICD_Hosting', WP_LANG_DIR . '/icd-hosting/icd-hosting-' . $locale . '.mo' );
		load_plugin_textdomain( 'ICD_Hosting', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return 'icd-hosting/';
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Load stripe library
	 */
	public function load_stripe_lib () {
		if ( !class_exists( '\Stripe\Stripe' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'includes/libraries/stripe/stripe-php/init.php' );
		}
	}

	/**
	 * Load braintree library
	 */
	public function load_braintree_lib () {
		if ( !class_exists( '\Braintree' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'includes/libraries/lib/autoload.php' );
		}
	}
}

endif;

/**
 * @return ICD_Hosting instance
 */
function ICD_Hosting() {
	return ICD_Hosting::instance();
}

// Start it
ICD_Hosting();
