<div id="payment-details-paypal-<?php echo $processor['processor_id'] ?>"
	 class="payment-details"
	<?php if ( strtolower( $order['payment_processor_id'] ) != $processor['processor_id'] ) { ?> style="display:none" <?php } ?>
	 data-order_id="<?php echo $order['order_id'] ?>"
	 data-client_id="<?php echo $processor['options']['client_id'] ?>"
	 data-amount="<?php echo $order['total_due'] ?>"
	 data-currency="<?php echo $order['currency'] ?>"
	 data-description="<?php echo icd_hosting_tr( 'hosting_order_due', [ 'order_id' => $order['order_id'] ] ) ?>"
	 data-processor-id="<?php echo $processor['processor_id'] ?>"
>
	<div>
		<div id="paypal-container-<?php echo $processor['processor_id'] ?>" class="paypal-container"></div>
	</div>
</div>
