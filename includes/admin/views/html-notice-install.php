<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated">
	<p><?php echo '<strong>' . __( 'Welcome to the ICDSoft Reseller Store', 'icd-hosting' ). '</strong> &#8211; ' . __( 'Create your new online store and start selling hosting services directly on your website.', 'icd-hosting' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=icd-hosting-register' ) ); ?>" class="button-primary"><?php _e( 'Get API Settings', 'icd-hosting' ); ?></a>
	</p>
</div>
