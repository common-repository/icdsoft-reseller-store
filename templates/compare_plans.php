<?php
/*
include_once( 'show_plan_info.php' );

echo '<div class="price-table-wrapper">';
foreach ( $plans as $plan ) {
	extract( $plan );
	show_plan_info( $plan, $plan_name, $dc, $dc_name, $price, $resources, $currency );
}
echo '</div>';*/
?>


<div class="card-deck mb-3 text-center">
	<?php
	$plans_count = 0;
	foreach ( $plans

	as $plan ) {

	if ( $plans_count % $plans_per_row == 0 ) {
	?>
</div>
<br>
<div class="card-deck mb-3 text-center">
	<?php
	}
	?>

	<div class="card mb-4 shadow-sm">
		<div class="card-header">
			<h4 class="font-weight-normal">
				<?php echo esc_html( $plan['plan_name'] ) ?>
				<img alt="<?php echo esc_attr( $plan['dc_name'] ) ?> " class="dc-plan-img"
					 src="<?php echo \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/img/' . esc_attr( $plan['dc'] ) . '.png ' ?>">
			</h4>
		</div>
		<div class="card-body">
			<?php
			$use_monthly_prices = ! empty( $show_monthly_price );
			$yearly_plan        = $plan['periodicity'] == 'YR';

			$plan_price         = ( $use_monthly_prices and $yearly_plan ) ? $plan['price'][1]['price'] / 12 : $plan['price'][1]['price'];
			$plan_period_label  = $use_monthly_prices ? icd_hosting_tr_choice( 'periods.MO', 1 ) : $plan['price'][1]['period_label'];
			?>
			<h3 class="card-title pricing-card-title">
				<span class="p-price"> <?php echo ( ( $plan['currency'] == 'USD' ) ? '$' : esc_attr( $plan['currency'] ) . ' ' ) . number_format( $plan_price, 2 ) ?> </span>
				<span class="p-duration small">/ <?php echo esc_attr( $plan_period_label ) ?></span>
			</h3>

			<?php if ( ! empty( $plan['renewal_price'][1]['price'] ) and !empty($show_renew_price) ): ?>
			<?php $plan_renewal_price = ( $use_monthly_prices and $yearly_plan ) ? $plan['renewal_price'][1]['price'] / 12 : $plan['renewal_price'][1]['price']; ?>
			<h4 class="card-title pricing-card-title">
				<span class="small"><?php echo icd_hosting_tr( 'labels.renew_at' ); ?></span>
				<span class="p-price small"> <?php echo ( ( $plan['currency'] == 'USD' ) ? '$' : esc_attr( $plan['currency'] ) . ' ' ) . number_format( $plan_renewal_price, 2 ) ?> </span>
				<span class="p-duration small">/ <?php echo esc_attr( $plan_period_label ) ?></span>
			</h4>
			<?php endif; ?>

			<ul class="list-unstyled mt-3 mb-4">
				<?php
				$resource_count = 0;
				foreach ( $plan['resources'] as $resource ) {
					if ( $resource_count == 0 ) {
						echo '<li>';
						?>
						<a class="btn btn-lg btn-block btn-primary"
						   href="<?php echo icd_hosting_url( 'hostingorder', array(
							   'dc'   => esc_attr( $plan['dc'] ),
							   'plan' => esc_attr( $plan['plan'] )
						   ), true ) ?>" target="_self"><?php echo icd_hosting_tr( 'btns.order' ); ?></a>
						<?php
						echo '</li>';
					}
					echo '<li>';
					echo esc_html( $resource['value'] );

					if ( $resource['unit'] != 'COUNT' ) {
						echo ' ' . esc_html( $resource['unit'] );
					}
					echo ' ' . esc_html( $resource['label'] );
					echo '</li>';
					$resource_count ++;
				}
				?>
			</ul>
		</div>
	</div>
	<?php $plans_count ++;
	} ?>
</div>