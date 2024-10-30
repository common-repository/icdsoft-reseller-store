<?php //check plugin required open php tag ?>
<div id="hosting-widget" class="hosting-widget">
	<div id="hosting-widget-errors"></div>
	<div class="container">
		<?php if (empty($offered)):  ?>
		<h2 class="text-center text-secondary"><i
					class="fas fa-cogs mr-2"></i><?php echo icd_hosting_tr('certificate.certificates_not_offered') ?></h2>
		<?php else: ?>
		<form action="<?php echo $ajax_url; ?>" method="post" class="form-horizontal" id="certificates-form"
			  data-url-approver-emails="<?php echo esc_attr( $approver_emails ); ?>"
			  onsubmit="return false">
			<input type="hidden" name="sslorder" value="1">
			<?php wp_nonce_field( 'approver-emails', '_approver_email_nonce' ); ?>
			<?php wp_nonce_field( 'order-create', '_order_check_nonce' ); ?>

			<?php
			if ( ! empty( $formdata['order_id'] ) ) {
				echo '<input type="hidden" name="order_id" value="' . $formdata['order_id'] . '">';
			}

			echo '<input type="hidden" name="action" value="' . $formdata['order_create'] . '">';
			?>

			<h3 class="pb-1 mb-3 border-bottom"><?php echo icd_hosting_tr('certificate.heading.ssl_certificate') ?></h3>
			<div class="form-group row">
				<label class="col-md-3">
					<?php echo icd_hosting_tr('certificate.label.certificate') ?>
					<a href="#" data-toggle="modal" data-target="#certificates-modal"><i
								class="glyphicon glyphicon-info-sign"></i></a>
				</label>
				<div class="col-md-6" id="ssl-products">
					<?php if ( ! empty( $offered ) ): ?>
						<?php foreach ( $offered as $product => $name ): ?>
							<div>
								<label>
									<input type="radio" name="ssl[catalog_id]"
										   value="<?php echo $default_prices[ $product ]['catalog_id'] ?>"
										   class="order-certificate"
										   data-product="<?php echo $product ?>" <?php if ( $product == $default_product ): ?> checked <?php endif; ?>>
									<?php echo $name ?>
								</label>;
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-form-label col-md-3">
					<?php echo icd_hosting_tr('certificate.label.period') ?>
				</label>
				<div class="col-md-6">
					<?php $selected = $default_prices[$default_product]; ?>
					<select name="ssl[period]" class="form-control selectui" id="order-period" data-allow-clear="false">
						<?php foreach ($selected['price'] as $period => $price): ?>
						<option value="<?php echo $period ?>" <?php if (isset($formdata['period']) and $formdata['period'] == $period): ?> selected <?php endif; ?>>
						<?php echo $period . " " . $price['periodicity'] . " - " .
						           (isset($store['currency']) ? $store['currency'] : "") . " " . sprintf('%.2f', $price['price']) ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2 text-right">
					<?php $selected = (is_array($default_prices)) ? current($default_prices) : null ?>
					<?php $ssl_price = (is_array($selected)) ? current($selected['price']) : null ?>
					<strong class="text-pink-darker" id="ssl-price" data-price="<?php echo $ssl_price['price'] ?>">
						<?php echo (isset($ssl_price['price']) and isset($store['currency'])) ? $store['currency'] . ' '  . sprintf('%.2f', $ssl_price['price']) : '-' ?>
					</strong>
				</div>
			</div>

			<h3 class="pb-1 mb-3 border-bottom"><?php echo icd_hosting_tr('certificate.heading.certificate_information') ?></h3>
			<div>
				<?php $setup='order';  $fields_namespace="ssl"; $i=$formdata?>
				<?php include_once( 'ssl.php' ); ?>
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

			<?php if ( ! empty( $processors ) ) { ?>
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

			<div class="hosting-options collapse">
				<h3 class="pb-1 mb-3 border-bottom"><?php echo icd_hosting_tr('certificate.label.hosting_account_options')?></h3>
				<div class="form-group row">
					<div class="col-md-3 text-right"><?php echo icd_hosting_tr('certificate.label.select_option') ?></div>
					<div class="col-md-8">
						<!--<div>
							<label class="mr-2">
								<input type="radio" name="hosting_plan" value="new" class="hosting-plan" disabled>
								{tr('certificate.label.new_hosting_account')}
							</label>
						</div>-->
						<div>
							<label class="mr-2">
								<input type="radio" name="hosting_plan" value="standalone" class="hosting-plan" checked>
								<?php echo icd_hosting_tr('certificate.label.without_hosting_account') ?>
							</label>
						</div>
						<!--<div><label class="mr-2">
								<input type="radio" name="hosting_plan" value="existing" class="hosting-plan" disabled>
								{tr('certificate.label.existing_hosting_account')}
							</label></div>-->
					</div>
				</div>
			</div>

			<h3 class="pb-1 mb-3 border-bottom"><?php echo icd_hosting_tr('titles.terms_and_agreements'); ?></h3>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr('labels.terms') ?>: </label>
				<div class="col-md-8">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="terms" required="required"
								   value="1"<?php if ( ! empty( $formdata['terms'] ) ): ?> checked="checked"<?php endif ?> data-field="terms">
							<span class="ssl-terms-text">
								<?php echo icd_hosting_tr('labels.terms_agree', ['url' => icd_hosting_url('terms')]); ?>
							</span>
						</label>
					</div>
				</div>
			</div>
			<div>
				<div class="form-group">
					<div class="col-md-6 col-md-offset-3 text-center">
						<br>
						<input type="hidden" name="idx" value="<?php echo $idx ?>">
						<input type="hidden" name="ssl_item_id"
							   value="<?php if ( ! empty( $formdata['ssl_item_id'] ) ) {
								   echo $formdata['ssl_item_id'];
							   } ?>">
						<button type="submit" class="btn btn-primary" id="create-order"><?php echo icd_hosting_tr( 'btns.order' ); ?></button>
					</div>
				</div>
			</div>
		</form>
		<?php endif; ?>
	</div>
	<?php include_once( 'ssl_modals.php' ); ?>
	<script>
		var widget_loaded = true;
	</script>
</div>
<script>
	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {
		Widget.loadTr(<?php echo wp_json_encode( icd_hosting_js_lang( 'request', [ 'ssl' => [ 'url' => icd_hosting_url( 'terms' ) ] ] ) );?>);
		CertificatesForm.init(<?php echo wp_json_encode( $formdata ); ?>);
	});
</script>
