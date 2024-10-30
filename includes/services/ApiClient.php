<?php

namespace ICD\Hosting\Services;

/**
 * Class ApiClient handles cURL requests
 *
 * @package ICD\Hosting\Services
 */
class ApiClient extends CurlClient {

	/**
	 * API Authentication Key
	 *
	 * @var string
	 */
	protected $apiKey = '';

	/**
	 * API HMAC Secret
	 *
	 * @var string
	 */
	protected $apiSec = '';

	/**
	 * Extra parameters form settings
	 *
	 * @var array
	 */
	protected $extra_params = array();

	/**
	 * ApiClient constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );

		if ( isset( $settings['extra'] ) ) {
			$this->extra_params = $settings['extra'];
		}

		$this->apiKey = $settings['key'];

		if ( ! empty( $settings['sec'] ) ) {
			$this->apiSec = $settings['sec'];
		}

		if ( ! empty( $settings['cache'] ) ) {
			$cache_path = ICD_HOSTING_PLUGIN_PATH   . 'storage/cache';

			if ( ! file_exists( $cache_path ) ) {
				@wp_mkdir_p( $cache_path );
			}

			if (!is_dir($cache_path) or !is_writable($cache_path)) {
				$cache_path = '';
			}
			$this->cacheDir = $cache_path;
		}
	}

	/**
	 * Check for valid API response
	 *
	 * @param $cmd
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function hasValidResponse( $cmd, $params = array() ) {
		if ( ! empty( $this->responses[ $this->cmdHash( $cmd, $params ) ]['status'] ) ) {
			return $this->responses[ $this->cmdHash( $cmd, $params ) ];
		}
	}

	/**
	 * Check if response can be cached
	 *
	 * @param $cmd
	 * @param array $params
	 *
	 * @return bool
	 */
	public function isCachable( $cmd, $params = array() ) {
		if ( $this->hasValidResponse( $cmd, $params ) and $this->responses[ $this->cmdHash( $cmd, $params ) ]['ttl'] > 0 and $this->cacheDir ) {
			return true;
		}

		return false;
	}


	/**
	 * Generate hash upon API signature
	 *
	 * @param $cmd
	 * @param $params
	 *
	 * @return string
	 */
	protected function cmdHash( $cmd, $params ) {
		unset( $params['auth_token'], $params['nonce'], $params['ts'], $params['signature'], $params['widget_version'] );

		return $cmd . '_' . md5( http_build_query( $params ) );
	}

	/**
	 * Send multiple API calls
	 *
	 * @param $commands
	 * @param string $method
	 *
	 * @return array
	 */
	public function commands( $commands, $method = 'GET' ) {
		if ( in_array( $method, $this->supportedHttpMethods ) ) {
			$this->httpMethod = $method;
		}

		$result = parent::executeMulti( $commands );
		foreach ( $result as $k => $v ) {
			if ( ! empty( $v['messages'] ) ) {
				$result[ $k ]['messages'] = $this->mapMessages( $v['messages'] );
			}
		}

		return $result;
	}

	/**
	 * Sign API call before request
	 *
	 * @param $cmd
	 * @param $params
	 */
	protected function beforeRequest( &$cmd, &$params ) {
		$params['auth_token']        = $this->apiKey;
		$params['wp_widget_version'] = WIDGET_VERSION;
		$params['user_ip']           = icd_hosting_get_user_ip();
		$params                      = array_merge( $params, $this->extra_params );

		$this->addHeader( 'X-User-IP: ' . $params['user_ip'] );
		//Add HMAC security params if configured
		$this->signRequest( $cmd, $params );
	}


	/**
	 * HMAC security functions, nonce, timestamp request signing
	 *
	 * @param $cmd
	 * @param $params
	 */
	protected function signRequest( $cmd, &$params ) {
		if ( $this->apiSec ) {
			$params['nonce']     = md5( wp_rand() );
			$params['ts']        = time();
			$params['signature'] = $this->genSignature( $cmd, $params );
		}
	}

	/**
	 * @param $cmd
	 * @param $params
	 *
	 * @return false|string
	 */
	protected function genSignature( $cmd, $params ) {
		unset( $params['signature'] );

		return hash_hmac( 'sha256', $cmd . '?' . http_build_query( $params ), $this->apiSec );
	}

	/**
	 * Make API call
	 *
	 * @param string $cmd
	 * @param array $params
	 *
	 * @return array|bool|mixed|object|string
	 */
	protected function execute( $cmd = '', $params = array() ) {
		$result = parent::execute( $cmd, $params );
		if ( ! empty( $result['messages'] ) ) {
			$result['messages'] = $this->mapMessages( $result['messages'] );
		}

		return $result;
	}

	/**
	 * Parse response messages
	 *
	 * @param $messages
	 *
	 * @return array
	 */
	protected function mapMessages( $messages ) {
		$result = array();
		foreach ( $messages as $k => $v ) {
			//skip duplicate contact errors on item level, because we use a copy of order contacts
			//$v['code'] = str_replace('contact.', '', $v['code']);
			$parts = explode( ':', $v['code'] );
			//type, code, params + message, field, subtype
			$v['message'] = "api_{$v['type']}.{$v['code']}";
			$v['field']   = '';
			$v['subtype'] = '';
			if ( count( $parts ) > 1 ) {
				$v['message'] = implode( '.', $parts );
				$v['field']   = str_replace( '.', '_', $parts[1] ) . ( isset( $v['params']['item_index'] ) ? "_{$v['params']['item_index']}" : '' );
				$v['subtype'] = $parts[0];
			}
			$result[] = $v;
		}

		return $result;
	}
}