<div id="hosting-widget-errors"></div>
<h3><?php echo esc_html( icd_hosting_tr( 'your_order_thankyou', array( 'order_id' => $order['order_id'] ) ) ); ?></h3>
<p><?php echo icd_hosting_tr( 'payment_info_text', array( 'outstanding' => $order['currency'] . ' ' . number_format( $order['total_due'], 2 ) ) ); ?></p>
<br>

<?php include( 'order_details.php' ) ?>
<?php if ( ! empty( $processors ) ): ?>
	<form action="<?php echo esc_attr( $ajax_url ); ?>" autocomplete="off" method="post" class="form-horizontal"
		  id="hosting-payment-form"
		  data-process_url="<?php echo esc_attr( icd_hosting_url('process_payment', [], true) ) ?>"
		  data-total="<?php echo isset( $order['payment_due'] ) ? esc_attr( $order['payment_due'] ) : '' ?>"
		  data-email="<?php echo isset( $order['email'] ) ? esc_attr( $order['email'] ) : ''?>"
		  data-city="<?php echo isset( $order['city'] ) ? esc_attr( $order['city'] ) : ''?>"
		  data-zip="<?php echo isset( $order['zip'] ) ? esc_attr( $order['zip'] ) : ''?>"
		  data-country="<?php isset( $order['country'] ) ? esc_attr( $order['country'] ) : ''?>"
		  onsubmit="return false">

		<?php wp_nonce_field( 'payment-check' ); ?>

		<?php echo '<input type="hidden" name="action" value="' . esc_attr( $formdata['pay'] ) . '">'; ?>
		<?php echo '<input type="hidden" name="order_id" value="' . esc_attr( $formdata['order_id'] ) . '">'; ?>
		<div id="payment-information">
			<h3><?php echo esc_html( icd_hosting_tr( 'titles.payment_method' ) ); ?></h3>
			<?php foreach ( $processors as $key => $processor ): ?>
				<div class="form-group">
					<div class="col-md-9 col-md-offset-3">
						<div class="checkbox">
							<label>
								<input class="payment_procs"
									   data-onsite="<?php echo esc_attr( $processor['onsite'] ); ?>"
									   data-type="<?php echo esc_attr( $processor['name'] ); ?>" type="radio"
									   name="payment_method"
									   value="<?php echo esc_attr( $key ); ?>" <?php if ( $key == $payment_method ) {
									echo 'checked="checked"';
								} ?>>
								<?php echo icd_hosting_tr( $processor['display_name'] ); ?>
							</label>
							<?php if ( ! empty( $processor['options']['info'] ) ): ?>
								<div class="payment-info mt-10 pl-20" id="procinfo-<?php echo esc_attr( $key ); ?>"
									<?php if ( $key != $payment_method ) {
										echo 'style="display: none"';
									} ?>>
									<?php echo esc_html( $processor['options']['info'] ); ?>
								</div>
							<?php endif; ?>

						</div>
						<?php
						if ( $processor['onsite'] and ! isset( $included[ $processor['name'] ] ) ) {
							include( __DIR__ . mb_strtolower( '/payment-details-' . $processor['name'] . '.php' ) );
						}
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div>
			<div class="form-group">
				<div class="col-md-6 col-md-offset-3 text-center">
					<br>
					<button type="submit" class="btn btn-primary" id="payment-btn"><?php echo icd_hosting_tr( 'btns.pay_now' ); ?></button>
				</div>
			</div>
		</div>
	</form>
<?php endif; ?>
<?php if (!empty($payment_assets['js'])): ?>
	<?php foreach($payment_assets['js'] as $js): ?>
			<script src="<?php echo $js; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>

<script>
	var widget_loaded = true;
</script>

<script>
	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {
		PaymentForm.init();
		<?php if ( isset( $js_trans ) ): ?>
		Widget.loadTr( <?php echo wp_json_encode( $js_trans );  ?>);
		Widget.loadTr( <?php echo wp_json_encode( icd_hosting_js_lang( 'payment' ) );?>);
		<?php endif; ?>
	});
</script>