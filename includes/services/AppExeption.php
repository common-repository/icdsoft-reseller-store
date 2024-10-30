<?php

namespace ICD\Hosting\Services;

use Exception;

/**
 * Class AppException handle errors
 *
 * @package ICD\Hosting\Services
 */
class AppException extends Exception {

	/**
	 * Stores errors
	 *
	 * @var array
	 */
	protected static $errors;

	/**
	 * AppException constructor.
	 *
	 * @param null $msg
	 */
	public function __construct( $msg = null ) {
		if ( ! is_null( $msg ) ) {
			static::error( $msg );
		}

		$err = empty( static::$errors ) ? 'error.generic' : static::$errors[ count( static::$errors ) - 1 ]['message'];
		parent::__construct( $err );
	}

	/**
	 * @param $msg
	 */
	public static function error( $msg ) {
		$msg              = is_array( $msg ) ? array( 'type' => 'error' ) + $msg : array( 'type'    => 'error',
		                                                                                  'message' => $msg
		);
		static::$errors[] = $msg;
	}

	/**
	 * @return bool
	 */
	public static function hasErrors() {
		return static::$errors ? true : false;
	}

	/**
	 * @return mixed
	 */
	public static function getErrors() {
		return static::$errors;
	}
}
