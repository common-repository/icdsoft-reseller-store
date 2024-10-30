<?php
/**
 * Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated icd-hosting-message">
	<a class="notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'icd-hosting-hide-notice', $notice ), 'icd_hosting_hide_notices_nonce', '_icd_hosting_notice_nonce' ) ); ?>"><?php _e( 'Dismiss' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
