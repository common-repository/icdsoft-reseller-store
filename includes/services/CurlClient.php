<?php

namespace ICD\Hosting\Services;

use http\Client\Response;

/**
 * Class CurlClient
 * @package ICD\Hosting\Services
 */
class CurlClient {
	/**
	 * cURL handle
	 */
	protected $ch;

	/**
	 * API endpoint
	 *
	 * @var string
	 */
	protected $apiURL = '';

	/**
	 * Cache directory
	 *
	 * @var string
	 */
	protected $cacheDir = '';

	/**
	 * Debug file
	 *
	 * @var string
	 */
	protected $debugFile = '';

	/**
	 * Log file
	 *
	 * @var string
	 */
	protected $logFile = '';

	/**
	 * HTTP Method
	 *
	 * @var string
	 */
	protected $httpMethod = 'GET';

	/**
	 * @var array
	 */
	protected $supportedHttpMethods = array( 'GET', 'POST' );

	/**
	 * Headers
	 *
	 * @var array
	 */
	protected $headers = [];

	protected $responses;
	/**
	 * CurlClient constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		$this->apiURL = rtrim( $settings['url'], '/' ) . '/';

		if ( ! empty( $settings['cache'] ) ) {
			$this->cacheDir = $settings['cache'];
		}

		if ( ! empty( $settings['debug'] ) ) {
			$this->debugFile = $settings['debug'];
		}

		if ( ! empty( $settings['log'] ) ) {
			$this->logFile = $settings['log'];
		}
	}

	/**
	 * Handle GET requests
	 *
	 * @param string $cmd
	 * @param array $params
	 *
	 * @return array|bool|mixed|object|string
	 */
	public function get( $cmd = '', $params = array() ) {
		$this->httpMethod = 'GET';
		$params           = $this->trimParams( $params );

		return $this->execute( $cmd, $params );
	}

	/**
	 * Handle POST requests
	 *
	 * @param string $cmd
	 * @param array $params
	 *
	 * @return array|bool|mixed|object|string
	 */
	public function post( $cmd = '', $params = array() ) {
		$this->httpMethod = 'POST';
		$params           = $this->trimParams( $params );

		return $this->execute( $cmd, $params );
	}

	/**
	 * @return string
	 */
	public function getHttpMethod() {
		return $this->httpMethod;
	}

	/**
	 * @param $method
	 */
	public function setHttpMethod( $method ) {
		$this->httpMethod = $method;
	}

	/**
	 * Set Headers
	 *
	 * @param $header
	 */
	public function addHeader($header) {
		if (is_string($header) && !empty($header)) {
			$this->headers[] = $header;
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
		if ( isset( $this->responses[ $this->cmdHash( $cmd, $params ) ] ) ) {
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
		if ( $this->hasValidResponse( $cmd, $params ) and $this->cacheDir ) {
			return true;
		}

		return false;
	}

	/**
	 * Create HTTP client.
	 */
	protected function initClient() {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		// Speed up big responses
		curl_setopt( $ch, CURLOPT_ENCODING, 'gzip' );

		// Set HTTP method.
		if ( $this->httpMethod === 'POST' ) {
			curl_setopt( $ch, CURLOPT_POST, true );
		}

		// Set connection timeouts.
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );

		// Set SSL options.
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );

		// Setup cURL to output verbose information when in debug mode and open the log file.
		if ( $this->debugFile ) {
			$this->curlLog = fopen( $this->debugFile, 'a+' );
			curl_setopt( $ch, CURLOPT_VERBOSE, true );
			curl_setopt( $ch, CURLOPT_STDERR, $this->curlLog );
		}

		$this->ch = $ch;
	}

	/**
	 * Create cURL request
	 *
	 * @param $url
	 * @param $params
	 * @param bool $exec
	 *
	 * @return mixed
	 */
	protected function clientRequest( $url, $params, $exec = true ) {
		if ( $this->httpMethod === 'POST' ) {
			curl_setopt( $this->ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
		} else {
			$url .= ( strpos( $url, '?' ) !== false ? '&' : '?' ) . http_build_query( $params );
		}

		if ( $this->logFile ) {
			$log = 'curl -X GET ' . escapeshellarg( $url ) . ' -k -g';
			if ( $this->httpMethod == 'POST' ) {
				$json = defined( 'JSON_PRETTY_PRINT' ) ? json_encode( $params, JSON_PRETTY_PRINT ) : json_encode( $params );
				$log  = 'curl -X POST ' . escapeshellarg( $url ) . " -H 'Content-Type: application/json' -k -g -d \\\n" . escapeshellarg( $json );
			}
			file_put_contents( $this->logFile, gmdate( 'Y-m-d H:i:s' ) . " UTC\n" . $log . "\n\n", FILE_APPEND );
		}

		if (!empty($this->headers)) {
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
		}

		curl_setopt( $this->ch, CURLOPT_URL, $url );
		if ( $exec ) {
			$response = curl_exec( $this->ch );

			return $response;
		}
	}

	/**
	 * Close a cURL session
	 */
	protected function closeClient() {
		// Close the log file handle and then append summary info about the last request.
		if ( $this->debugFile ) {
			fclose( $this->curlLog );
			file_put_contents( $this->debugFile, print_r( curl_getinfo( $this->ch ), true ), FILE_APPEND );
		}

		// Close cURL handle.
		curl_close( $this->ch );
	}

	/**
	 * Handle before actions
	 *
	 * @param $cmd
	 * @param $params
	 */
	protected function beforeRequest( &$cmd, &$params ) {
	}

	/**
	 * Init cURL client and execute request
	 *
	 * @param string $cmd
	 * @param array $params
	 *
	 * @return mixed
	 */
	protected function execute( $cmd = '', $params = array() ) {
		// Hook for extending classes
		$this->beforeRequest( $cmd, $params );

		// For subsequent calls after a successful call return the parsed response directly.
		if ( $response = $this->hasValidResponse( $cmd, $params ) ) {
			return $response;
		}
		// If the command's output is cacheable try to retrieve the response from the local file cache.
		if ( $response = $this->getFromCache( $cmd, $params ) ) {
			return $response;
		}

		// Init cURL.
		$this->initClient();

		// Build API command URL.
		$url = $this->apiURL . $cmd;


		// Make the request.
		$response = $this->responses[ $this->cmdHash( $cmd, $params ) ] = $this->clientRequest( $url, $params );
		if ( curl_getinfo( $this->ch, CURLINFO_CONTENT_TYPE ) == 'application/json' ) {
			$response = $this->responses[ $this->cmdHash( $cmd, $params ) ] = json_decode( $response, true );
		}

		if ( $this->isCachable( $cmd, $params ) ) {
			$this->saveToCache( $cmd, $params, $response );
		}

		$this->headers = [];

		return $response;
	}

	/**
	 * Execute multiple API calls
	 *
	 * @param $commands
	 *
	 * @return array
	 */
	protected function executeMulti( $commands ) {
		$list   = [];
		$result = [];
		$multi  = curl_multi_init();

		foreach ( $commands as $k => $details ) {
			if ( ! isset( $details['params'] ) ) {
				$commands[ $k ]['params'] = $details['params'] = [];
			}
			$this->beforeRequest( $details['command'], $details['params'] );

			// For subsequent calls after a successful call return the parsed response directly.
			if ( $response = $this->hasValidResponse( $details['command'], $details['params'] ) ) {
				$result[ $k ] = $response;
				continue;
			}

			// If the command's output is cacheable try to retrieve the response from the local file cache.
			if ( $response = $this->getFromCache( $details['command'], $details['params'] ) ) {
				$result[ $k ] = $response;
				continue;
			}

			if ( isset( $details['method'] ) and in_array( $details['method'], $this->supportedHttpMethods ) ) {
				$this->httpMethod = $details['method'];
			}

			$commands[ $k ] = $details;

			$this->initClient();

			$this->clientRequest( $this->apiURL . $details['command'], $details['params'], false );

			$list[ $k ] = $this->ch;

			curl_multi_add_handle( $multi, $list[ $k ] );
		}

		do {
			curl_multi_exec( $multi, $running );
			curl_multi_select( $multi );
			usleep( 100 );
		} while ( $running > 0 );

		foreach ( $list as $k => $curl ) {
			$response = curl_multi_getcontent( $curl );
			if ( curl_getinfo( $curl, CURLINFO_CONTENT_TYPE ) == 'application/json' ) {
				$response = json_decode( $response, true );
			}

			$this->responses[ $this->cmdHash( $commands[ $k ]['command'], $commands[ $k ]['params'] ) ] = $result[ $k ] = $response;

			if ( $this->isCachable( $commands[ $k ]['command'], $commands[ $k ]['params'] ) ) {
				$this->saveToCache( $commands[ $k ]['command'], $commands[ $k ]['params'], $response );
			}

			curl_multi_remove_handle( $multi, $curl );
		}

		$this->headers = [];

		curl_multi_close( $multi );

		return $result;
	}

	/**
	 * Save response to cache
	 *
	 * @param $cmd
	 * @param $params
	 * @param $response
	 * @param int $TTL
	 */
	protected function saveToCache( $cmd, $params, $response, $TTL = 3600 ) {
		$cache = $this->getCacheFilePath( $cmd, $params );
		file_put_contents( $cache, json_encode( $response ) );
		if ( (int) $TTL > 0 ) {
			touch( $cache, time() + $TTL );
		}
	}

	/**
	 * Get cached responses for concrete API call
	 *
	 * @param $cmd
	 * @param $params
	 *
	 * @return array|mixed|object
	 */
	protected function getFromCache( $cmd, $params ) {
		$cache = $this->getCacheFilePath( $cmd, $params );
		if ( file_exists( $cache ) && filemtime( $cache ) > time() ) {
			$response = $this->responses[ $this->cmdHash( $cmd, $params ) ] = json_decode( file_get_contents( $cache ), true );

			return $response;
		}
	}

	/**
	 * Generate hash
	 *
	 * @param $cmd
	 * @param $params
	 *
	 * @return string
	 */
	protected function cmdHash( $cmd, $params ) {
		return $cmd . '_' . md5( http_build_query( $params ) );
	}

	/**
	 * Cache file path
	 *
	 * @param $cmd
	 * @param $params
	 *
	 * @return string
	 */
	protected function getCacheFilePath( $cmd, $params ) {
		$cmd  = empty( $cmd ) ? 'UNKNOWN' : $cmd;
		$path = $this->cacheDir . '/' . $this->cmdHash( $cmd, $params );

		return $path;
	}

	/**
	 * Sanitize params
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	protected function trimParams( $params ) {
		foreach ( $params as $k => $v ) {
			$params[ $k ] = is_array( $v ) ? $this->trimParams( $v ) : trim( $v );
		}

		return $params;
	}
}
