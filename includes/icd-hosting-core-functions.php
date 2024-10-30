<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( 'icd-hosting-page-functions.php' );

/**
 * Get templates, passing arguments and including the file.
 *
 * @param $template_name
 * @param array $args
 * @param string $template_path
 * @param string $default_path
 */
function icd_hosting_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	if ( strcmp( substr( $template_name, - 4 ), '.php' ) !== 0 ) {
		$template_name .= '.php';
	}
	$located = icd_hosting_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		return;
	}

	include( $located );
}

/**
 * Display template html
 *
 * @param $template_name
 * @param array $args
 * @param string $template_path
 * @param string $default_path
 *
 * @return false|string
 */
function icd_hosting_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	icd_hosting_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}

/**
 * Retrieve the name of the template file that exists with default plugin path folder.
 *
 * @param $template_name
 * @param string $template_path
 * @param string $default_path
 *
 * @return string
 */
function icd_hosting_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = \ICD\Hosting\ICD_Hosting()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = \ICD\Hosting\ICD_Hosting()->plugin_path() . '/templates/';
	}

	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	return $template;
}

/**
 * Send a JSON response back to an Ajax request.
 *
 * @param $data
 */
function icd_hosting_json_out( $data ) {
	if ( ! empty( $data['messages'] ) ) {
		$data['messages'] = icd_hosting_translate_messages( $data['messages'] );
	}

	header( "Content-Type: application/json" );
	wp_send_json( $data );
	wp_die();
}

/**
 * Send a JSON response with status and error message
 *
 * @param string $msg
 */
function icd_hosting_ajax_error( $msg = 'error.generic' ) {
	if ( ! $msg instanceof \ICD\Hosting\Services\AppException ) {
		 \ICD\Hosting\Services\AppException::error( $msg );
	}

	icd_hosting_json_out( array( 'status' => false, 'messages' => \ICD\Hosting\Services\AppException::getErrors() ) );
}

/**
 * Display AppException errors
 *
 * @param string $msg
 */
function icd_hosting_display_error( $msg = 'error.generic' ) {
	if ( ! $msg instanceof \ICD\Hosting\Services\AppException ) {
		 \ICD\Hosting\Services\AppException::error( $msg );
	}

	$viewdata['errors'] = \ICD\Hosting\Services\AppException::getErrors();
	icd_hosting_get_template( 'error', $viewdata );
}

/**
 * Translate messages
 *
 * @param $messages
 *
 * @return mixed
 */
function icd_hosting_translate_messages( $messages ) {
	foreach ( $messages as $key => $message ) {
		$messages[ $key ]['message'] = icd_hosting_tr( $message['message'], isset( $message['params'] ) ? $message['params'] : array() );
		unset( $messages[ $key ]['params'] );
	}

	return $messages;
}

/**
 * Retrieves the URL to the admin area.
 *
 * @return string Admin URL link.
 */
function icd_hosting_admin_ajax_url() {
	return admin_url( 'admin-ajax.php' );
}
