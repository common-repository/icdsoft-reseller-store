<?php

function show_plan_info( $plan, $plan_name, $dc, $dc_name, $price, $periodicity, $resources, $currency = '', $custom = false, $renewal_price = [], $show_renew_price = false, $show_monthly_price = false ) { ?>
	<div class="card shadow-sm ml-10 mb-10 pr-0 pl-0" style="float: left;">
		<div class="card-header">
			<h4 class="font-weight-normal">
				<?php echo esc_html( $plan_name ) ?>
				<img alt="<?php echo esc_html( $dc_name ) ?> " class="dc-plan-img"
					 src="<?php echo \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/img/' . esc_html( $dc ) . '.png ' ?>">
			</h4>
		</div>
		<div class="card-body">
			<?php
			$use_monthly_prices = ! empty( $show_monthly_price );
			$yearly_plan        = $periodicity == 'YR';;

			$plan_price         = ( $use_monthly_prices and $yearly_plan ) ? $price[1]['price'] / 12 : $price[1]['price'];
			$plan_period_label  = $use_monthly_prices ? icd_hosting_tr_choice( 'periods.MO', 1 )  : $price[1]['period_label'];
			?>
			<h3 class="card-title pricing-card-title">
				<span class="p-price"> <?php echo ( ( $currency == 'USD' ) ? '$' : $currency . ' ' ) . number_format( $plan_price, 2 ) ?> </span>
				<span class="p-duration small">/ <?php echo esc_html( $plan_period_label ) ?></span>
			</h3>

			<?php

			if ( ! empty( $renewal_price[1]['price'] ) and !empty($show_renew_price) ): ?>
			<?php $plan_renewal_price = ( $use_monthly_prices and $yearly_plan ) ? $renewal_price[1]['price'] / 12 : $renewal_price[1]['price']; ?>
				<h4 class="card-title pricing-card-title">
					<span class="small"><?php echo icd_hosting_tr( 'labels.renew_at' ); ?></span>
					<span class="p-price small"> <?php echo ( ( $currency == 'USD' ) ? '$' : esc_attr( $currency ) . ' ' ) . number_format( $plan_renewal_price, 2 ) ?> </span>
					<span class="p-duration small">/ <?php echo esc_attr( $plan_period_label ) ?></span>
				</h4>
			<?php endif; ?>

			<ul class="list-unstyled mt-3 mb-4 ml-0">
				<?php
				$resource_count = 0;
				foreach ( $resources as $resource ) {
					if ( $resource_count == 0 ) {
						echo '<li>';
						?>
						<a class="btn btn-lg btn-block btn-primary"
						   href="<?php echo icd_hosting_url( 'hostingorder', array(
							   'dc'   => $dc,
							   'plan' => $plan
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
	<?php
}

$customCss = '<link href="' . \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/css/custom.css' . '" rel="stylesheet"> ';

echo $customCss;
?>