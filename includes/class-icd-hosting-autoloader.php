<?php

namespace ICD\Hosting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ICD_Hosting_Autoloader {

	private $include_path = '';

	public function __construct() {

		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( ICD_HOSTING_PLUGIN_PATH ) ) . '/includes/';
	}

	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );

			return true;
		}

		return false;
	}

	public function autoload( $class ) {
		if ( strpos( $class, 'ICD_Hosting' ) === 0 ) {
			$class = strtolower( $class );
			//var_dump( $class );
			$file = $this->get_file_name_from_class( $class );
			//var_dump( $file );
			$path = '';

			if ( strpos( $class, 'ICD_Hosting_Admin' ) === 0 ) {
				$path = $this->include_path . 'admin/' . substr( str_replace( '_', '-', $class ), 18 ) . '/';
			}

			//var_dump( $path );
			//var_dump( $file );
			if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'ICD_Hosting' ) === 0 ) ) {
				$this->load_file( $this->include_path . $file );
			}
		}
	}
}

new ICD_Hosting_Autoloader();
