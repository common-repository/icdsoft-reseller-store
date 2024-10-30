<?php

include_once( 'show_plan_info.php' );

echo '<div class="price-table-wrapper">';
show_plan_info( $plan, $plan_name, $dc, $dc_name, $price, $periodicity, $resources, $currency, false, $renewal_price, $show_renew_price, $show_monthly_price);
echo '</div>';
?>

<script>
	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {
		$('.hosting-widget').prev().each(function (e) {
			if ($(this).is('br')) {
				$(this).remove();
			}
		});
	});
</script>