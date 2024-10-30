<div id="hosting-widget" class="hosting-widget">
	<div id="hosting-widget-errors"></div>
	<form action="<?php echo $ajax_url; ?>" method="post" class="form-horizontal" id="domain-search-form"
		  onsubmit="return false">

		<?php wp_nonce_field( 'domain-check', '_domain_check_nonce' ); ?>

		<div class="form-group">
			<label for="domain-search-domain" class="col-md-3 control-label">
				<?php echo icd_hosting_tr( 'labels.domain_name' ); ?>:
			</label>
			<div class="col-md-5">
				<input type="text" class="form-control" value="" name="domain" id="domain-search-domain"
					   placeholder="<?php echo icd_hosting_tr( 'domain_placeholder' ); ?>" required="required" maxlength="68"
					   data-field="domain">
			</div>
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary"><?php echo icd_hosting_tr('btns.check') ?></button>
			</div>
		</div>
		<div class="form-group">
				<div class="col-md-7 col-md-offset-3">
					<div id="domain-search-tlds">
						<?php foreach ( $tlds as $tld => $info ) : ?>
							<label style="margin-left:10px">
								<input type="checkbox" name="tlds[]" value="<?php echo $tld; ?>"
									<?php if ( in_array( $tld, $preselected_tlds ) ) { ?>
										checked="checked"
									<?php } ?>>
								<strong class="small">.<?php echo $tld; ?></strong>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="col-md-2">
					<a href="#" class="check-all-tlds" data-checked="0"><?php echo icd_hosting_tr('domain_search.check_all') ?></a>
				</div>
			</div>
		<div class="form-group">
			<div class="col-md-8 col-md-offset-3">
				<div class="pl20 domain-search-result" id="domain-search-result"></div>
			</div>
		</div>
	</form>
	<script>
		/* var widget_loaded = true; */
		/* var widget_lang = {json_encode(widget_lang())} */
	</script>
</div>

<script>

	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {

		var widget_lang = <?php echo wp_json_encode( icd_hosting_js_lang() ); ?>;
		Widget.loadTr(widget_lang);
		DomainSearchForm.init();
	});
</script>
