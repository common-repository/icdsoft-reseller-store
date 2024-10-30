<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieves ICD Hosting page id for given page.
 *
 * @param $page
 *
 * @return int
 */
function icd_hosting_get_page_id( $page ) {
	$page = get_option( 'icd_hosting_' . $page . '_page_id' );

	return $page ? absint( $page ) : - 1;
}

/**
 * Retrieves the full permalink for given page.
 *
 * @param $page
 *
 * @return false|string
 */
function icd_hosting_get_page_permalink( $page ) {
	$page_id   = icd_hosting_get_page_id( $page );
	$permalink = $page_id ? get_permalink( $page_id ) : get_home_url();

	return $permalink;
}

/**
 * Exclude pages from the menu
 *
 * @param $pages
 *
 * @return mixed
 */
function icd_hosting_exclude_pages( $pages ) {
    if ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) {
		return $pages;
	}

	$icd_hosting_excluded_pages_ids = get_option( 'icd_hosting_excluded_pages_ids' );
	$excluded_ids                  = array_unique( $icd_hosting_excluded_pages_ids );

	foreach ( $pages as $key => &$page ) {
		if ( in_array( $page->ID, $excluded_ids ) ) {
			unset( $pages[ $key ] );
		}
	}

	return $pages;
}

/**
 * Exclude pages from WordPress search
 *
 * @param $query
 *
 * @return mixed
 */
function icd_hosting_search_filter( $query ) {
	if ( $query->is_search && ! is_admin() ) {
		$icd_hosting_excluded_pages_ids = get_option( 'icd_hosting_excluded_pages_ids' );
		$query->set( 'post__not_in', array_merge( array(), $icd_hosting_excluded_pages_ids ) );
	}

	return $query;
}
