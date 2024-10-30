<?php

namespace ICD\Hosting\Services;

/**
 * Class Translate
 *
 * @package ICD\Hosting\Services
 */
class Translate {

	/**
	 * Instance of Translate class
	 *
	 * @var Translate
	 */
	private static $instance;

	/**
	 * Translation labels
	 *
	 * @var array
	 */
	private static $labels = array();

	/**
	 * Custom translation labels
	 *
	 * @var array
	 */
	private static $custom_labels = array();

	/**
	 * Translation language
	 *
	 * @var string
	 */
	private static $lang = 'en';

	/**
	 * Language path
	 *
	 * @var string
	 */
	private static $lang_path = '';

	/**
	 * Custom language path
	 *
	 * @var string
	 */
	private static $custom_lang_path = '';

	/**
	 * Default language
	 */
	const DEFAULT_LANG = 'en';

	/**
	 * Translate constructor
	 *
	 * @param mixed $lang requested language
	 * @param mixed $lang_path path to translation files
	 */
	public function __construct( $lang, $lang_path, $custom_lang_path ) {
		self::setLang( $lang );
		self::setLangPath( $lang_path );
		self::setCustomLangPath( $custom_lang_path );
	}

	/**
	 * Translate given key string
	 *
	 * @param $key
	 * @param $params
	 * @param null $language
	 *
	 * @return string
	 */
	public static function trans( $key, $params, $language = null ) {
		if ( self::$instance == null ) {
			self::$instance = new Translate( self::$lang, self::$lang_path, self::$custom_lang_path );
		}

		return self::$instance->tr( $key, $params, $langugage = null );
	}

	/**
	 * Set translation language
	 *
	 * @param $language
	 */
	public function setLang( $language ) {
		self::$lang = $language;
	}

	/**
	 * Get translation language
	 *
	 * @return string
	 */
	public static function getLang() {
		return self::$lang;
	}

	/**
	 * Set translation path
	 *
	 * @param $lang_path
	 */
	public function setLangPath( $lang_path ) {
		self::$lang_path = $lang_path;
	}

	/**
	 * Set custom translation path
	 *
	 * @param $lang_path
	 */
	public function setCustomLangPath( $lang_path ) {
		self::$custom_lang_path = $lang_path;
	}

	/**
	 * Get labels
	 *
	 * @param null $language
	 * @param null $key
	 *
	 * @return array|mixed
	 */
	public static function getLabels( $language = null, $key = null ) {
		if ( empty( $language ) ) {
			return self::$labels;
		}

		if ( empty( self::$labels[ $language ] ) ) {
			return self::$labels[ self::DEFAULT_LANG ];
		}
	}

	/**
	 * Find translation key
	 *
	 * @param $key
	 * @param null $language
	 *
	 * @return array|string
	 */
	public function find_key( $key, $language = null ) {
		if ( is_null( $language ) ) {
			$language = self::$lang;
		}

		if ( empty( self::$labels[ $language ] ) ) {
			$this->loadTranslations( $language );
		}

		$tr = '';
		if ( ! empty( self::$custom_labels[ $language ] ) ) {
			$tr = $this->dot_path( $key, self::$custom_labels[ $language ] );
		}

		return $tr ? $tr : $this->dot_path( $key, self::$labels[ $language ] );
	}

	/**
	 * Translate key string
	 *
	 * @param $key
	 * @param null $params
	 * @param null $language
	 *
	 * @return string
	 */
	public function tr( $key, $params = null, $language = null ) {
		$str = $this->find_key( $key, $language );

		if ( ! $str ) {
			return $key;
		}

		if ( ! $params ) {
			return $str;
		}

		foreach ( $params as $k => $v ) {
			$params["%$k%"] = $v;
			unset( $params[ $k ] );
		}

		return str_replace( array_keys( $params ), array_values( $params ), $str );
	}

	/**
	 * Translate upon choice (int)
	 *
	 * @param $key
	 * @param int $choice
	 * @param null $params
	 * @param null $language
	 *
	 * @return string
	 */
	public function tr_choice( $key, $choice = 1, $params = null, $language = null ) {
		$choice = (int) $choice;
		$str    = $this->find_key( $key, $language );

		if ( empty( $str ) ) {
			return $key;
		}

		$str_forms = explode( '|', $str );
		$str       = isset( $str_forms[ $choice - 1 ] ) ? $str_forms[ $choice - 1 ] : $str_forms[0];
		if ( is_null( $params ) ) {
			return $str;
		}

		foreach ( $params as $name => $value ) {
			$str = str_replace( "%{$name}%", $value, $str );
		}

		return $str;
	}

	/**
	 * Translate key string, return default if not found
	 *
	 * @param $key
	 * @param null $default
	 * @param null $params
	 * @param null $language
	 *
	 * @return string|null
	 */
	public function tr_empty( $key, $default = null, $params = null, $language = null ) {
		if ( $this->find_key( $key, $language ) ) {
			return $this->tr( $key, $params, $language );
		}

		return $default;
	}

	/**
	 * Load new translations
	 *
	 * @param $extra
	 */
	public function overload( $extra ) {
		if ( empty( self::$labels[ self::$lang ] ) ) {
			$this->loadTranslations( self::$lang );
		}

		$lang_path        = self::$lang_path;
		$custom_lang_path = self::$custom_lang_path;

		self::$lang_path        = self::$lang_path . '/' . $extra;
		self::$custom_lang_path = self::$custom_lang_path . '/' . $extra;

		$this->loadTranslations( self::$lang );

		self::$lang_path        = $lang_path;
		self::$custom_lang_path = $custom_lang_path;
	}

	/**
	 * Load custom translations
	 *
	 * @param $language
	 */
	private function loadCustomTranslations( $language ) {
		if ( is_file( self::$custom_lang_path . "/{$language}.php" ) ) {
			if ( ! isset( self::$custom_labels[ $language ] ) ) {
				self::$custom_labels[ $language ] = array();
			}

			self::$custom_labels[ $language ] = array_merge( self::$custom_labels[ $language ], require_once( self::$custom_lang_path . "/{$language}.php" ) );

			return;
		}
	}

	/**
	 * Load translations
	 *
	 * @param $language
	 */
	private function loadTranslations( $language ) {
		self::loadCustomTranslations( $language );

		if ( ! isset( self::$labels[ $language ] ) ) {
			self::$labels[ $language ] = array();
		}

		if ( is_file( self::$lang_path . "/{$language}.php" ) ) {
			self::$labels[ $language ] = array_merge( self::$labels[ $language ], require_once( self::$lang_path . "/{$language}.php" ) );

			return;
		}
		if ( is_file( self::$lang_path . '/' . self::DEFAULT_LANG . '.php' ) ) {
			self::$labels[ $language ] = array_merge( self::$labels[ $language ], require_once( self::$lang_path . '/' . self::DEFAULT_LANG . '.php' ) );

			return;
		}

		throw new Exception( 'Cannot locate language file' );
	}

	/**
	 * Return data if exist in path. Dot notation can be used.
	 *
	 * @param $path
	 * @param $data
	 *
	 * @return array|string
	 */
	private function dot_path( $path, $data ) {
		if ( ! is_array( $data ) ) {
			return $path;
		}

		if ( isset( $data[ $path ] ) ) {
			return $data[ $path ];
		}

		$pos = explode( '.', $path );
		foreach ( $pos as $key ) {
			if ( is_array( $data ) and ! isset( $data[ $key ] ) ) {
				return;
			}

			if ( is_array( $data ) and isset( $data[ $key ] ) ) {
				$data = $data[ $key ];
			}
		}

		return $data;
	}
}
