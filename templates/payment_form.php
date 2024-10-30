<?php if(isset($url) && isset($params)): ?>
<div class="form-group text-center">
	<form action="<?php echo esc_attr( $url ); ?>" method="post" id="widget_payment_form"
		<?php if ( ! empty( $properties ) ) {
			foreach ( $properties as $k => $v ) {
				echo esc_attr( $k . '="' . $v . '"' );
			}
		} ?>>

		<?php wp_nonce_field( 'payment-form-check' ); ?>

		<?php if ( ! empty( $params ) ) {
			foreach ( $params as $k => $v ) {
				if ( ! is_array( $v ) ) {
					echo '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">';
				} elseif ( is_array( $v ) ) {
					echo '<input type="hidden" name="' . esc_attr( $v["key"] ) . '" value="' . esc_attr( $v["value"] ) . '">';
				}
			}
		}
		?>

		<button type="submit" class="btn btn-primary hidden"><?php echo icd_hosting_tr( 'btns.pay' ); ?></button>
	</form>
</div>
<script>document.getElementById('widget_payment_form').submit()</script>
<?php elseif (!empty($code)): ?>
<?php echo $code; ?>
<?php endif; ?>