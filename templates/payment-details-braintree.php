<div id="payment-details-braintree-<?php echo $processor['processor_id'] ?>"
	 class="payment-details braintree-<?php echo $processor['processor_id'] ?>"
	<?php if ( strtolower( $order['payment_processor_id'] ) != $processor['processor_id'] ) { ?> style="display:none" <?php } ?>
	 data-client-token="<?php echo $processor['options']['client_token'] ?>"
	 data-amount="<?php echo $order['total_due'] ?>"
	 data-currency="<?php echo $order['currency'] ?>"
	 data-processor-id="<?php echo $processor['processor_id'] ?>"
>
	<div class="pl-20 pt-20 pr-20">
		<div id="braintree-dropin-<?php echo $processor['processor_id'] ?>" class="braintree-dropin"></div>
		<input id="braintree-nonce-<?php echo $processor['processor_id'] ?>" name="braintree_payment_method_nonce_<?php echo $processor['processor_id'] ?>" type="hidden"/>
	</div>
</div>