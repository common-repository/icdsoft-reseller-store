<?php

namespace ICD\Hosting;

use ICD\Hosting\Services\ApiClient;
use ICD\Hosting\Services\Payment;
use ICD\Hosting\Services\Translate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ICD_Hosting_App
 *
 * @package ICD\Hosting
 */
class ICD_Hosting_App {
	/**
	 * icd-hosting config settings
	 *
	 * @see ICD_Hosting_Config
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * Handles curl requests
	 *
	 * @see \ICD\Hosting\CurlClient
	 * @see \ICD\Hosting\ApiClient
	 *
	 * @var object
	 */
	public $api;

	/**
	 * Handle translations
	 *
	 * @see \ICD\Hosting\Translate
	 *
	 * @var object
	 */
	public $trans;

	/**
	 * @var
	 */
	public $payment;

	/**
	 * ICD_Hosting_App constructor.
	 *
	 * @param $settings
	 * @param $locale
	 * @param $app_dir
	 */
	public function __construct( $settings, $locale, $app_dir ) {
		$this->settings = $settings;

		$this->setApi();
		$this->setPayment();
		$this->setTrans( $locale, $app_dir );
	}

	/**
	 * @param $name
	 * @param null $value
	 *
	 * @return |null
	 */
	public function config( $name, $value = null ) {
		if ( is_array( $name ) ) {
			if ( true === $value ) {
				$this->settings = array_merge_recursive( $this->settings, $name );
			} else {
				$this->settings = array_merge( $this->settings, $name );
			}
		} elseif ( func_num_args() === 1 ) {
			return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
		} else {
			$settings          = $this->settings;
			$settings[ $name ] = $value;
			$this->settings    = $settings;
		}
	}

	/**
	 * Initialize ApiClient
	 */
	public function setApi() {
		$this->api = new ApiClient( $this->settings['api'] );
	}

	/**
	 * Initialize translations for given locale
	 *
	 * @param $locale
	 * @param $app_dir
	 */
	public function setTrans( $locale, $app_dir ) {
		$custom_lang_path = $app_dir . '/custom/language';

		$upload_dir  = wp_get_upload_dir();
		$upload_path = untrailingslashit( wp_normalize_path( $upload_dir['basedir'] ) ) . '/icd-hosting';

		if ( file_exists( $upload_path ) ) {
			$custom_lang_path = $upload_path;
		}

		$this->trans = new Translate( $locale, $app_dir . '/language',  $custom_lang_path );
	}

	/**
	 * Translation getter
	 *
	 * @return mixed
	 */
	public function getTrans() {
		return $this->trans;
	}

	/**
	 * Set payment processor for current store
	 *
	 * @see \ICD\Hosting\Payment
	 */
	public function setPayment() {
		if ( empty( $this->payment ) ) {
			$settings   = array_merge( $this->settings['payment'], array( 'processors' => array() ) );
			$processors = $this->api->get( 'payment-processors', array( 'test' => (int) $this->settings['payment']['test_mode'] ) );
			if ( ! empty( $processors['data']['processors'] ) ) {
				foreach ( $processors['data']['processors'] as $p ) {
					$settings['processors'][ $p['processor_id'] ] = $p;
				}
			}

			$this->payment = new Payment( $settings );
		}
	}
}
