<div id="hosting-widget" class="hosting-widget">
	<div id="hosting-widget-errors"></div>
	<form action="<?php echo $ajax_url; ?>" method="post" class="form-horizontal" id="hosting-order-form"
		  onsubmit="return false">

		<?php wp_nonce_field( 'order-create', '_order_check_nonce' ); ?>
		<?php wp_nonce_field( 'domain-check', '_domain_check_nonce' ); ?>

		<?php
		if ( ! empty( $formdata['order_id'] ) ) {
			echo '<input type="hidden" name="order_id" value="' . $formdata['order_id'] . '">';
		}
		echo '<input type="hidden" name="action" value="' . $formdata['order_create'] . '">';
		?>
		<div id="hosting-information">
			<h3><?php echo icd_hosting_tr( 'titles.hosting_plan' ); ?></h3>
			<div id="domain-search-form" action="<?php echo $ajax_url; ?>">

				<div class="form-group mb-5">
					<label for="domain-search-domain" class="col-md-3 control-label">
						<?php echo icd_hosting_tr( 'labels.domain_name' ); ?>:
					</label>
					<div class="col-md-5">
						<input type="text" class="form-control" name="domain" value="<?php echo  $formdata['domain']; ?>"
							   id="domain-search-domain" placeholder="<?php echo icd_hosting_tr( 'domain_placeholder' ); ?>"
							   required="required" maxlength="68" data-field="domain">
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-primary domain-search-controls"
								id="domain-search-btn"><?php echo icd_hosting_tr( 'btns.check' ); ?></button>
					</div>
					<div class="col-md-2 text-right">
						<p class="form-control-static">
							<strong class="text-success price-amount" id="hosting-price-domain"></strong>
						</p>
					</div>
				</div>

				<div class="form-group new-domain-controls">
					<div class="col-md-9 col-md-offset-3">
						<div class="hosting-domain-option ml-10">
							<label class="radio-inline"><input type="radio" value="1"
															   name="new_domain" <?php if ( $formdata['new_domain'] == '1' ) {
									echo 'checked="checked"';
								} ?> class="hosting-new-domain"> <?php echo icd_hosting_tr( 'labels.new_domain' ); ?></label>
							<label class="radio-inline"><input type="radio" value="0"
															   name="new_domain" <?php if ( $formdata['new_domain'] == '0' ) {
									echo 'checked="checked"';
								} ?> class="hosting-new-domain"> <?php echo icd_hosting_tr( 'labels.existing_domain' ); ?></label>
						</div>
					</div>
				</div>

				<div class="form-group domain-search-controls">
					<div class="col-md-7 col-md-offset-3">
						<div class="hosting-domain-option" id="domain-search-tlds">
							<div class="col">
							<?php
							$counter_col = 0;
							foreach ( $tlds as $tld => $info ) { ?>
								<label class="ml-10" style="padding-right: 5px;">
									<input type="checkbox" name="tlds[]" value="<?php echo $tld; ?>" <?php
									if ( in_array( $tld, $preselected_tlds ) ) { ?>
										checked="checked"
									<?php } ?>
										   data-icann="<?php echo $info['icann']; ?>">
									<strong class="small">.<?php echo $tld; ?></strong>
								</label>
							<?php $counter_col++; ?>
							<?php if ($counter_col % 6 == 0 ) { ?>
							</div>
							<div class="col">

							<?php } ?>

							<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<a href="#" class="check-all-tlds" data-checked="0"><?php echo icd_hosting_tr('domain_search.check_all') ?></a>
					</div>
				</div>

				<div class="form-group domain-search-controls">
					<div class="col-md-8 col-md-offset-3">
						<div class="domain-search-result pl20" id="domain-search-result"></div>
					</div>
				</div>

			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'labels.server_location' ); ?>:</label>
				<div class="col-md-9">
						<?php foreach ( $datacenters as $dc => $dc_info ) {
							if ( $catalog[ $dc ] ) {
								?>
								<label class="location radio-inline">
									<input type="radio" value="<?php echo $dc; ?>" name="location"
										   id="<?php echo $dc; ?>"
										   class="required hosting-order-location" <?php if ( $dc == $formdata['location'] ) {
										echo 'checked="checked"';
									} ?>>
									<img src="<?php echo \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/img/' . $dc; ?>.png" class="mr-5"
										 alt="<?php echo $dc_info['name']; ?>"> <?php echo icd_hosting_tr( "locations.$dc" ); ?>
								</label><br>
							<?php }
						}
						?>
				</div>
			</div>

			<!--<div class="form-group">
				<label class="col-md-3 control-label"><?php /*echo icd_hosting_tr( 'labels.hosting_plan' ); */?>:</label>
				<div class="col-md-9">
					<div id="hosting-order-plans" class="radio">
						<?php /*foreach ( $catalog[ $formdata['location'] ] as $product => $data ) {
							echo '<label>';
							echo '<input type="radio" name="plan" class="hosting-order-plan" value="' . $product . '"';
							if ( $product == $formdata['plan'] ) {
								echo 'checked="checked"';
							}
							echo '>';
							echo '<strong>' . $data['name'] . ', </strong>';
							echo '<span class="props">';
							$prop_index = 0;
							foreach ( $resources[ $data['product_id'] ] as $resource ) {
								if ( $resource['name'] == 'traffic' || $resource['name'] == 'storage' ) {
									if ( $prop_index > 0 ) {
										echo ', ';
									}
									echo $resource['value'] . ' ' . $resource['label_unit'] . ' ' . $resource['label'];
									$prop_index = $prop_index + 1;
								}

							}
							echo '</span>';
							echo '</label><br>';

							if ( $product == $formdata['plan'] ) {
								$plan_periods = $data['order'];
							}
						} */?>
					</div>
				</div>
			</div>-->



			<div class="form-group">
				<div class="col-md-10 col-md-offset-1">

					<!--<div class="row pb-5 group-head">
						<div class="col-md-2"></div>
						<div class="col-md-2"><span class="stronger">{tr('resources.dedicated_cpu')}</span></div>
						<div class="col-md-2"><span class="stronger">{tr('resources.dedicated_ram')}</span></div>
						<div class="col-md-2"><span class="stronger">{tr('resources.storage')}</span></div>
						<div class="col-md-2"><span class="stronger">{tr('resources.traffic')}</span></div>
						<div class="col-md-2 text-right"><span class="stronger">{tr('labels.price_mo')}</span></div>
					</div>-->


					<?php foreach ($groups as $group_dc => $group_plans): ?>
					<div class="grouped-plans<?php if ($group_dc != $formdata['location']) {?> display-none<?php } ?>" id="grouped-plans-<?php echo $group_dc ?>">
						<?php foreach ($group_plans as $group => $plans): ?>
						<?php $resources_count = count($group_resources[$group]); ?>
						<div class="mb-10">
							<div id="group-<?php echo $group ?>">
								<div class="row group-head border-bottom">
									<div class="col-md-2"><span class="stronger"><?php echo icd_hosting_tr("plan_group.$group") ?></span></div>
									<?php for ($j = 0; $j < 4 - $resources_count; $j++) { ?>
									<div class="col-md-2"></div>
									<?php } ?>
									<?php foreach ($group_resources[$group] as $res) { ?>
									<?php if ($res == 'traffic') { $res = 'traffic_mo'; } ?>
									<div class="col-md-2"><span class="stronger"><?php echo icd_hosting_tr("resources.{$res}") ?></span></div>
									<?php } ?>
									<div class="col-md-2 text-right"><span class="stronger"><?php icd_hosting_tr('labels.price_mo') ?></span></div>
								</div>
								<?php foreach ($plans as $p) {
								$checked = 0;
								$plan = $catalog[ $group_dc ][ $p ];

								if ($group_dc == $formdata['location'] && $p == $formdata['plan']) {
									$checked = 1;
									$plan_periods = $plan['order'];
								}
								?>
								<div class="row plan-row border-bottom <?php if ($checked) {?> bg-light <?php } ?>">
									<div class="col-md-2">
										<label class="m-0 plan-name" style="display: table-cell;">
											<input type="radio" name="plan" value="<?php echo $plan['product'] ?>" class="hosting-order-plan"
												   data-plan="<?php echo $p ?>"<?php if ($checked) {?> checked="checked" <?php } ?>>
											<span class="stronger"><?php echo $plan['name'] ?></span>
										</label>
									</div>
									<?php for ($j = 0; $j < 4 - $resources_count; $j++) { ?>
									<div class="col-md-2"></div>
									<?php } ?>
									<?php foreach ( $group_resources[ $group ] as $res ) { ?>
									<div class="col-md-2 plan-<?php echo $res ?>">
										<?php echo $resources[ $plan['product_id'] ][ $res ]['value']; ?>
										<?php if ( $resources[ $plan['product_id'] ][ $res ]['unit'] != 'COUNT' ) { ?>
											<?php $unit = $resources[ $plan['product_id'] ][ $res ]['unit']; ?>
											<?php if ( $res == 'traffic' ) {
												$unit = str_replace( '/MO', '', $unit );
											} ?>
											<?php echo icd_hosting_tr( "units.{$unit}" ); ?>
										<?php } ?>
									</div>
									<?php } ?>
									<div class="col-md-2 text-right">
										<?php $order_price = current($plan['order']) ?>
										<?php $order_price_mo = ($plan['periodicity'] == 'YR') ? $order_price['price'] / 12 : $order_price['price']; ?>
										<?php echo $store['currency'] . ' ' . sprintf('%.2f', $order_price_mo) ?>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>


			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'labels.period' ); ?>:</label>
				<div class="col-md-5">
					<select name="period" class="periods form-control" id="hosting-order-period" required="required">
						<?php foreach ( $plan_periods as $pp => $price ) {
							echo '<option value="' . $pp . '"';
							if ( $pp == $formdata['period'] ) {
								echo 'selected="selected"';
							}
							echo '>';
							echo $pp . ' ' . $price['period_label'] . ' - ' . $store['currency'] . ' ' . sprintf( '%.2f', $price['price'] );
							echo '</option>';
						} ?>
					</select>
				</div>
				<div class="col-md-offset-2 col-md-2 text-right">
					<p class="form-control-static">
						<strong class="text-success price-amount" id="hosting-price-plan"></strong>
					</p>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'labels.terms' ); ?>:</label>
				<div class="col-md-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="terms" required="required"
								   value="1" <?php if ( $formdata['terms'] ) {
								echo 'checked="checked"';
							} ?> data-field="terms">
							<?php echo icd_hosting_tr( 'labels.terms_agree', array( 'url' => icd_hosting_url( 'terms' ) ) ); ?>
						</label>
					</div>
				</div>
			</div>

			<div class="form-group" id="order-domain-icann" style="display:none">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'labels.icann_verification' ); ?>:</label>
				<div class="col-md-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="icann" value="1" <?php if ( ! empty( $formdata['icann'] ) ) {
								echo 'checked="checked"';
							} ?> data-field="icann">
							<?php echo icd_hosting_tr( 'labels.icann_agree' ); ?>
						</label>
						<a href="#" data-toggle="modal" data-target="#icann-modal" class="label label-primary">?</a>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-10 control-label"><?php echo icd_hosting_tr( 'order_total' ); ?>:</label>
				<div class="col-md-2 text-right">
					<p class="form-control-static">
						<strong id="hosting-price-order" class="text-success price-amount"></strong>
					</p>
				</div>
			</div>
		</div>

		<div id="domain-extra-attributes-container" class="order-domain-extra-attributes">

			<div class="extra-attributes" id="extra-attributes"
			     <?php if ( empty( $tlds[ $formdata['tld'] ]['extra_attributes'] ) ) { ?>style="display: none"<?php } ?>>
				<br>
				<h3><?php echo icd_hosting_tr( 'request.tld_specific_data' ) ?></h3>
				<div class="extra-attributes-inner" data-tld="<?php echo $formdata['tld'] ?>">
					<?php if ( ! empty( $tlds[ $formdata['tld'] ]['extra_attributes'] ) ) {
						if ( empty( $formdata['domain_item_id'] ) ) {
							$index = 1;
						} else {
							$index = $formdata['domain_item_id'];
						}

						$idx              = $index;
						$extra_attributes = $tlds[ $formdata['tld'] ]['extra_attributes'];
						$data             = ! empty( $formdata['extra_attributes'] ) ? $formdata['extra_attributes'] : [];
						$prefix           = "domain_extra_attributes";
						$tld              = $formdata['tld'];
						$action           = "register";
						$setup            = "order";
						include_once( 'extra-attributes.php' );
					} ?>
				</div>
			</div>
		</div>

		<div id="personal-information">
			<h3><?php echo icd_hosting_tr( 'titles.personal_information' ); ?></h3>
			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_firstname"><?php echo icd_hosting_tr( 'contacts.firstname' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[firstname]" required="required"
						   value="<?php echo $formdata['contact']['firstname']; ?>" id="contact_firstname"
						   class="form-control" data-field="firstname"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.firstname' ); ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_lastname"><?php echo icd_hosting_tr( 'contacts.lastname' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[lastname]" required="required"
						   value="<?php echo $formdata['contact']['lastname']; ?>" id="contact_lastname"
						   class="form-control" data-field="lastname"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.lastname' ); ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_address"><?php echo icd_hosting_tr( 'contacts.address' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[address]" required="required"
						   value="<?php echo $formdata['contact']['address']; ?>" id="contact_address"
						   class="form-control" data-field="address"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.address' ); ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_address2"><?php echo icd_hosting_tr( 'contacts.address2' ); ?>:</label>
					<input type="text" name="contact[address2]" value="<?php echo $formdata['contact']['address2']; ?>"
						   id="contact_address2" class="form-control" data-field="address2"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.address2' ); ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_city"><?php echo icd_hosting_tr( 'contacts.city' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[city]" required="required" id="contact_city"
						   value="<?php echo $formdata['contact']['city']; ?>" class="form-control" data-field="city"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.city' ); ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_state"><?php echo icd_hosting_tr( 'contacts.state' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[state]" required="required"
						   value="<?php echo $formdata['contact']['state']; ?>" id="contact_state" class="form-control"
						   data-field="state" placeholder="<?php echo icd_hosting_tr( 'contacts.state' ); ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_zip"><?php echo icd_hosting_tr( 'contacts.zip' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="contact[zip]" required="required" id="contact_zip"
						   value="<?php echo $formdata['contact']['zip']; ?>" class="form-control" data-field="zip"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.zip' ); ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_country"><?php echo icd_hosting_tr( 'contacts.country' ); ?>: <span
								class="mandatory">*</span></label>
					<select class="form-control" name="contact[country]" required="required" id="contact_country"
							data-field="country">
						<option value="">-- <?php echo icd_hosting_tr( 'contacts.country' ); ?> --</option>
						<?php foreach ( $countries as $iso => $country ) {
							echo '<option value="' . $iso . '"';
							if ( $iso == $formdata['contact']['country'] ) {
								echo 'selected="selected"';
							}
							echo '>' . $country['country'] . '</option>';
						} ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_phone"><?php echo icd_hosting_tr( 'contacts.phone' ); ?>: <span
								class="mandatory">*</span></label>
					<div class="row">
						<div class="col-md-4">
							<select class="form-control" name="contact[phone_country]"
									required="required" id="contact_phone_country" data-field="phone_country">
								<option value="">-- <?php echo icd_hosting_tr( 'contacts.phone_country' ) ?> --</option>
								<?php foreach ( $countries as $iso => $country ) { ?>
									<option value="<?php echo $iso ?>" <?php if ( $iso == $formdata['contact']['phone_country'] ) { ?> selected='selected'<?php } ?>>
										<?php echo $country['country'] . ':' . $country['phone_code'] ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-8">
							<input type="text" name="contact[phone]" id="contact_phone"
								   value="<?php echo $formdata['contact']['phone'] ?>"
								   required="required" class="form-control" data-field="phone"
								   placeholder="<?php echo icd_hosting_tr( 'contacts.phone' ) ?>">
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<label for="contact_fax"><?php echo icd_hosting_tr( 'contacts.fax' ); ?>:</label>
					<div class="row">
						<div class="col-md-4">
							<select class="form-control" name="contact[fax_country]" id="contact_fax_country"
									data-field="fax_country">
								<option value="">-- <?php echo icd_hosting_tr( 'contacts.fax_country' ) ?> --</option>
								<?php foreach ( $countries as $iso => $country ) { ?>
									<option value="<?php echo $iso ?>" <?php if ( $iso == $formdata['contact']['fax_country'] ) { ?> selected='selected'<?php } ?>>
										<?php echo $country['country'] . ':' . $country['phone_code'] ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-8">
							<input type="text" name="contact[fax]" id="contact_fax"
								   value="<?php echo $formdata['contact']['fax'] ?>"
								   class="form-control" data-field="fax"
								   placeholder="<?php echo icd_hosting_tr( 'contacts.fax' ) ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-5 col-md-offset-2">
					<label for="contact_email"><?php echo icd_hosting_tr( 'contacts.email' ); ?>: <span
								class="mandatory">*</span></label>
					<input type="email" name="contact[email]" id="contact_email" required="required"
						   value="<?php echo $formdata['contact']['email']; ?>" class="form-control" data-field="email"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.email' ); ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_email2"><?php echo icd_hosting_tr( 'contacts.email2' ); ?>:</label>
					<input type="email" name="contact[email2]" id="contact_email2"
						   value="<?php echo $formdata['contact']['email2']; ?>" class="form-control"
						   data-field="email2" placeholder="<?php echo icd_hosting_tr( 'contacts.email2' ); ?>">
				</div>
			</div>
		</div>

		<?php if ( $processors ) { ?>
			<div id="payment-information">
				<h3><?php echo icd_hosting_tr( 'titles.payment_method' ); ?></h3>
				<?php foreach ( $processors as $key => $display_name ) { ?>
					<div class="form-group">
						<div class="col-md-9 col-md-offset-3">
							<div class="checkbox">
								<label>
									<input type="radio" name="payment_method"
										   value="<?php echo $key; ?>" <?php if ( $key == $payment_method ) {
										echo 'checked="checked"';
									} ?>>
									<?php echo icd_hosting_tr( $display_name ); ?></label>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<div>
			<div class="form-group">
				<div class="col-md-6 col-md-offset-3 text-center">
					<br>
					<input type="hidden" name="hosting_item_id"
						   value="<?php if ( ! empty( $formdata['hosting_item_id'] ) ) {
						       echo $formdata['hosting_item_id'];
					       } ?>">
					<input type="hidden" name="domain_item_id"
						   value="<?php if ( ! empty( $formdata['domain_item_id'] ) ) {
						       echo $formdata['domain_item_id'];
					       } ?>">
					<button type="submit" class="btn btn-primary"
							id="create-order"><?php echo icd_hosting_tr( 'btns.order' ); ?></button>
				</div>
			</div>
		</div>
	</form>
	<div class="modal" id="icann-modal" tabindex="-1" role="dialog" data-backdrop="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header popover-title">
					<h4 class="modal-title"><?php echo icd_hosting_tr( 'labels.icann_verification' ) ?></h4>
				</div>

				<div class="modal-body">
					<?php echo icd_hosting_tr( 'labels.icann_info' ) ?>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i
								class="glyphicon glyphicon-close"></i><?php echo icd_hosting_tr( 'btns.close' ) ?></button>
				</div>
			</div>
		</div>
	</div>
	<script type="x-template" id="hosting-plan-template">
		<label>
			<input type="radio" name="plan" class="hosting-order-plan" value="">
			<strong class='planlabel'></strong>
			<span class="propslabel"></span>
		</label><br>
	</script>
	<script>
		var widget_loaded = true;

		var formdata = {
			catalog: <?php echo wp_json_encode( $catalog ); ?>,
			resources: <?php echo wp_json_encode( $resources ); ?>,
			plan: '<?php echo $formdata['plan']; ?>',
			location: '<?php echo $formdata['location']; ?>',
			period: '<?php echo $formdata['period']; ?>',
			new_domain: '<?php echo $formdata['new_domain']; ?>',
			extra_attributes: <?php echo wp_json_encode( $tlds_extra_attributes ); ?>,
			order_currency: '<?php echo $store['currency']; ?>'
		};
	</script>
</div>
<script>
	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {
		Widget.loadTr(<?php echo wp_json_encode( icd_hosting_js_lang() );?>);
		HostingOrderForm.init(formdata);
	});
</script>
