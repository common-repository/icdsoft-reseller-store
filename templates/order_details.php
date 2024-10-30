<div class="order-summary">
	<div class="order-items">
		<h4><?php echo icd_hosting_tr( 'order_summary' ); ?>:</h4>
		<div class="row item-level-0">
			<div class="col-md-7 col-xs-12"><strong>Product</strong></div>
			<div class="col-md-2 col-xs-3"><strong>Period</strong></div>
			<div class="col-md-1 col-xs-3"><strong>Quantity</strong></div>
			<div class="col-md-2 col-xs-6 text-right"><strong>Price</strong></div>
		</div>
		<?php
		$group = '';
		foreach ( $order['items'] as $id => $item ) {
			if ( $item['group'] != $group ) {
				$group = $item['group'];
				?>
				<div class="row item-level-0">
					<div class="col-md-12 col-xs-12">
						<?php
						echo preg_replace( '/ - \d+$/', '', $group );
						if ( ! empty( $item['hosting_account'] ) ) {
							echo '(' . esc_attr( $item['hosting_account']['username'] ) . '@' . esc_attr( $item['hosting_account']['server'] ) . ')';
						}
						?>
					</div>
				</div>
			<?php } ?>

			<div class="row item-level-1">
				<div class="col-md-7 col-xs-12"><?php echo icd_hosting_format_item_label( $item ) ?></div>
				<div class="col-md-2 col-xs-3">
					<?php
					if ( in_array( $item['periodicity'], [ 'MO', 'YR' ] ) ) {
						$choice = 1;
						if ( $item['period'] > 1 ) {
							$choice = 2;
						}
						echo $item['period'] . ' ' . icd_hosting_tr_choice( 'periods.' . $item['periodicity'], $choice );
					} else {
						echo '-';
					}
					?>
				</div>
				<div class="col-md-1 col-xs-3">
					<?php
					if ( ! empty( $item['resource_unit'] ) ) {
						echo $item['quantity'] * $item['resource_value'];
						if ( $item['resource_unit'] != 'COUNT' ) {
							echo esc_html( $item['resource_unit'] );
						}
					} else {
						echo '-';
					}
					?>
				</div>
				<div class="col-md-2 col-xs-6 text-right"><?php echo esc_html( $order['currency'] . ' ' . number_format ( $item['price'], 2 ) ) ?></div>
			</div>
		<?php } ?>
	</div>

	<div class="order-totals">
		<div class="row">
			<div class="col-md-12 col-xs-12 text-right text-success">
				<strong><?php echo icd_hosting_tr( 'total' ) . ': ' . esc_attr( $order['currency'] . ' ' . number_format( $order['total'], 2 ) ); ?></strong>
			</div>
		</div>
	</div>

	<?php
	if ( $order['payments'] ) {
		echo '<div class="order-payments">';
		echo '<h4>' . icd_hosting_tr( 'payments_received_title' ) . ':</h4>';
		foreach ( $order['payments'] as $item ) {
			echo '<div class="row">';
			echo '<span class="col-md-6 col-xs-12"><strong>' . esc_html( $item['payment_method'] . '#' . $item['transaction_id'] ) . '</strong></span>';
			echo '<span class="col-md-4 col-xs-8">' . esc_html( gmdate('F d, Y h:i:s T', strtotime($item['payment_date']) ) ) . '</span>';
			echo '<span class="col-md-2 col-xs-4 text-right">' . esc_html( $item['currency'] . ' ' . $item['total'] ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
		if ( $order['total_due'] > 0 ) {
			echo '<div class="order-totals total-due">';
			echo '<div class="row">';
			echo '<div class="col-md-12 col-xs-12 text-right"><strong>' . icd_hosting_tr( 'balance_due' ) . ': ' . esc_html( $order['currency'] . ' ' . number_format( $order['total_due'], 2 ) ) . '</strong></div>';
			echo '</div>';
			echo '</div>';
		}
	}
	?>
</div>
