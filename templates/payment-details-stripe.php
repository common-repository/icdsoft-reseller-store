<div id="payment-details-stripe-<?php echo $processor['processor_id'] ?>" class="payment-details" style="display:none"
	 data-key="<?php echo esc_attr( $processor['options']['public_key'] ) ?>" ]
	 data-amount="<?php echo esc_attr( $order['total_due'] ) * 100 ?>"
	 data-currency="<?php echo esc_attr( $order['currency'] ) ?>"
	 data-description="<?php echo icd_hosting_tr( 'hosting_order_due', [ 'order_id' => $order['order_id'] ] ) ?>"
	 data-country="<?php echo $order['country'] ?>"
	 data-processor-id="<?php echo $processor['processor_id'] ?>"
>
	<div class="pl-20 pt-20 pr-20">
		<div class="stripe-card"></div>
		<div class="stripe-error text-danger"></div>
	</div>
</div>
