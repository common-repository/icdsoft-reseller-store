<?php if ( empty( $fields_namespace ) ) {
	$fields_namespace = "order[items][$idx]";
} ?>
<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.common_name' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<?php if ( isset($i['action']) and $i['action'] == 'renewal' ): ?>
			<div class="form-control-plaintext"><?php echo $i['ssl']['common_name'] ?></div>
			<input type="hidden" name="<?php echo $fields_namespace ?>[ssl][common_name]"
				   value="<?php  echo isset($i['ssl']['common_name']) ?  esc_attr($i['ssl']['approver_email']) : ''; ?>" id="common-name-<?php echo $idx ?>">
		<?php else: ?>
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][common_name]"
			   value="<?php if ( isset($i['ssl']['common_name']) ) echo esc_attr($i['ssl']['common_name']) ?>"
			   required
			   placeholder="<?php echo icd_hosting_tr( 'request.ssl.common_name' ) ?>"
			   class="form-control ssl-common-name" id="common-name-<?php echo $idx ?>"
			   data-field="ssl_common_name_<?php echo $idx ?>">
		<?php endif; ?>
	</div>
</div>

<div class="form-group row ">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.approver_email' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<div class="input-group">
			<input type="text" name="<?php echo $fields_namespace ?>[ssl][approver_email]"
				   value="<?php if ( isset($i['ssl']['approver_email']) ) echo esc_attr($i['ssl']['approver_email']); ?>"
				   required
				   placeholder="<?php echo icd_hosting_tr( 'request.ssl.approver_email' ) ?>"
				   class="form-control approver-email" data-field="ssl_approver_email_<?php echo $idx ?>">

			<span class="input-group-btn ssl-approver-email" data-id="<?php echo $idx ?>"
				  data-product="<?php if ( isset($i['product']) ) echo $i['product']; ?>" data-url="<?php icd_hosting_url( 'approveremails' ) ?>">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="dropdown-header"><?php echo icd_hosting_tr( 'ssl.valid_approver_emails' ) ?></li>
					<li class="loading-email-spinner text-muted text-center collapse">
						<div class="spinner-border spinner-border-sm text-secondary align-baseline" role="status"><span
									class="sr-only">...</span></div>
					</li>
				</ul>
			</span>
		</div>
	</div>
</div>

<!--{if $setup != 'order' and $i.parent_product != 'standalone:ssl'}
{/if}
-->
<hr>
<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.organization' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][organization]"
			   value="<?php if ( isset($i['ssl']['organization']) ) echo esc_attr($i['ssl']['organization']); ?>"
			   required
			   placeholder="<?php echo icd_hosting_tr( 'request.ssl.organization' ) ?>"
			   class="form-control" data-field="ssl_organization_<?php echo $idx ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.organization_unit' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][organization_unit]"
			   value="<?php if ( isset($i['ssl']['organization_unit']) ) echo esc_attr($i['ssl']['organization_unit']); ?>"
			   required
			   placeholder="<?php echo icd_hosting_tr( 'request.ssl.organization_unit' ) ?>"
			   class="form-control" data-field="ssl_organization_unit_<?php echo $idx ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.country' ) ?><span class="mandatory">*</span></label>
	<div class="col-md-6">
		<select name="<?php echo $fields_namespace ?>[ssl][country]" class="form-control selectui"
				data-field="ssl_country_<?php echo $idx ?>">
			<option value="">--</option>
			<?php foreach ( $countries as $iso => $country ): ?>
				<option value="<?php echo $iso ?>"<?php if ( $iso == $i['ssl']['country'] ): ?> selected="selected"<?php endif; ?>>
					<?php echo $country['country']; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.city' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][city]"
			   value="<?php if ( isset($i['ssl']['city']) ) echo esc_attr($i['ssl']['city']); ?>"
			   required
			   placeholder="<?php echo icd_hosting_tr( 'request.ssl.city' ) ?>"
			   class="form-control" data-field="ssl_city_<?php echo $idx ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php echo icd_hosting_tr( 'request.ssl.state' ) ?> <span
				class="mandatory">*</span></label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][state]"
			   value="<?php if ( isset($i['ssl']['state']) ) echo esc_attr($i['ssl']['state']); ?>"
			   required
			   placeholder="<?php echo icd_hosting_tr( 'request.ssl.state' ) ?>"
			   class="form-control" data-field="ssl_state_<?php echo $idx ?>">
	</div>
</div>


<!--<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php /*echo icd_hosting_tr('request.ssl.email') */ ?></label>
	<div class="col-md-6">
		<input type="text" name="<?php /*echo $fields_namespace  */ ?>[ssl][email]" value="<?php /*echo $i['ssl']['email'] */ ?>"
		       class="form-control" data-field="ssl.email.<?php /*echo $idx */ ?>">
	</div>
</div>-->


<div class="form-group row">
	<label class="col-md-3 col-form-label">
		<?php echo icd_hosting_tr( 'request.ssl.address' ) ?>
		<span class="mandatory madatory-sectigo<?php if ( isset($i['product']) and ! in_array( $i['product'], [
			'sectigo_essential',
			'sectigo_essential_wildcard'
		] ) ): ?> collapse <?php endif; ?>">*</span>
	</label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][address]"
			   value="<?php if ( isset($i['ssl']['address']) ) echo esc_attr($i['ssl']['address']); ?>"
			<?php if ( isset($i['product']) and ! in_array( $i['product'], [
				'sectigo_essential',
				'sectigo_essential_wildcard'
			] ) ): ?>
				required
				placeholder="<?php echo icd_hosting_tr( 'request.ssl.address' ) ?>"
			<?php endif; ?>
			   class="form-control" data-field="ssl_address_<?php echo $idx ?>">
	</div>
</div>

<!--<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php /*echo icd_hosting_tr('request.ssl.address2') */ ?></label>
	<div class="col-md-6">
		<input type="text" name="<?php /*echo $fields_namespace  */ ?>[ssl][address2]" value="<?php /*echo $i['ssl']['address2'] */ ?>"
		       class="form-control" data-field="ssl.address2.<?php /*echo $idx */ ?>">
	</div>
</div>

<div class="form-group row">
	<label class="col-md-3 col-form-label"><?php /*echo icd_hosting_tr('request.ssl.address3') */ ?></label>
	<div class="col-md-6">
		<input type="text" name="<?php /*echo $fields_namespace  */ ?>[ssl][address3]" value="<?php /*echo $i['ssl']['address3'] */ ?>"
		       class="form-control" data-field="ssl.address3.<?php /*echo $idx */ ?>">
	</div>
</div>-->

<div class="form-group row">
	<label class="col-md-3 col-form-label">
		<?php echo icd_hosting_tr( 'request.ssl.zip' ) ?>
		<span class="mandatory madatory-sectigo<?php if ( isset($i['product']) and ! in_array( $i['product'], [
			'sectigo_essential',
			'sectigo_essential_wildcard'
		] ) ): ?> collapse <?php endif; ?>">*</span>
	</label>
	<div class="col-md-6">
		<input type="text" name="<?php echo $fields_namespace ?>[ssl][zip]"
			   value="<?php if ( isset($i['ssl']['zip']) ) echo esc_attr($i['ssl']['zip']); ?>"
			<?php if ( isset($i['product']) and ! in_array( $i['product'], [
				'sectigo_essential',
				'sectigo_essential_wildcard'
			] ) ): ?>
				required
				placeholder="<?php echo icd_hosting_tr( 'request.ssl.zip' ) ?>"
			<?php endif; ?>
			   class="form-control" data-field="ssl_zip_<?php echo $idx ?>">
	</div>
</div>

<?php if ( $setup != 'order' ): ?>
	<div class="form-group row">
		<div class="col-md-6 offset-md-3 text-center">
			<button type="button" class="btn btn-primary btn-done"
					data-toggle="config-<?php echo $idx ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
		</div>
	</div>
<?php endif; ?>
