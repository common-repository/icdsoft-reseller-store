<?php
/**
 * ICD Hosting   Uninstall
 *
 * Uninstalling ICD Hosting deletes pages and options.
 *
 * @version     1.0.0
 */


/**
 * If uninstall.php is not called by WordPress, die.
 * The constant is NOT defined when uninstall is performed by register_uninstall_hook().
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
global $wpdb;


// Pages.
wp_trash_post( get_option( 'icd_hosting_domaincheck_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_hostingorder_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_thankyou_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_terms_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_payment_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_postback_page_id' ) );
wp_trash_post( get_option( 'icd_hosting_request_page_id' ) );

// Delete options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'icd_hosting%';" );

// Clear any cached data that has been removed
wp_cache_flush();