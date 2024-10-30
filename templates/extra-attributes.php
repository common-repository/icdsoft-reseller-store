<?php
if ( $extra_attributes ) {
	if ( ! $prefix ) {
		$prefix = 'extra_attributes';
	}
	if ( ! strlen( $idx ) ) {
		$idx = substr( sha1( uniqid( time() . wp_rand(), true ) ), 0, 8 );;
	}

	if ( $setup == 'request') {
		$alert_class = '';
	} else {
		$alert_class = 'col-xs-10 col-xs-offset-1';
	}

	if ( in_array( $tld, [ 'hk', 'com.hk', 'net.hk', 'org.hk', 'idv.hk' ] ) ) { ?>
		<div class="alert alert-info {$alert_class}">
			<?php echo icd_hosting_tr( 'hk_documents_notice' ); ?>
		</div>
	<?php }

	if ( in_array( $tld, [ 'uk', 'co.uk', 'org.uk' ] ) and $action == 'transfer' ) { ?>
		<div class="alert alert-info {$alert_class}">
			<?php echo icd_hosting_tr( 'uk_transfer_notice' ); ?>
		</div>
	<?php }

	$idx    = esc_attr( $idx );
	$prefix = esc_attr( $prefix );
	?>
	<div class="extra-attributes-domain" id="extra-attributes-domain-<?php echo $idx ?>">
		<?php foreach ( $extra_attributes as $key => $item ) {
			$key = esc_attr( $key );

			if ( $item['type'] == 'hidden' ) { ?>
				<input type="hidden" name="<?php echo $prefix ?>[<?php echo $key ?>]"
					   value="<?php echo esc_attr( $item['value'] ) ?>" <?php if ( $item['required'] ) { ?> data-ext-required="<?php echo esc_attr( $item['required'] ) ?>"<?php } ?>>
				<?php continue;
			}
			$label = icd_hosting_tr_empty( "extra_attributes." . $key . "_label", esc_attr( $item['label'] ) ); ?>
			<div class="form-group">
				<label class="col-md-3 text-right control-label" for="<?php echo $key . '-' . $idx ?>">
					<?php echo esc_html( $label ) ?>:
					<?php if ( $item['required'] == 1 or $item['required'] == 2 ) { ?>
						<span class="mandatory">*</span>
					<?php } ?>
					<?php if ( icd_hosting_tr_has( "extra_attributes." . $key . "_help_title" ) and icd_hosting_tr_has( "extra_attributes." . $key . "_help_info" ) ) { ?>
						<a href="#" data-toggle="modal" data-target="#<?php echo $key ?>-modal-<?php echo $idx ?>"
						   class="label label-primary">?</a>
					<?php } ?>

					<!--{* Help messages *}-->
					<?php if ( icd_hosting_tr_has( "extra_attributes.help_msg." . $key ) ) {
						foreach ( icd_hosting_tr( "extra_attributes.help_msg." . $key ) as $k => $v ) { ?>
							<div class="ea-help-msg-<?php echo $key . '-' . $k ?> small text-muted pl-5 ea-help-msg"><?php echo esc_html( $v ) ?></div>
						<?php }
					} ?>
				</label>

				<div class="col-md-6">
					<?php if ( $item['type'] == 'select' ) { ?>
						<span class="selectwrap">
						<select id="<?php echo $key . '-' . $idx ?>" name="<?php echo $prefix ?>[<?php echo $key ?>]"
								class="form-control extra-attributes-select extra-attributes-<?php echo $key ?>"
								data-placeholder="<?php echo $label ?>"
								data-placeholder="<?php
								if ( ! empty( $item['placeholder'] ) ) {
									if ( icd_hosting_tr_has( "domains.extra_attributes.{$key}_placeholder"  ) ) {
										echo icd_hosting_tr( "domains.extra_attributes.{$key}_placeholder" );
									} else {
										echo esc_attr( $item['placeholder'] );
									}
								} else {
									echo $label;
								} ?>"
								data-theme="bootstrap"<?php if ( $item['required'] ) { ?> data-ext-required="<?php echo $item['required'] ?>" <?php } ?>
								data-field="domain_extra_attributes_<?php echo $key ?>_<?php echo $idx ?>">
							<option value=""></option>

							<?php if ( isset( $item['select_type'] ) and $item['select_type'] == 'country' ) { ?>
								<option value="">-- <?php echo icd_hosting_tr( 'contacts.country' ) ?> --</option>
								<?php foreach ( $countries as $iso => $country ) { ?>
									<option
											value="<?php echo $iso ?>"<?php if ( isset( $data[ $key ] ) and $iso == $data[ $key ] ) { ?> selected="selected"<?php } ?>>
								<?php echo esc_html( $country['country'] ) ?>
								</option>
								<?php } ?>
							<?php } else {
								foreach ( $item['options'] as $k => $v ) { ?>
									<option value="<?php echo esc_attr( $k ) ?>"
										<?php if ( isset( $data[ $key ] ) && $data[ $key ] == $k ) { ?> selected <?php } ?>
										<?php if ( ! empty( $v['help_msg'] ) ) { ?> data-ext-help-msg="<?php echo esc_attr( $v['help_msg'] ) ?>"<?php } ?>
										<?php if ( ! empty( $v['requires'] ) ) { ?> data-ext-requires="<?php if ( is_array( $v['requires'] ) ) {
											echo esc_attr( implode( ',', $v['requires'] ) );
										} else {
											echo esc_attr( $v['requires'] );
										} ?>"<?php } ?>
										<?php if ( ! empty( $v['hide'] ) ) { ?> data-ext-hide="<?php echo $v['hide'] ?>"<?php } ?>>
												<?php echo icd_hosting_tr_empty( "extra_attributes.$key.$k", esc_attr( $v['label'] ) ) ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</span>

						<!-- Static help messages -->
						<?php if ( icd_hosting_tr_has( "extra_attributes.static_help_msg." . $key ) ) { ?>
						<div class="ea-static-help-msg-<?php echo $key ?> ea-static-help-msg small text-muted pt-5 pb-5"><?php echo icd_hosting_tr("extra_attributes.static_help_msg." . $key); ?></div>
						<?php } ?>

						<label for="<?php echo $key ?>-<?php echo $idx ?>"
							   id="<?php echo $key ?>-error-<?php echo $idx ?>"
							   class="error display-none <?php echo $key ?>-error"></label>
					<?php } elseif ( $item['type'] == 'checkbox' ) { ?>
						<div class="checkbox">
							<?php foreach ( $item['options'] as $k => $v ) { ?>
								<label>
									<input id="<?php echo $key . '-' . $idx ?>" type="checkbox"
										   name="<?php echo $prefix ?>[<?php echo $key ?>]"
										   value="<?php echo $k ?>" class="extra-attributes-<?php echo $key ?>"
										<?php if ( isset( $data[ $key ] ) && $data[ $key ] == $k ) { ?> checked <?php } ?>
										<?php if ( $item['required'] ) { ?> data-ext-required="<?php echo esc_attr( $item['required'] ) ?>"<?php } ?>
										<?php if ( ! empty( $v['requires'] ) ) { ?> data-ext-requires="<?php if ( is_array( $v['requires'] ) ) {
											echo esc_attr( implode( ',', $v['requires'] ) );
										} else {
											echo esc_attr( $v['requires'] );
										} ?>"<?php } ?>
										   data-field="domain_extra_attributes_<?php echo $key ?>_<?php echo $idx ?>">

									<?php echo icd_hosting_tr_empty( "extra_attributes.$key.$k", $v['label'] ) ?>
								</label>
							<?php } ?>
						</div>

						<!-- Static help messages -->
						<?php if ( icd_hosting_tr_has( "extra_attributes.static_help_msg." . $key ) ) { ?>
							<div class="ea-static-help-msg-<?php echo $key ?> ea-static-help-msg small text-muted pt-5 pb-5"><?php echo icd_hosting_tr( "extra_attributes.static_help_msg." . $key ); ?></div>
						<?php } ?>

						<br><label for="<?php echo $key ?>-<?php echo $idx ?>"
								   id="<?php echo $key ?>-error-<1?php echo $idx ?>"
								   class="error display-none <?php echo $key ?>-error"></label>
					<?php } elseif ( $item['type'] == 'text' ) { ?>
						<input id="<?php echo $key ?>_<?php echo $idx ?>" type="text"
							   name="<?php echo $prefix ?>[<?php echo $key ?>]"
							   value="<?php echo isset($data[ $key ]) ? esc_attr( $data[ $key ] ) : '' ?>"
							   maxlength="255" placeholder="<?php
								if ( ! empty( $item['placeholder'] ) ) {
									if ( icd_hosting_tr_has( "domains.extra_attributes." . $key . "_placeholder" ) ) {
										echo icd_hosting_tr( "domains.extra_attributes." . $key . "_placeholder" );
									} else {
										echo esc_attr( $item['placeholder'] );
									}
								} else {
									echo $label;
								}?>"
							   class="form-control extra-attributes-<?php echo $key ?>"
							<?php if ( ! empty( $item['required'] ) ) { ?> data-ext-required="<?php echo esc_attr( $item['required'] ) ?>"<?php } ?>
							<?php if ( ! empty( $item['requires'] ) ) { ?> data-ext-requires="<?php if ( is_array( $v['requires'] ) ) {
								echo esc_attr( implode( ',', $v['requires'] ) );
							} else {
								echo esc_attr( $v['requires'] );
							} ?>"<?php } ?>
							   data-field="domain_extra_attributes_<?php echo $key ?>_<?php echo $idx ?>">

						<!-- Static help messages -->
						<?php if ( icd_hosting_tr_has( "extra_attributes.static_help_msg." . $key ) ) { ?>
							<div class="ea-static-help-msg-<?php echo $key ?> ea-static-help-msg small text-muted pt-5 pb-5"><?php echo icd_hosting_tr( "extra_attributes.static_help_msg." . $key ); ?></div>
						<?php } ?>

						<label for="<?php echo $key . '-' . $idx ?>" id="<?php echo $key ?>-error-<?php echo $idx ?>"
							   class="error display-none <?php echo $key ?>-error"></label>
					<?php } ?>
				</div>
			</div>

			<?php if ( icd_hosting_tr_has( "extra_attributes.{$key}_help_title" ) and icd_hosting_tr_has( "extra_attributes.{$key}_help_info" ) ) { ?>
				<div class="modal" id="<?php echo $key ?>-modal-<?php echo $idx ?>" tabindex="-1" role="dialog" data-backdrop="false">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header popover-title">
								<h4 class="modal-title"
									id="<?php echo $key ?>-info-label-<?php echo $idx ?>"><?php echo icd_hosting_tr( "extra_attributes." . $key . "_help_title" ) ?></h4>
							</div>

							<div class="modal-body">
								<?php echo icd_hosting_tr( "extra_attributes." . $key . "_help_info" ) ?>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-info" data-dismiss="modal"><i
											class="glyphicon glyphicon-remove"></i> <?php echo icd_hosting_tr( 'btns.close' ) ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>






