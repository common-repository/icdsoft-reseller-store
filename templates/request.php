<div id="hosting-widget" class="hosting-widget hosting-widget-request">
	<div id="hosting-widget-errors"></div>

	<form action="<?php echo esc_attr( $ajax_url ); ?>" method="post" class="form-horizontal" id="hosting-order-form"
		  data-url-prices="<?php echo esc_attr( $request_prices ); ?>"
		  data-url-domain-search="<?php echo esc_attr( $domain_check ); ?>"
		  data-url-approver-emails="<?php echo esc_attr( $approver_emails ); ?>">

		<?php wp_nonce_field( 'payment-request-check' ); ?>
		<?php wp_nonce_field( 'domain-check', '_domain_check_nonce' ); ?>
		<?php wp_nonce_field( 'approver-emails', '_approver_email_nonce' ); ?>

		<?php echo '<input id="switch_ajax_action" type="hidden" name="action" value="' . esc_attr( $request_submit ) . '">'; ?>

		<div id="hosting-information">
			<h3><?php echo icd_hosting_tr( 'request.order_items_subtitle' ) ?></h3>
			<table class="table request-items">
				<thead>
				<tr>
					<th>#</th>
					<th> <?php echo icd_hosting_tr( 'request.item_action_th' ) ?></th>
					<th> <?php echo icd_hosting_tr( 'request.item_type_th' ) ?></th>
					<th> <?php echo icd_hosting_tr( 'request.item_name_th' ) ?></th>
					<th class="text-center" > <?php echo icd_hosting_tr( 'request.item_expiration_th' ) ?></th>
					<th class="text-center" > <?php echo icd_hosting_tr( 'request.item_new_expiration_th' ) ?></th>
					<th class="text-center" > <?php echo icd_hosting_tr( 'request.item_quantity_th' ) ?></th>
					<th class="text-center" > <?php echo icd_hosting_tr( 'request.item_period_th' ) ?></th>
					<th class="text-center" > <?php echo icd_hosting_tr( 'request.item_price_th' ) ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$cn                = 1;
				$discounted_prices = 0;
				$catalog_total     = 0;
				$group             = '';
				foreach ( $order['items'] as $idx => $i ) {
					if ( $order['locked'] && ! $i['checked'] ) {
						continue;
					}
					if ( $i['group'] != $group ) {
						$group = $i['group'];
						?>
						<tr>
							<td colspan="10">
								<strong>
									<?php
									echo preg_replace( '/ - \d+$/', '', $group );
									if ( ! empty( $i['hosting_account'] ) ) {
										echo '(' . esc_html( $i['hosting_account']['username'] ) . '@' . esc_html( $i['hosting_account']['server'] ) . ')';
									}
									?>
								</strong>
							</td>
						</tr>
					<?php } ?>

					<tr class="row-item-<?php echo esc_attr( $idx ) ?> parent-<?php echo isset( $i['parent'] ) ? esc_attr( $i['parent'] ) : '' ?> middle
						<?php if ( strpos( $i['parent_product'], 'bonus:' ) === 0 ) { ?> bonus-item <?php } ?>"
						data-id="<?php echo esc_attr( $idx ) ?>"
						data-parent="<?php echo isset( $i['parent'] ) ? esc_attr( $i['parent'] ) : '' ?>"
						data-account_id="<?php echo isset( $i['account_id'] ) ? esc_attr( $i['account_id']) : '' ?>">
						<td nowrap>
							<label class="mb0">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][checked]"
									   value="<?php if ( $order['locked'] ) {
									       echo 1;
								       } else {
									       echo 0;
								       } ?>">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][action]"
									   value="<?php echo esc_attr( $i['action'] ) ?>">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][period]"
									   value="<?php echo $i['period'] ?>">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][parent]"
									   value="<?php echo isset( $i['parent'] ) ? esc_attr( $i['parent'] ) : '' ?>">
								<input type="hidden"
									   name="order[items][<?php echo esc_attr( $idx ) ?>][parent_resource_id]"
									   value="<?php echo isset( $i['parent_resource_id'] ) ? $i['parent_resource_id'] : '' ?>">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][catalog_id]"
									   value="<?php echo esc_attr( $i['catalog_id'] ) ?>">
								<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][resource_id]"
									   value="<?php echo esc_attr( $i['resource_id'] ) ?>">
								<?php if ( ! empty( $i['recommended_upgrade'] ) ) { ?>
									<input type="hidden"
										   name="order[items][<?php echo esc_attr( $idx ) ?>][recommended_upgrade]"
										   value="<?php echo esc_attr( $i['recommended_upgrade'] ) ?>">
								<?php } ?>
								<?php if ( ! $order['locked'] ) {
									if ( $i['error'] ) { ?>
										<i class="text-warning glyphicon glyphicon-exclamation-sign tooltipui"
										   data-toggle="tooltip"
										   title="<?php echo icd_hosting_tr( "request.{$i['error']['code']}", $i['error']['params'] ) ?>"
										   data-content="<?php echo icd_hosting_tr( "request.{$i['error']['code']}", $i['error']['params'] ) ?>"></i>
									<?php } elseif ( ! $i['prices'] ) { ?>
										<i class="text-warning glyphicon glyphicon-exclamation-sign tooltipui"
										   data-toggle="tooltip"
										   title="<?php echo icd_hosting_tr( "request.account_cannot_be_upgraded_without_renewal" ) ?>"
										   data-content="<?php echo icd_hosting_tr( "request.account_cannot_be_upgraded_without_renewal" ) ?>"></i>
									<?php } else { ?>
										<input type="checkbox"
											   name="order[items][<?php echo esc_attr( $idx ) ?>][checked]"
											   class="select-item" value="1"
											   id="select-item-<?php echo esc_attr( $idx ) ?>" <?php if ( $i['checked'] ) { ?> checked <?php } ?>>
									<?php } ?>
								<?php } else { ?>
									<input type="checkbox" name="order[items][<?php echo esc_attr( $idx ) ?>][checked]"
										   class="select-item display-none" value="1"
										   id="select-item-<?php echo esc_attr( $idx ) ?>" checked>
								<?php }
								echo $cn ++; ?>
							</label>
						</td>
						<td>
							<?php $item_action = esc_html( ( isset( $i['domain'] ) ) ? $i['domain']['action'] : $i['action'] ); ?>
							<?php echo icd_hosting_tr( "request.order_actions.$item_action" ); ?>
						</td>
						<td>
							<?php

							if ( $i['action'] == 'purchase' ) {
								echo '-';
							} else if ( $i['parent_type'] == 'group' ) {
								$i_parent_product = $i['parent_product'];
								/*echo esc_html( $catalog[ $catalog[ $i['catalog_id'] ]['parent_id'] ]['name'] );*/
								echo icd_hosting_tr( "request.item_type.$i_parent_product" );
							} else {
								$i_product_type = $i['product_type'];
								echo icd_hosting_tr( "request.item_type.$i_product_type" );
							} ?>
						</td>
						<td>
							<?php
							if ( $i['action'] == 'purchase' ) {
								echo nl2br( htmlspecialchars( $i['purchase']['items'] ) );
							} else {
								echo htmlspecialchars( $i['item'] );
							}
							?>
						</td>
						<td class="text-center">
							<?php if ( ! empty( $i['resource'] ) and in_array( $i['periodicity'], [ 'YR', 'MO' ] ) ) {
								echo date( 'F d, Y', strtotime( $i['resource']['end_date'] ) );
							} else {
								echo '-';
							} ?>
						</td>
						<td class="text-center">
							<?php $periodicities = [ 'YR' => 'year', 'MO' => 'month' ];
							if ( $i['action'] == 'renewal' and $i['product_type'] == 'ssl' and strtotime( $i['resource']['end_date'] ) <= time() ) {
								echo icd_hosting_format_date( "+{$i['period']} {$periodicities[$i['periodicity']]}" );
							} elseif ( ! empty( $i['resource'] ) and in_array( $i['periodicity'], [
									'YR',
									'MO'
								] ) and $i['action'] == 'renewal' ) {
								echo icd_hosting_format_date( $i['resource']['end_date'] . ' +' . $i['period'] . ' ' . $periodicities[ $i['periodicity'] ] );
							} elseif ( ! empty( $i['resource'] ) and in_array( $i['periodicity'], [ 'YR', 'MO' ] ) ) {
								echo icd_hosting_format_date( $i['resource']['end_date'] );
							} elseif ( in_array( $i['periodicity'], [
									'YR',
									'MO'
								] ) and ! empty( $i['parent_end_date'] ) ) {
								echo icd_hosting_format_date( icd_hosting_calculated_end_date( $i['period'], $i['parent_end_date'] ) );
							} elseif ( in_array( $i['periodicity'], [ 'YR', 'MO' ] ) and $item_action != 'transfer' ) {
								echo icd_hosting_format_date( ' +' . $i['period'] . ' ' . $periodicities[ $i['periodicity'] ] );
							} else {
								echo '-';
							}
							?>
						</td>
						<td class="text-center">
							<?php if ( ! empty( $i['packages'] ) and count( $i['packages'] ) > 1 ) { ?>
								<select name="order[items][<?php echo esc_attr( $idx ) ?>][quantity]"
										class="form-control quantity <?php echo $i['product'] . '-quantity'; ?>">
									<?php
									foreach ( $i['packages'] as $p ) {
										$price = $p * $i['prices'][ $i['period'] ]['price']; ?>
										<option value="<?php echo esc_attr( $p ) ?>"
												data-price="<?php echo sprintf( '%.2f', $price ) ?>"
											<?php if ( !empty( $i['prices'][ $i['period'] ]['rules']['customer_discount'] ) ) { ?>
												data-catalog-price="<?php echo sprintf( '%.2f', $i['prices'][ $i['period'] ]['rules']['customer_discount']['price'] * $p ) ?>"
											<?php } ?>
											<?php if ( $i['quantity'] == $p ) {
												?> selected <?php } ?>>
											<?php if ( $i['resource_unit'] == 'COUNT' ) {
												echo esc_html( $p * $i['resource_value'] . ' - ' . $order['currency'] . sprintf( '%.2f', $price ) );
											} else {
												echo esc_html( $p * $i['resource_value'] . ' ' . $i['resource_unit'] . ' - ' . $order['currency'] . ' ' . sprintf( '%.2f', $price ) );
											}
											?>
										</option>
									<?php } ?>
								</select>
							<?php } else {
								echo ' - ';
							} ?>
						</td>
						<td class="text-center">
							<?php if ( in_array( $i['periodicity'], [ 'YR', 'MO' ] ) and ! empty( $i['prices'] ) ) {
								if ( ! $order['locked'] ) { ?>
									<select name="order[items][<?php echo esc_attr( $idx ) ?>][period]"
											class="form-control price"
											data-periodicity="<?php echo $i['periodicity']; ?>">
										<?php foreach ( $i['prices'] as $period => $price ) { ?>
											<option value="<?php echo $period ?>"
													id="price-<?php echo esc_attr( $idx ) ?>-<?php echo $period ?>"
													data-price="<?php echo $price['price'] ?>"
												<?php if ( $i['period'] == $period ) { ?> selected <?php } ?>>
												<?php if ( in_array( $i['periodicity'], [ 'YR', 'MO' ] ) ) { ?>
													<?php echo esc_html( $period ) . ' ' ?>
													<?php if ( ! empty( $price['periodicity'] ) ) {
														echo esc_html( $price['periodicity'] );
													} else {
														echo esc_html( $i['periodicity'] );
													} ?>
												<?php } ?>
												<?php echo esc_html( $order['currency'] . " " . sprintf( '%.2f', $price['price'] ) ); ?>
												<?php if ( ! empty( $price['rules'] ) ) {
													$rules = [];
													foreach ( $price['rules'] as $rule_key => $rule_val ) {
														if ( $rule_key == 'customer_discount' and ! empty( $i['item_discounts'][ $period ]['label'] ) ) {
															$rules[] = $i['item_discounts'][ $period ]['label'];
														} elseif ( ! in_array( $rule_key, [
															'hosting_upgrade',
															'hosting_migrate'
														] ) ) {
															$rules[] = icd_hosting_tr( "request.price_rules.$rule_key" );
														}
													}
													if ( ! empty( $rules ) ) {
														?> [<?php echo esc_html( implode( ', ', $rules ) ) ?>]
													<?php } ?>
												<?php } ?>
											</option>
										<?php } ?>
									</select>
								<?php } else {
									$price = $i['prices'][ $i['period'] ];
									if ( in_array( $i['periodicity'], [ 'YR', 'MO' ] ) ) {
										?>
										<?php echo esc_html( $i['period'] ) ?><?php
										if ( ! empty( $price['periodicity'] ) ) {
											echo esc_html( $price['periodicity'] );
										} else {
											echo esc_html( $i['periodicity'] );
										}
										echo "-";
									} ?>
									<?php echo esc_html( $order['currency'] . " " . sprintf( '%.2f', $price['price'] ) ) ?>
									<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][period]"
										   value="<?php echo esc_attr( $i['period'] ) ?>">
								<?php } ?>

							<?php } else {
								echo '-';
							} ?>
						</td>
						<td class="text-right">
							<?php echo $order['currency'] ?>
							<span class="item-price"
								  data-price="<?php echo esc_attr( $i['price'] ) ?>}"><?php echo esc_html( sprintf( '%.2f', $i['price'] ) ) ?></span>
							<?php if ( ! empty( $i['prices'][ $i['period'] ]['rules']['customer_discount'] ) ) {
								$discounted_prices = 1;
								$catalog_total     = $catalog_total + $i['prices'][ $i['period'] ]['rules']['customer_discount']['price'] * $i['quantity'];
								?>
								<span data-price="<?php echo esc_attr( $i['prices'][ $i['period'] ]['rules']['customer_discount']['price'] * $i['quantity'] ) ?>"
									  style="text-decoration: line-through;">
									<?php sprintf( '%.2f', $i['prices'][ $i['period'] ]['rules']['customer_discount']['price'] * $i['quantity'] ); ?>
								</span>
							<?php } else {
								$catalog_total = $catalog_total + $i['price'];
							} ?>
						</td>
						<td class="text-right">

							<?php if ( ! $order['locked'] and in_array( $i['action'], [ 'order' ] ) and in_array( $i['product_type'], [ 'server', 'hosting' ] ) ) { ?>
								<?php $sub = $catalog_helper->subCatalog( $i['catalog_id'] ); ?>
								<?php if ( $sub ) { ?>
									<button type="button" class="btn btn-xs btn-default toggle-item"
											title="<?php echo icd_hosting_tr( 'request.item_btn.add_title' ) ?>"
											data-toggle="extra-<?php echo esc_attr( $idx ) ?>"
											id="toggle-extra-<?php echo esc_attr( $idx ) ?>">
										<i class="glyphicon glyphicon-plus"></i>
									</button>
								<?php }
								}
								if ( in_array( $i['action'], [ 'order', 'upgrade', 'migrate'] ) and in_array( $i['product_type'], [ 'server', 'hosting', 'domain', 'ssl' ] ) or
							           in_array( $i['action'], [ 'renewal' ] ) and in_array( $i['product_type'], [ 'ssl' ] )  or
							           in_array( $i['action'], [ 'order' ] ) and in_array( $i['product'], [ 'advanced_security' ])
								) { ?>
								<button type="button"
										class="btn btn-xs btn-default toggle-item <?php if ( $i['not_configured'] ) { ?> btn-warning<?php } ?>"
										title="<?php echo icd_hosting_tr( 'request.item_btn.config_title' ) ?>"
										data-toggle="config-<?php echo esc_attr( $idx ) ?>"
										id="toggle-config-<?php echo esc_attr( $idx ) ?>">
									<i class="glyphicon glyphicon-cog"></i>
								</button>
							<?php }
							if ( isset( $i['contact'] ) ) { ?>
								<button type="button" class="btn btn-xs btn-default toggle-item"
										title="<?php echo icd_hosting_tr( 'request.item_btn.contact_title' ) ?>"
										data-toggle="icontact-<?php echo esc_attr( $idx ) ?>"
										id="toggle-icontact-<?php echo esc_attr( $idx ) ?>">
									<i class="glyphicon glyphicon-user"></i>
								</button>
							<?php } ?>
							<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][config]"
								   value="<?php if ( $i['config'] or $i['not_configured'] ) {
								       echo 1;
							       } else {
								       echo 0;
							       } ?>" class="item-toggled" id="item-config-<?php echo esc_attr( $idx ) ?>">
							<input type="hidden" name="order[items][<?php echo esc_attr( $idx ) ?>][icontact]"
								   value="<?php if ( ! empty( $i['icontact'] ) ) {
								       echo 1;
							       } else {
								       echo 0;
							       } ?>" class="item-toggled" id="item-icontact-<?php echo esc_attr( $idx ) ?>">
						</td>
					</tr>
					<tr class="row-item-<?php echo esc_attr( $idx ) ?> parent-<?php echo isset( $i['parent'] ) ? esc_attr( $i['parent'] ) : '' ?>
						<?php if ( ! $i['config'] and ! $i['not_configured'] and ! $i['icontact'] and ! $i['discount'] ) { ?> display-none <?php } ?> row-item-config"
						data-id="<?php echo esc_attr( $idx ) ?>"
						data-parent="<?php echo isset( $i['parent'] ) ? esc_attr( $i['parent'] ) : '' ?>"
						data-account_id="<?php echo isset( $i['account_id'] ) ? esc_attr( $i['account_id']) : '' ?>">
						<td colspan="10">
							<div class="row">
								<div class="col-md-10 col-md-offset-1">

									<?php if ( ! $order['locked'] and $i['action'] == 'order' and in_array( $i['product_type'], [ 'server', 'hosting' ] ) ) { ?>
										<div class="item-extra clearfix display-none"
											 id="extra-<?php echo esc_attr( $idx ) ?>">
											<input type="hidden" id="extra-item-<?php echo esc_attr( $idx ) ?>"
												   value="0">
											<h4><?php echo icd_hosting_tr( 'request.item_extra_resource_subtitle' ) ?></h4>
											<div class="extra-item-form">
												<div class="form-group">
													<div class="col-md-6 col-md-offset-3">
														<select data-name="catalog_id" class="form-control extra-item"
																size="15">
															<?php foreach ( $sub as $group_id => $items ) { ?>
																<optgroup
																		label="<?php echo esc_attr( $catalog[ $group_id ]['name'] ) ?>">
																	<?php foreach ( $items as $catalog_id => $item ) { ?>
																		<option value="<?php echo esc_attr( $catalog_id ) ?>"><?php echo esc_html( $item ) ?></option>
																	<?php } ?>
																</optgroup>
															<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<div class="col-md-6 col-md-offset-3 text-center">
														<input type="hidden" data-name="action" class="extra-item"
															   value="order">
														<input type="hidden" data-name="parent" class="extra-item"
															   value="<?php echo esc_attr( $idx ) ?>">
														<button type="button" class="btn btn-primary add-extra-item"><i
																	class="glyphicon glyphicon-plus"></i>
															<?php echo icd_hosting_tr( 'request.btn.add_item' ) ?>
														</button>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>

									<?php if ( in_array( $i['action'], [
											'order',
											'upgrade',
											'migrate'
										] ) or in_array( $i['action'], [ 'renewal' ] ) and in_array( $i['product_type'], [ 'ssl' ] )
									           or in_array( $i['action'], [ 'order' ] ) and in_array( $i['product'], [ 'advanced_security' ] ) ) { ?>
										<div class="item-config clearfix <?php if ( ! $i['config'] and ! $i['not_configured'] ) { ?> display-none <?php } ?>"
											 id="config-<?php echo esc_attr( $idx ) ?>"
											 data-id="<?php echo esc_attr( $idx ) ?>"
											 data-period="<?php echo esc_attr( $i['period'] ) ?>">
											<?php
											if ( $i['action'] == 'order' and ! empty( $i['hosting'] ) ) { ?>
												<h4><?php echo icd_hosting_tr( 'request.item_hosting_subtitle' ) ?></h4>
												<div>
													<div class="form-group">
														<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'request.hosting.hostname' ) ?>
															:
															<span class="mandatory">*</span>
														</label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][hosting][hostname]"
																   value="<?php echo esc_attr( $i['hosting']['hostname'] ) ?>"
																   class="form-control hosting-hostname"
																   placeholder="my-domain.com"
																   data-field="hosting_hostname_<?php echo esc_attr( $idx ) ?>">
														</div>
														<div class="col-md-3">
															<button type="button" class="btn btn-primary btn-done"
																	data-toggle="config	-<?php echo esc_attr( $idx ) ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?>
															</button>
														</div>
													</div>
												</div>
											<?php } elseif ( $i['action'] == 'order' and ! empty( $i['domain'] ) ) {
												?>
												<h4><?php echo icd_hosting_tr( 'request.item_domain_subtitle' ) ?></h4>
												<div class="domain-search-container"
													 data-parent-id="<?php echo esc_attr( $catalog[ $i['catalog_id'] ]['parent_id'] ) ?>"
													 data-id="<?php echo esc_attr( $idx ) ?>">
													<?php
													if ($i['checked'] and !empty($i['host_configuration']) and isset($i['new_addon_domain']) and in_array($i['domain']['parked_to'], ['', 'main'])) {
														$addon_parent = !empty($i['account_id']) ? $i['account_id'] : $i['parent'];

														if ( ! isset( $order['addon_domains'][ $addon_parent ]['used_order'] ) ) {
															$order['addon_domains'][ $addon_parent ]['used_order'] = 0;
														} else {
															$order['addon_domains'][ $addon_parent ]['used_order'] = $order['addon_domains'][ $addon_parent ]['used_order'] + 1;
														}
													}
													?>
													<div>
														<div class="form-group">
															<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'request.domain.domain' ) ?>: <span class="mandatory">*</span>
															</label>
															<div class="col-md-6">
																<input type="hidden"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][catalog_id]"
																	   value="<?php echo esc_attr( $i['catalog_id'] ) ?>"
																	   class="domain-catalog-id">
																<input type="hidden"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][sld]"
																	   value="<?php echo esc_attr( $i['domain']['sld'] ) ?>"
																	   class="domain-sld">
																<input type="hidden"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][tld]"
																	   value="<?php echo esc_attr( $i['domain']['tld'] ) ?>"
																	   class="domain-tld">
																<input type="hidden"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][action]"
																	   value="<?php echo esc_attr( $i['domain']['action'] ) ?>"
																	   class="domain-action">
																<input type="text"
																	   value="<?php if ( $i['domain']['sld'] ) {
																	       echo esc_attr( $i['domain']['sld'] . '.' . $i['domain']['tld'] );
																       } ?>"
																	   class="form-control domain-search-input"
																	   placeholder="my-domain.<?php echo esc_attr( $i['domain']['tld'] ) ?>"
																	   data-icann="<?php if ( $tld_info[ $i['domain']['tld'] ]['icann'] ) { ?> 1 <?php } else { ?> 0 <?php } ?>"
																	   data-epp="<?php if ( $tld_info[ $i['domain']['tld'] ]['epp'] ) { ?> 1 <?php } else { ?> 0 <?php } ?>"
																	   data-field="domain_sld_<?php echo esc_attr( $idx ) ?>"
																	   data-group="<?php echo esc_attr( $i['parent_product'] ) ?>"
																>
															</div>
															<div class="col-md-3">
																<button type="button"
																		class="btn btn-info domain-search-btn<?php if ( $i['domain']['sld'] ) {
																	        ?> click-search <?php } ?>">
																	<?php echo icd_hosting_tr( 'request.btn.search' ) ?></button>
															</div>
														</div>
														<div class="form-group domain-search-controls">
															<div class="col-md-8 col-md-offset-2">
																<?php foreach ( $tlds[ $catalog[ $i['catalog_id'] ]['parent_id'] ] as $catalog_id => $info ) { ?>
																	<label class="ml-10">
																		<input type="checkbox"
																			   value="<?php echo esc_attr( $info['tld'] ) ?>"
																			   class="check-tlds"
																			   data-catalog_id="<?php echo esc_attr( $catalog_id ) ?>"
																			<?php if ( in_array( $info['tld'], $preselected_tlds ) or $info['tld'] == $i['product'] ) { ?>
																				checked="checked"
																			<?php } ?>
																			   data-icann="<?php echo isset( $info['icann'] ) ? esc_attr( $info['icann'] ) : '' ?>"
																			   data-epp="<?php echo isset( $info['epp'] ) ? esc_attr( $info['epp'] ) : '' ?>">
																		<strong class="small">.<?php echo esc_html( $info['tld'] ) ?></strong>
																	</label>
																<?php } ?>
															</div>
															<div class="col-md-2">
																<a href="#" class="check-all-tlds"
																   data-checked="0"><?php echo icd_hosting_tr( 'domain_search.check_all' ) ?></a>
															</div>
														</div>
													</div>

													<br>

													<?php if ( isset($i['parent_resource_id']) || $i['parent_product'] == 'extra:domain' ) { ?>
														<div class="dns-options" id="dns-options-<?php echo esc_attr( $idx ) ?>">
															<br>
															<h5><?php echo icd_hosting_tr( 'request.dns_configuration' ) ?></h5>

															<?php if (isset($i['new_addon_domain']) && $i['account_id']) {
															$addon_slots       = $order['addon_domains'][ $i['account_id'] ]['value'];
															$addon_extra_slots = $order['addon_domains'][ $i['account_id'] ]['extra'] + $order['addon_domains'][ $i['account_id'] ]['extra_order'];
															$addon_used_slots  = $order['addon_domains'][ $i['account_id'] ]['used'] + $order['addon_domains'][ $i['account_id'] ]['used_order'] - 1;
														 	// not checked but calculate price like checked
															if (!$i['checked']) {
																$addon_used_slots = $addon_used_slots + 1;
															}
															$is_free = $addon_slots + $addon_extra_slots > $addon_used_slots;
															?>
															<div class="form-group parking-options-inner" id="parking-options-inner-<?php echo esc_attr($idx) ?>">
																<label class="control-label col-md-3 text-right">
																	<?php echo icd_hosting_tr('request.host_as'); ?>:
																	<a href="#" data-toggle="modal" data-target="#parking-modal" class="label label-primary">?</a>
																</label>
																<div class="col-md-6">
																	<select name="<?php echo "{$fields_namespace}[{$idx}][domain][parked_to]" ?>" class="form-control domain-parked-to">
																		<option value="" <?php if ( $is_free and ! $i['new_addon_domain'] ) { ?> disabled<?php } ?>>
																			<?php echo icd_hosting_tr('request.host_as_addon_domain') ?>
																		</option>
																		<optgroup label="<?php echo icd_hosting_tr('request.parked_domain_to')?>">
																			<?php foreach ($order['hosted_domains'][$i['account_id']] as $dom) { ?>
																				<?php if ($dom['domain'] != "{$i['domain']['sld']}.{$i['domain']['tld']}") { ?>
																				<option value="<?php echo $dom['domain'] ?>" class="hosted-<?php echo $dom['domain_type'] ?>"
																					<?php if (isset($i['domain']['parked_to']) and $i['domain']['parked_to'] == $dom['domain']) { ?> selected <?php }?>>
																					<?php echo $dom['domain'] ?> (<?php echo $dom['domain_type'] ?>)
																				</option>
																				<?php } ?>
																			<?php } ?>
																		</optgroup>
																	</select>
																</div>
																<div class="col-md-3 domain-parked-to-details<?php if (!in_array($i['domain']['parked_to'], ['', 'main']) or !$is_free and !$i['new_addon_domain']) { ?> display-none<?php } ?>"
																	 data-addon_slots="<?php echo $order['addon_domains'][ $addon_parent ]['value'] ?>"
																	 data-addon_extra_slots="<?php echo $order['addon_domains'][$addon_parent]['extra'] ?>"
																	 data-addon_used_slots="<?php echo  $order['addon_domains'][$addon_parent]['used'] ?>">

																	<div class="form-control-static pull-left">
																		<span class="tooltipui"
																			  data-content="<?php echo icd_hosting_tr('request.account_addon_domains_usage') ?>">
																			<strong class="parked_to"><?php echo $addon_used_slots ?></strong>
																			<span> / </span>
																			<strong><?php echo $addon_slots ?></strong>
																			<strong>
																				<span class="text-success addon-extra-slots">
																					<?php if ($addon_extra_slots) {  echo ' + ' . $addon_extra_slots; } ?>
																				</span>
																			</strong>
																		</span>
																	</div>
																	<div class="addon-slot-free pull-right<?php if (!$is_free) {?> display-none <?php } ?>">
																		<span class="inline-block form-control-static"><?php echo $order.currency ?> 0.00</span>
																		<button type="button" class="btn btn-sm btn-primary ml-10" style="visibility: hidden">
																			<i class="glyphicon glyphicon-plus"></i>
																		</button>
																	</div>
																	<div class="addon-slot-paid pull-right <?php if (!$is_free) {?> display-none <?php } ?>">
																		<?php
																		$months_left = $i['hosting_account']['calculated_periods'][0];
																		$price = $catalog[$i['new_addon_domain']]['prices']['order'][1];
																		?>
																		<span class="inline-block form-control-static tooltipui"
																			  data-content="<?php echo icd_hosting_addon_price_tip($months_left, $price) ?>">
																			<?php echo $order['currency'] . ' ' . sprintf('%.2f', $months_left * $price.price) ?>
																		</span>
																		<button type="button" class="pull-right btn btn-sm btn-primary ml-10 add-new-addon-domain"
																				title="<?php icd_hosting_tr('request.btn.add_item')?>"
																				data-account_id="<?php echo $i['account_id'] ?>"
																				data-request="request[action]=order&request[parent_resource_id]=<?php echo $i['parent_resource_id']?>&request[catalog_id]=<?php echo $i['new_addon_domain'] ?>" >
																			<i class="glyphicon glyphicon-plus"></i>
																		</button>
																	</div>
																</div>
															</div>
															<?php } ?>

															<div class="form-group" id="dns-options-inner-<?php echo esc_attr( $idx ) ?>">
																<label class="col-md-3 text-right control-label">
																	<?php echo icd_hosting_tr( 'request.dns_configuration' ) ?>
																	:
																	<a href="#" data-toggle="modal"
																	   data-target="#dns-modal"
																	   class="label label-primary">?</a>
																</label>
																<div class="col-md-9">
																	<div class="checkbox">
																		<label>
																			<input type="hidden"
																				   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][global_dns]"
																				   value="0">
																			<input type="checkbox"
																				   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][global_dns]"
																				   value="1"
																				<?php if ( $i['domain']['global_dns'] ) { ?> checked <?php } ?>>
																			<?php echo icd_hosting_tr( 'request.use_global_dns' ) ?>
																		</label>
																	</div>
																</div>
															</div>
														</div>
													<?php } ?>

													<div class="epp-code display-none"
														 id="epp-code-<?php echo esc_attr( $idx ) ?>">
														<br>
														<h5><?php echo icd_hosting_tr( 'request.transfer_information_subtitle' ) ?>
															:</h5>
														<div class="form-group"
															 id="epp-code-inner-<?php echo esc_attr( $idx ) ?>">
															<label class="col-md-3 text-right control-label">
																<?php echo icd_hosting_tr( 'request.epp_code' ) ?>:
																<span class="mandatory">*</span>
																<a href="#" data-toggle="modal" data-target="#epp-modal"
																   class="label label-primary">?</a>
															</label>
															<div class="col-md-6">
																<input type="text"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][epp]"
																	   value="<?php echo esc_attr( $i['domain']['epp'] ) ?>"
																	   class="form-control" placeholder="EPP"
																	   data-field="domain_epp_<?php echo esc_attr( $idx ) ?>">
															</div>
														</div>
													</div>

													<div class="extra-attributes <?php if ( ! $tld_info[ $i['domain']['tld'] ]['extra_attributes'] ) { ?> display-none <?php } ?>"
														 id="extra-attributes-<?php echo esc_attr( $idx ) ?>">
														<br>
														<h5><?php echo icd_hosting_tr( 'request.tld_specific_data' ) ?></h5>
														<div class="extra-attributes-inner"
															 data-tld="<?php echo esc_attr( $i['domain']['tld'] ); ?>">
															<?php if ( $tld_info[ $i['domain']['tld'] ]['extra_attributes'] ) {
																$extra_attributes = $tld_info[ $i['domain']['tld'] ]['extra_attributes'];
																$data             = $i['domain']['extra_attributes'];
																$prefix           = "order[items][$idx][domain][extra_attributes]";
																$action           = $i['domain']['action'];
																$tld              = $i['domain']['tld'];
																$setup            = 'request';
																include( 'extra-attributes.php' );
															} ?>
														</div>
													</div>

													<?php if ( $i['parent_product'] == 'standalone:domain' ) { ?>
														<div class="ns-configuration"
															 id="ns-configuration-<?php echo esc_attr( $idx ) ?>">
															<br>
															<h5><?php echo icd_hosting_tr( 'request.dns_configuration' ); ?>
																<small>( <?php echo icd_hosting_tr( 'optional' ); ?>)</small>
															</h5>
															<div id="ns-configuration-inner-<?php echo esc_attr( $idx ) ?>"
																 class="col-md-12">
																<?php
																$max_ns   = 5;
																$ns_count = 0;
																for ( $j = 1; $j <= $max_ns; $j ++ ) {
																	if ( ! empty( $i['domain']["ns{$j}"] ) ) {
																		$ns_count = $ns_count + 1;
																	}
																}

																for ( $j = 1; $j <= $max_ns; $j ++ ) { ?>
																	<div class="form-group <?php if ( $j > 2 and empty( $i['domain']["ns{$j}"] ) ) { ?> display-none <?php } ?>">
																		<label class="col-sm-3 control-label text-right"><?php echo icd_hosting_tr( 'request.ns', [ 'num' => $j ] ); ?>:</label>

																		<div class="col-sm-6">
																			<input type="text" class="form-control"
																				   placeholder="<?php echo icd_hosting_tr( 'request.hostname' ); ?>"
																				   value="<?php echo $i['domain']["ns{$j}"] ?>"
																				<?php if ( $j > 2 and empty( $i['domain']["ns{$j}"] ) ) { ?>
																					disabled="disabled"
																				<?php } ?>
																				   name="order[items][<?php echo esc_attr( $idx ) ?>][domain][ns<?php echo esc_attr( $j ) ?>]"
																				   data-field="domain_ns<?php echo esc_attr( $j . '_' . $idx ); ?>">
																		</div>
																	</div>

																	<?php if ( $j == $max_ns ) { ?>
																		<div class="form-group text-right m-0">
																			<div class="col-sm-6 col-sm-offset-3">
																				<span class="ns-configuration-toggle">
																					<a href="#" class="ns-show-more <?php if ($ns_count >= $max_ns) { ?> display-none <?php } ?>">
																						<i class="glyphicon glyphicon-plus pr-5"></i><?php echo icd_hosting_tr( 'btns.more' ); ?>
																					</a>
																				</span>
																			</div>
																		</div>
																	<?php } ?>
																<?php } ?>
															</div>
														</div>
													<?php } ?>

													<div class="domain-done"
														 id="domain-done-<?php echo esc_attr( $idx ) ?> display-none">
														<div class="form-group">
															<div class="col-md-6 col-md-offset-3 text-center">
																<button type="button" class="btn btn-primary btn-done"
																		data-toggle="config-<?php echo esc_attr( $idx ) ?>">
																	<?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
															</div>
														</div>
													</div>

													<div id="domain-search-result-<?php echo esc_attr( $idx ) ?>"
														 class="display-none">
														<br>
														<h5><?php echo icd_hosting_tr( 'request.search_result_subtitle' ) ?></h5>
														<div class="domain-search-result"></div>
													</div>
												</div>
											<?php } else if ( ! empty( $i['ssl'] ) ) { ?>
												<h4><?php echo icd_hosting_tr( 'request.item_ssl_subtitle' ) ?></h4>
												<div>
													<div class="form-group">
														<label class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.common_name' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<?php if ( $i['action'] == 'renewal' ) { ?>
																<div class="form-control-static"><?php echo esc_html( $i['ssl']['common_name'] ) ?></div>
																<input type="hidden"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][common_name]"
																	   value="<?php echo htmlspecialchars( $i['ssl']['common_name'] ) ?>"
																	   id="common-name-<?php echo esc_attr( $idx ) ?>">
															<?php } else { ?>
																<input type="text"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][common_name]"
																	   value="<?php echo htmlspecialchars( $i['ssl']['common_name'] ) ?>"
																	   class="form-control ssl-common-name"
																	   id="common-name-<?php echo esc_attr( $idx ) ?>"
																	   data-field="ssl_common_name_<?php echo esc_attr( $idx ) ?>">
															<?php } ?>
														</div>
													</div>

													<div class="form-group">
														<label class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.approver_email' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<div class="input-group">
																<input type="text"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][approver_email]"
																	   value="<?php echo esc_attr( $i['ssl']['approver_email'] ) ?>"
																	   class="form-control approver-email"
																	   data-field="ssl_approver_email_<?php echo esc_attr( $idx ) ?>">
																<span class="input-group-btn ssl-approver-email"
																	  data-id="<?php echo esc_attr( $idx ) ?>"
																	  data-product="<?php echo $i['product'] ?>">
																<button class="btn btn-default widget-dropdown-toggle" type="button">
																	<span class="caret"></span>
																</button>
																<ul class="dropdown-menu dropdown-menu-right">
																	<li class="dropdown-header"><?php echo icd_hosting_tr( 'request.valid_approver_emails' ) ?></li>
																	<li class="loading-email-spinner text-muted text-center pt-10 pb-10 display-none">
																		<i class="glyphicon glyphicon-refresh spin"></i>
																	</li>
																</ul>
															</span>
															</div>
														</div>
													</div>

													<?php if ( $i['parent_product'] == 'standalone:ssl' ) { ?>
														<input type="hidden"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][ip_type]"
															   value="noip">
													<?php } else { ?>
														<div class="form-group">
															<label class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.ip_type' ) ?>
																<span class="mandatory">*</span></label>
															<div class="col-md-6">
																<select name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][ip_type]"
																		class="form-control ssl-installation"
																		data-field="ssl_ip_type_<?php echo esc_attr( $idx ) ?>"
																		data-value="<?php echo $i['ssl']['ip_type'] ?>">
																	<option
																			value="noip"<?php if ( 'noip' == $i['ssl']['ip_type'] ) { ?> selected <?php } ?>>
																		<?php echo icd_hosting_tr( 'request.ssl_ip_type.noip' ) ?>
																	</option>
																	<option
																			value="sni"<?php if ( 'sni' == $i['ssl']['ip_type'] ) { ?> selected<?php } ?>>
																		<?php echo icd_hosting_tr( 'request.ssl_ip_type.sni' ) ?>
																	</option>
																	<?php if ( $i['dedicated_ips'] ) { ?>
																		<option
																				value="dedicated"<?php if ( 'dedicated' == $i['ssl']['ip_type'] ) { ?> selected<?php } ?>>
																			<?php echo icd_hosting_tr( 'request.ssl_ip_type.dedicated' ) ?>
																		</option>
																	<?php } ?>

																	<?php if (isset($i['new_dedicated_ip'])) { ?>
																	<option value="new"
																		<?php if ( $i['account_id'] and empty( $i['hosting_account']['calculated_periods'] ) ) { ?> disabled <?php } ?>
																		<?php if ( 'new' == $i['ssl']['ip_type'] ) { ?> selected<?php } ?>>
																		<?php echo icd_hosting_tr( 'request.ssl_ip_type.new' ) ?>
																	</option>
																	<?php } ?>

																</select>
															</div>
														</div>

														<div class="form-group <?php if ( in_array( $i['ssl']['ip_type'], [
															'noip',
															'sni'
														] ) ) { ?> display-none <?php } ?>"
															 id="dedicated-<?php echo esc_attr( $idx ) ?>">
															<label class="col-md-4 control-label">IP <span
																		class="mandatory">*</span></label>
															<div class="col-md-6">
																<select id="dedicated-options-<?php echo esc_attr( $idx ) ?>"
																		class="display-none">
																	<?php foreach ( $i['dedicated_ips'] as $ip ) { ?>
																		<option
																				value="<?php echo esc_attr( $ip ) ?>"<?php if ( $ip == $i['ssl']['ip'] ) { ?> selected<?php } ?>><?php echo esc_html( $ip ) ?></option>
																	<?php } ?>
																</select>
																<select id="new-options-<?php echo esc_attr( $idx ) ?>"
																		style="display: none">
																	<?php if ( ! empty( $order['dedicated_ips'][ $i['account_id'] ] ) ) {
																		foreach ( $order['dedicated_ips'][ $i['account_id'] ] as $ip ) {
																			?>
																			<option
																					value="<?php echo esc_attr( $ip ) ?>"<?php if ( $ip == $i['ssl']['ip'] ) { ?> selected<?php } ?>><?php echo esc_html( $ip ) ?></option>
																		<?php } ?>
																	<?php } elseif ( ! empty( $order['dedicated_ips'][ $i['parent'] ] ) ) {
																		foreach ( $order['dedicated_ips'][ $i['parent'] ] as $ip ) {
																			?>
																			<option
																					value="<?php echo esc_attr( $ip ) ?>"<?php if ( $ip == $i['ssl']['ip'] ) {
																				?> selected<?php } ?>><?php echo esc_html( $ip ) ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
																<select name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][ip]"
																		class="form-control"
																		id="ssl-ip-<?php echo esc_attr( $idx ) ?>"
																		data-field="ssl_ip_<?php echo esc_attr( $idx ) ?>">
																	<?php
																	if ( 'dedicated' == $i['ssl']['ip_type'] ) {
																		foreach ( $i['dedicated_ips'] as $ip ) {
																			?>
																			<option value="<?php echo $ip ?>"
																			        <?php if ( $ip == $i['ssl']['ip'] ) {
																			        ?>selected <?php
																			} ?>><?php echo esc_html( $ip ) ?></option>
																			<?php
																		}
																	} else if ( $i['ssl']['ip_type'] == 'new' ) { ?>
																		<?php if ( ! empty( $order['dedicated_ips'][ $i['account_id'] ] ) ) {
																			foreach ( $order['dedicated_ips'][ $i['account_id'] ] as $ip ) {
																				?>
																				<option
																						value="<?php echo esc_attr( $ip ) ?>"<?php if ( $ip == $i['ssl']['ip'] ) {
																					?> selected<?php } ?>><?php echo esc_html( $ip ) ?></option>
																			<?php } ?>
																		<?php } else if ( isset( $i['parent'] ) ) {
																			foreach ( $order['dedicated_ips'][ $i['parent'] ] as $ip ) {
																				?>

																				<option value="<?php echo esc_attr( $ip ) ?>"
																				        <?php if ( $ip == $i['ssl']['ip'] ){
																				        ?>selected<?php } ?>><?php echo esc_html( $ip ) ?></option>
																			<?php } ?>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
															<?php

															if ( isset( $i['account_id'] ) ) { ?>
																<div id="add-new-ip-<?php echo esc_attr( $idx ) ?>"
																	 class="display-none">
																	<button type="button"
																			class="btn btn-default add-new-ip"
																			title="<?php echo icd_hosting_tr( 'request.ssl_add_new_dedicated_ip' ) ?>"
																			data-request="request[action]=order&request[parent_resource_id]=<?php echo esc_attr( $i['parent_resource_id'] ) ?>&request[catalog_id]=<?php echo esc_attr( $i['new_dedicated_ip'] ) ?>"
																			data-id="<?php echo esc_attr( $idx ) ?>">
																		<i class="glyphicon glyphicon-plus"></i>
																	</button>
																</div>
															<?php } elseif ( $i['parent'] !== '' ) { ?>
																<div id="add-new-ip-<?php echo esc_attr( $idx ) ?>"
																	 class="display-none">
																	<button type="button"
																			class="btn btn-default add-new-ip"
																			title="<?php echo icd_hosting_tr( 'request.ssl_add_new_dedicated_ip' ) ?>"
																			data-request="request[action]=order&request[parent]=<?php echo $i['parent'] ?>&request[catalog_id]=<?php echo $i['new_dedicated_ip'] ?>"
																			data-id="<?php echo esc_attr( $idx ) ?>">
																		<i class="glyphicon glyphicon-plus"></i>
																	</button>
																</div>
															<?php } ?>
														</div>
													<?php } ?>

													<hr>
													<div class="form-group">
														<label
																class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.organization' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][organization]"
																   value="<?php echo esc_attr( $i['ssl']['organization'] ) ?>"
																   class="form-control"
																   data-field="ssl_organization_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<div class="form-group">
														<label
																class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.organization_unit' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][organization_unit]"
																   value="<?php echo esc_attr( $i['ssl']['organization_unit'] ) ?>"
																   class="form-control"
																   data-field="ssl_organization_unit_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<div class="form-group">
														<label
																class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.country' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<select name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][country]"
																	class="form-control"
																	data-field="ssl_country_<?php echo esc_attr( $idx ) ?>">
																<option value="">
																	-- <?php echo icd_hosting_tr( 'contacts.country' ) ?>
																	--
																</option>
																<?php foreach ( $countries as $iso => $country ) { ?>
																	<option
																			value="<?php echo esc_attr( $iso ) ?>" <?php if ( $iso == $i['ssl']['country'] ) { ?> selected="selected"<?php } ?>>
																		<?php echo esc_html( $country['country'] ) ?>
																	</option>
																<?php } ?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label
																class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.city' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][city]"
																   value="<?php echo esc_attr( $i['ssl']['city'] ) ?>"
																   class="form-control"
																   data-field="ssl_city_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<div class="form-group">
														<label
																class="col-md-4 control-label"><?php echo icd_hosting_tr( 'request.ssl.state' ) ?>
															<span class="mandatory">*</span></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][state]"
																   value="<?php echo esc_attr( $i['ssl']['state'] ) ?>"
																   class="form-control"
																   data-field="ssl_state_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<!--<div class="form-group">
														<label
																class="col-md-4 control-label"><?php /*echo tr( 'request.ssl.email' ) */ ?></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php /*echo esc_attr( $idx ) */ ?>][ssl][email]"
																   value="<?php /*echo $i['ssl']['email'] */ ?>"
																   class="form-control"
																   data-field="ssl_email_<?php /*echo esc_attr( $idx ) */ ?>">
														</div>
													</div>-->

													<div class="form-group">
														<label class="col-md-4 control-label">
															<?php echo icd_hosting_tr( 'request.ssl.address' ) ?>
															<?php if ( in_array( $i['product'], [
																'comodo_essential',
																'comodo_essential_wildcard'
															] ) ) { ?>
																<span class="mandatory">*</span>
															<?php } ?>
														</label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][address]"
																   value="<?php echo esc_attr( $i['ssl']['address'] ) ?>"
																   class="form-control"
																   data-field="ssl_address_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<!--<div class="form-group">
														<label
																class="col-md-4 control-label"><?php /*echo tr( 'request.ssl.address2' ) */ ?></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php /*echo esc_attr( $idx ) */ ?>][ssl][address2]"
																   value="<?php /*echo $i['ssl']['address2'] */ ?>"
																   class="form-control"
																   data-field="ssl_address2_<?php /*echo esc_attr( $idx ) */ ?>">
														</div>
													</div>-->

													<!--	<div class="form-group">
														<label
																class="col-md-4 control-label"><?php /*echo tr( 'request.ssl.address3' ) */ ?></label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php /*echo esc_attr( $idx ) */ ?>][ssl][address3]"
																   value="<?php /*echo $i['ssl']['address3'] */ ?>"
																   class="form-control"
																   data-field="ssl_address3_<?php /*echo esc_attr( $idx ) */ ?>">
														</div>
													</div>-->

													<div class="form-group">
														<label class="col-md-4 control-label">
															<?php echo icd_hosting_tr( 'request.ssl.zip' ) ?>
															<?php if ( in_array( $i['product'], [
																'comodo_essential',
																'comodo_essential_wildcard'
															] ) ) { ?>
																<span class="mandatory">*</span>
															<?php } ?>
														</label>
														<div class="col-md-6">
															<input type="text"
																   name="order[items][<?php echo esc_attr( $idx ) ?>][ssl][zip]"
																   value="<?php echo esc_attr( $i['ssl']['zip'] ) ?>"
																   class="form-control"
																   data-field="ssl_zip_<?php echo esc_attr( $idx ) ?>">
														</div>
													</div>

													<div class="form-group">
														<div class="col-md-6 col-md-offset-3 text-center">
															<button type="button" class="btn btn-primary btn-done"
																	data-toggle="config-<?php echo esc_attr( $idx ) ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
														</div>
													</div>
												</div>

											<?php } else if ( $i['action'] == 'upgrade' ) { ?>
												<h4><?php echo icd_hosting_tr( 'request.item_upgrade_subtitle' ) ?></h4>
												<?php if ( ! empty( $i['recommended_upgrade'] ) ) { ?>
													<div class="alert alert-warning">
														<?php
														$upgrade_info = [
															'current_plan' => $catalog[ $i['hosting']['catalog_id'] ]['name'],
															'upgrade_plan' => $catalog[ $i['catalog_id'] ]['name'],
														];

														echo icd_hosting_tr('request.recommended_upgrade_generic', $upgrade_info);
														?>
													</div>
												<?php } ?>
												<div data-id="<?php echo esc_attr( $idx ) ?>">
													<div class="col-md-4">
														<label><?php echo icd_hosting_tr( 'request.upgrade_from_plan_label' ) ?></label><br>
														<span class="form-control">
													<?php echo esc_html( $i['hosting_account']['account_plan'] ) ?>
												</span>
													</div>
													<div class="col-md-5">
														<label><?php echo icd_hosting_tr( 'request.upgrade_to_plan_label' ) ?></label><br>
														<select name="order[items][<?php echo esc_attr( $idx ) ?>][catalog_id]"
																class="form-control">
															<?php foreach ( $i['upgrade']['options'] as $catalog_id => $opt ) { ?>
																<option
																		value="<?php echo $catalog_id ?>"<?php if ( $catalog_id == $i['catalog_id'] ) { ?> selected <?php } ?>><?php echo esc_html( $opt['name'] ) ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-2">
														<label>&nbsp;</label><br>
														<button type="button" class="btn btn-primary btn-done"
																data-toggle="config-<?php echo esc_attr( $idx ) ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
													</div>
												</div>
											<?php } elseif ( $i['action'] == 'migrate' ) { ?>
												<h4><?php echo icd_hosting_tr( 'request.item_migrate_subtitle' ) ?></h4>
												<div class="form-horizontal col-md-10 col-md-offset-1"
													 data-id="<?php echo esc_attr( $idx ) ?>">
													<div class="col-md-5">
														<label><?php echo icd_hosting_tr( 'request.upgrade_from_plan_label' ) ?></label>
														<span class="form-control">
															<?php echo esc_html( $i['hosting_account']['account_plan'] ) ?>
														</span>
													</div>
													<div class="col-md-5">
														<label><?php echo icd_hosting_tr( 'request.upgrade_to_plan_label' ) ?></label>
														<select name="order[items][<?php echo esc_attr( $idx ) ?>][catalog_id]"
																class="form-control">
															<?php foreach ( $i['migrate']['options'] as $catalog_id => $opt ) { ?>
																<option value="<?php echo $catalog_id ?>"<?php if ( $catalog_id == $i['catalog_id'] ) { ?> selected <?php } ?>>
																	<?php echo $opt['name'] . ' ' . "({$opt['period']} " .  icd_hosting_tr("periodicity.{$opt['periodicity']}") .') - ' . $opt['currency'] . ' ' . sprintf('%.2f', $opt['price']); ?>
																</option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-2">
														<label>&nbsp;</label><br>
														<button type="button" class="btn btn-primary btn-done"
																data-toggle="config-<?php echo esc_attr( $idx ) ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
													</div>
												</div>

											<?php } elseif( $i['action'] == 'order' and $i['product'] == 'advanced_security' ) { ?>
												<div>
													<div class="form-group">
														<label class="col-md-3 control-label"><?php echo icd_hosting_tr('request.service.url') ?>: </label>
														<div class="col-md-6">
															<input type="text" name="order[items][<?php echo esc_attr( $idx ) ?>][service][url]" value="<?php echo esc_attr( $i['service']['url'] ) ?>"
																   class="form-control service-url" placeholder="http://my-domain.com/" data-field="service_url_<?php echo esc_attr( $idx ) ?>">
														</div>
														<div class="col-md-3">
															<button type="button" class="btn btn-primary btn-done" data-toggle="config-<?php echo esc_attr( $idx ) ?>"><?php echo icd_hosting_tr( 'request.btn.done' ) ?></button>
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									<?php } ?>


									<?php if ( isset( $i['contact'] ) ) { ?>
										<div class="item-icontact clearfix <?php if ( empty( $i['icontact'] ) ) { ?> display-none <?php } ?>"
											 id="icontact-<?php echo esc_attr( $idx ) ?>"
											 data-id="<?php echo esc_attr( $idx ) ?>">
											<h4><?php echo icd_hosting_tr( 'request.item_contact_subtitle' ) ?></h4>
											<div class="alert alert-warning">
												<?php echo icd_hosting_tr( 'request.item_contact_desc' ) ?>
											</div>
											<div class="form-group">
												<div class="col-md-12">
													<label class="radio-inline">
														<input type="radio"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][custom_contact]"
															   value="0"
															   class="custom-contact"<?php if ( empty( $i['custom_contact'] ) ) { ?> checked <?php } ?>>
														<?php echo icd_hosting_tr( 'request.use_order_billing_contact' ) ?>
													</label>
													<label class="radio-inline">
														<input type="radio"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][custom_contact]"
															   value="1"
															   class="custom-contact"<?php if ( ! empty( $i['custom_contact'] ) ) { ?> checked<?php } ?>>
														<?php echo icd_hosting_tr( 'request.use_custom_contact' ) ?>
													</label>
												</div>
											</div>

											<div class="custom-item-icontact <?php if ( empty( $i['custom_contact'] ) ) { ?> display-none <?php } ?>"
												 id="custom-item-icontact-<?php echo esc_attr( $idx ) ?>">
												<div class="form-group">
													<div class="col-md-6">
														<label for="contact_firstname"><?php echo icd_hosting_tr( 'contacts.firstname' ) ?>
															: <span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][firstname]"
															   value="<?php echo isset( $i['contact']['firstname'] ) ? esc_attr( $i['contact']['firstname'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.firstname' ) ?>"
															   data-field="contact_firstname_<?php echo esc_attr( $idx ) ?>">
													</div>
													<div class="col-md-6">
														<label for="contact_lastname"><?php echo icd_hosting_tr( 'contacts.lastname' ) ?>
															: <span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][lastname]"
															   value="<?php echo isset( $i['contact']['lastname'] ) ? esc_attr( $i['contact']['lastname'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.lastname' ) ?>"
															   data-field="contact_lastname_<?php echo esc_attr( $idx ) ?>">
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-6">
														<label for="contact_address"><?php echo icd_hosting_tr( 'contacts.address' ) ?>
															: <span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][address]"
															   value="<?php echo isset( $i['contact']['address'] ) ? esc_attr( $i['contact']['address'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.address' ) ?>"
															   data-field="contact_address_<?php echo esc_attr( $idx ) ?>">
													</div>
													<div class="col-md-6">
														<label for="contact_address2"><?php echo icd_hosting_tr( 'contacts.address2' ) ?>
															:</label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][address2]"
															   value="<?php echo isset( $i['contact']['address2'] ) ? esc_attr( $i['contact']['address2'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.address2' ) ?>"
															   data-field="contact_address2_<?php echo esc_attr( $idx ) ?>">
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-6">
														<label for="contact_city"><?php echo icd_hosting_tr( 'contacts.city' ) ?>
															:
															<span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][city]"
															   value="<?php echo isset( $i['contact']['city'] ) ? esc_attr( $i['contact']['city'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.city' ) ?>"
															   data-field="contact_city_<?php echo esc_attr( $idx ) ?>">
													</div>
													<div class="col-md-6">
														<label for="contact_state"><?php echo icd_hosting_tr( 'contacts.state' ) ?>
															:
															<span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][state]"
															   value="<?php echo isset( $i['contact']['state'] ) ? esc_attr( $i['contact']['state'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.state' ) ?>"
															   data-field="contact_state_<?php echo esc_attr( $idx ) ?>">
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-6">
														<label for="contact_zip"><?php echo icd_hosting_tr( 'contacts.zip' ) ?>
															:
															<span
																	class="mandatory">*</span></label>
														<input type="text"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][zip]"
															   value="<?php echo isset( $i['contact']['zip'] ) ? esc_attr( $i['contact']['zip'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.zip' ) ?>"
															   data-field="contact_zip_<?php echo esc_attr( $idx ) ?>">
													</div>
													<div class="col-md-6">
														<label for="contact_country"><?php echo icd_hosting_tr( 'contacts.country' ) ?>
															: <span
																	class="mandatory">*</span></label>
														<select name="order[items][<?php echo esc_attr( $idx ) ?>][contact][country]"
																class="form-control"
																data-field="contact_country_<?php echo esc_attr( $idx ) ?>">
															<option value="">
																-- <?php echo icd_hosting_tr( 'contacts.country' ) ?>
																--
															</option>
															<?php foreach ( $countries as $iso => $country ) { ?>
																<option
																		value="<?php echo esc_attr( $iso ) ?>"<?php if ( isset( $i['contact']['country'] ) and $iso == $i['contact']['country'] ) { ?> selected="selected"<?php } ?>>
																	<?php echo esc_html( $country['country'] ) ?>
																</option>
															<?php } ?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-6">
														<label
																for="contact_phone"><?php echo icd_hosting_tr( 'contacts.phone' ) ?>
															: <span
																	class="mandatory">*</span></label>
														<div class="row">
															<div class="col-md-4">
																<select name="order[items][<?php echo esc_attr( $idx ) ?>][contact][phone_country]"
																		class="form-control"
																		data-field="contact_phone_country_<?php echo esc_attr( $idx ) ?>">
																	<option value="">
																		-- <?php echo icd_hosting_tr( 'contacts.phone_country' ) ?>
																		--
																	</option>
																	<?php foreach ( $countries as $iso => $country ) { ?>
																		<option value="<?php echo esc_attr( $iso ) ?>"<?php if ( isset( $i['contact']['phone_country'] ) and $iso == $i['contact']['phone_country'] ) { ?> selected="selected"<?php } ?>>
																			<?php echo ( isset( $country['country'] ) and isset( $country['phone_code'] ) ) ? esc_attr( $country['country'] . ': ' . $country['phone_code'] ) : '' ?>
																		</option>
																	<?php } ?>
																</select>
															</div>
															<div class="col-md-8">
																<input type="text"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][phone]"
																	   value="<?php echo isset( $i['contact']['phone'] ) ? esc_attr( $i['contact']['phone'] ) : '' ?>"
																	   class="form-control"
																	   placeholder="<?php echo icd_hosting_tr( 'contacts.phone' ) ?>"
																	   data-field="contact_phone_<?php echo esc_attr( $idx ) ?>">
															</div>
														</div>
													</div>

													<div class="col-md-6">
														<label for="contact_fax"><?php echo icd_hosting_tr( 'contacts.fax' ) ?>
															: </label>
														<div class="row">
															<div class="col-md-4">
																<select name="order[items][<?php echo esc_attr( $idx ) ?>][contact][fax_country]"
																		class="form-control"
																		data-field="contact_fax_country_<?php echo esc_attr( $idx ) ?>">
																	<option value="">
																		-- <?php echo icd_hosting_tr( 'contacts.fax_country' ) ?>
																		--
																	</option>
																	<?php foreach ( $countries as $iso => $country ) { ?>
																		<option value="<?php echo esc_attr( $iso ) ?>"
																			<?php if ( isset( $i['contact']['fax_country'] ) and $iso == $i['contact']['fax_country'] ) { ?> selected="selected" <?php } ?>>
																			<?php echo esc_html( $country['country'] . ':' . $country['phone_code'] ); ?>
																		</option>
																	<?php } ?>
																</select>
															</div>
															<div class="col-md-8">
																<input type="text"
																	   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][fax]"
																	   value="<?php echo isset( $i['contact']['fax'] ) ? esc_attr( $i['contact']['fax'] ) : '' ?>"
																	   class="form-control"
																	   placeholder="<?php echo icd_hosting_tr( 'contacts.fax' ) ?>"
																	   data-field="contact_fax_<?php echo esc_attr( $idx ) ?>">
															</div>
														</div>
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-6">
														<label for="contact_email"><?php echo icd_hosting_tr( 'contacts.email' ) ?>
															:
															<span class="mandatory">*</span></label>
														<input type="email"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][email]"
															   value="<?php echo isset( $i['contact']['email'] ) ? esc_attr( $i['contact']['email'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.email' ) ?>"
															   data-field="contact_email_<?php echo esc_attr( $idx ) ?>">
													</div>
													<div class="col-md-6">
														<label for="contact_email2"><?php echo icd_hosting_tr( 'contacts.email2' ) ?>
															:</label>
														<input type="email"
															   name="order[items][<?php echo esc_attr( $idx ) ?>][contact][email2]"
															   value="<?php echo isset( $i['contact']['email2'] ) ? esc_attr( $i['contact']['email2'] ) : '' ?>"
															   class="form-control"
															   placeholder="<?php echo icd_hosting_tr( 'contacts.email2' ) ?>"
															   data-field="contact_email2_<?php echo esc_attr( $idx ) ?>">
													</div>
												</div>

												<div class="form-group">
													<div class="col-md-12 text-center">
														<button type="button" class="btn btn-primary toggle-item"
																data-toggle="icontact-<?php echo esc_attr( $idx ) ?>">
															<?php echo icd_hosting_tr( 'request.btn.done' ) ?>
														</button>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="8" class="text-right">
						<strong><?php echo icd_hosting_tr( 'request.order_total_label' ) ?>: </strong>
					</td>
					<td class="text-right">
						<strong>
							<?php echo esc_html( $order['currency'] ) ?>
							<span id="order-total"><?php echo sprintf( '%.2f', $order['total'] ) ?></span>
							<?php if ( $discounted_prices ) { ?>
								<span id="order-catalog-total"
									  style="text-decoration: line-through;"><?php sprintf( '%.2f', $catalog_total ) ?></span>
							<?php } ?>
						</strong>
					</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="10"></td>
				</tr>
				</tbody>
			</table>
		</div>

		<div id="personal-information">
			<h3><?php echo icd_hosting_tr( 'titles.personal_information' ) ?></h3>
			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_firstname"><?php echo icd_hosting_tr( 'contacts.firstname' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[firstname]" required="required"
						   value="<?php echo esc_attr( $order['firstname'] ) ?>"
						   id="contact_firstname" class="form-control" data-field="firstname"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.firstname' ) ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_lastname"><?php echo icd_hosting_tr( 'contacts.lastname' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[lastname]" required="required"
						   value="<?php echo esc_attr( $order['lastname'] ) ?>"
						   id="contact_lastname" class="form-control" data-field="lastname"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.lastname' ) ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_address"><?php echo icd_hosting_tr( 'contacts.address' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[address]" required="required"
						   value="<?php echo esc_attr( $order['address'] ) ?>"
						   id="contact_address" class="form-control" data-field="address"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.address' ) ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_address2"><?php echo icd_hosting_tr( 'contacts.address2' ) ?>:</label>
					<input type="text" name="order[address2]" value="<?php echo esc_attr( $order['address2'] ) ?>"
						   id="contact_address2"
						   class="form-control" data-field="address2"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.address2' ) ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_city"><?php echo icd_hosting_tr( 'contacts.city' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[city]" required="required" id="contact_city"
						   value="<?php echo esc_attr( $order['city'] ) ?>" class="form-control" data-field="city"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.city' ) ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_state"><?php echo icd_hosting_tr( 'contacts.state' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[state]" required="required"
						   value="<?php echo esc_attr( $order['state'] ) ?>"
						   id="contact_state" class="form-control" data-field="state"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.state' ) ?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_zip"><?php echo icd_hosting_tr( 'contacts.zip' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="text" name="order[zip]" required="required" id="contact_zip"
						   value="<?php echo esc_attr( $order['zip'] ) ?>" class="form-control" data-field="zip"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.zip' ) ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_country"><?php echo icd_hosting_tr( 'contacts.country' ) ?>: <span
								class="mandatory">*</span></label>
					<select class="form-control" name="order[country]" required="required" id="contact_country"
							data-field="country">
						<option value="">-- <?php echo icd_hosting_tr( 'contacts.country' ) ?> --</option>
						<?php foreach ( $countries as $iso => $country ) { ?>
							<option
									value="<?php echo esc_attr( $iso ) ?>" <?php if ( $iso == $order['country'] ) {
								echo 'selected="selected"';
							} ?>><?php echo esc_html( $country['country'] ) ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_phone"><?php echo icd_hosting_tr( 'contacts.phone' ) ?>: <span
								class="mandatory">*</span></label>
					<div class="row">
						<div class="col-md-4">
							<select class="form-control" name="order[phone_country]" required="required"
									id="contact_phone_country" data-field="phone_country">
								<option value="">-- <?php echo icd_hosting_tr( 'contacts.phone_country' ) ?> --</option>
								<?php foreach ( $countries as $iso => $country ) { ?>
									<option value="<?php echo esc_attr( $iso ) ?>"
									        <?php if ( $iso == $order['phone_country'] ) { ?>selected='selected' <?php } ?>>
										<?php echo esc_html( $country['country'] . ':' . $country['phone_code'] ) ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-8">
							<input type="text" name="order[phone]" id="contact_phone"
								   value="<?php echo esc_attr( $order['phone'] ) ?>" required="required"
								   class="form-control" data-field="phone"
								   placeholder=" <?php echo icd_hosting_tr( 'contacts.phone' ) ?>">
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<label for="contact_fax"><?php echo icd_hosting_tr( 'contacts.fax' ) ?>:</label>
					<div class="row">
						<div class="col-md-4">
							<select class="form-control" name="order[fax_country]" id="contact_fax_country"
									data-field="fax_country">
								<option value="">-- <?php echo icd_hosting_tr( 'contacts.fax_country' ) ?> --</option>
								<?php foreach ( $countries as $iso => $country ) { ?>
									<option value="<?php echo esc_attr( $iso ) ?>" <?php if ( $iso == $order['fax_country'] ) { ?> selected='selected' <?php } ?>>
										<?php echo esc_attr( $country['country'] . ':' . $country['phone_code'] ) ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-8">
							<input type="text" name="order[fax]" id="contact_fax"
								   value="<?php echo esc_attr( $order['fax'] ); ?>"
								   class="form-control" data-field="fax"
								   placeholder="<?php echo icd_hosting_tr( 'contacts.fax' ); ?>">
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-5 col-md-offset-1">
					<label for="contact_email"><?php echo icd_hosting_tr( 'contacts.email' ) ?>: <span
								class="mandatory">*</span></label>
					<input type="email" name="order[email]" id="contact_email" required="required"
						   value="<?php echo esc_attr( $order['email'] ) ?>" class="form-control" data-field="email"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.email' ) ?>">
				</div>
				<div class="col-md-5">
					<label for="contact_email2"><?php echo icd_hosting_tr( 'contacts.email2' ) ?>:</label>
					<input type="email" name="order[email2]" id="contact_email2"
						   value="<?php echo esc_attr( $order['email2'] ) ?>"
						   class="form-control" data-field="email2"
						   placeholder="<?php echo icd_hosting_tr( 'contacts.email2' ) ?>">
				</div>
			</div>
		</div>

		<?php if ( $order['total'] > 0 and $processors ) { ?>
			<div id="payment-information">
				<h3><?php echo icd_hosting_tr( 'titles.payment_method' ) ?></h3>
				<?php foreach ( $processors as $key => $display_name ) { ?>
					<div class="form-group">
						<div class="col-md-9 col-md-offset-3">
							<div class="checkbox">
								<label>
									<input type="radio" name="order[payment_method]"
										   value="<?php echo esc_attr( $key ) ?>" <?php if ( $key == $payment_method ) {
										echo 'checked="checked"';
									} ?>>
									<?php echo icd_hosting_tr( $display_name ) ?></label>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<div>
			<h3><?php echo icd_hosting_tr( 'titles.terms_and_agreements' ) ?></h3>

			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo icd_hosting_tr( 'labels.terms' ) ?>:</label>
				<div class="col-md-8">
					<div class="checkbox pl20">
						<label>
							<input type="checkbox" name="terms"
								   value="1"<?php if ( $terms ) { ?> checked="checked"<?php } ?>
								   data-field="terms">
							<?php echo icd_hosting_tr( 'labels.terms_agree', [ 'url' => icd_hosting_url( 'terms' ) ] ) ?>
						</label>
					</div>
				</div>
			</div>

			<div class="form-group <?php if ( ! $icann_show ) { ?>display-none<?php } ?>" id="order-domain-icann">
				<label class="col-md-3 control-label">
					<?php echo icd_hosting_tr( 'labels.icann_verification' ) ?>:
					<a href="#" data-toggle="modal" data-target="#icann-modal" class="label label-primary">?</a>
				</label>
				<div class="col-md-8">
					<div class="checkbox pl20">
						<label>
							<input type="checkbox" name="icann"
								   value="1" <?php if ( $icann ) { ?> checked="checked"<?php } ?>
								   data-field="icann">
							<?php echo icd_hosting_tr( 'labels.icann_agree' ) ?>
						</label>
					</div>
				</div>

			</div>

		</div>

		<div>
			<div class="form-group">
				<div class="col-md-6 col-md-offset-3 text-center">
					<br>
					<input type="hidden" name="order[request_id]" value="<?php echo $request_id ?>">
					<button type="submit" class="btn btn-primary"
							id="create-order"><?php echo icd_hosting_tr( 'btns.order' ) ?></button>
				</div>
			</div>
		</div>
	</form>

	<div class="modal fade" id="epp-modal" tabindex="-1" role="dialog" data-backdrop="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header popover-title">
					<h4 class="modal-title"><?php echo icd_hosting_tr( 'request.epp_code' ) ?></h4>
				</div>

				<div class="modal-body">
					<?php echo icd_hosting_tr( 'request.domain_search.epp_info' ) ?>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i
								class="glyphicon glyphicon-close"></i> <?php echo icd_hosting_tr( 'btns.close' ) ?></button>
				</div>
			</div>
		</div>

	</div>

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
								class="glyphicon glyphicon-close"></i> <?php echo icd_hosting_tr( 'btns.close' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" id="parking-modal" tabindex="-1" role="dialog" data-backdrop="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header popover-title">
					<h4 class="modal-title"><?php echo icd_hosting_tr('request.host_as') ?></h4>
				</div>

				<div class="modal-body">
					<?php echo icd_hosting_tr('request.host_as_info') ?>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i class="glyphicon glyphicon-close"></i> <?php echo icd_hosting_tr('btns.close') ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" id="dns-modal" tabindex="-1" role="dialog" data-backdrop="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header popover-title">
					<h4 class="modal-title"><?php echo icd_hosting_tr( 'request.dns_configuration' ) ?></h4>
				</div>

				<div class="modal-body">
					<?php echo icd_hosting_tr( 'request.dns_configuration_info' ) ?>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal"><i
								class="glyphicon glyphicon-close"></i> <?php echo icd_hosting_tr( 'btns.close' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$ = jQuery = window.jQuery.noConflict(true);
	$(function () {
		if (!RequestForm.params.currency) {
			Widget.loadTr(<?php echo wp_json_encode( icd_hosting_js_lang( 'request' ) ) ?>);

			RequestForm.init({
				currency: <?php echo wp_json_encode( $store['currency'] ) ?>,
				tlds: <?php echo wp_json_encode( $tlds ) ?>
			});
		}
		$('.dropdown-toggle').dropdown()
	});
</script>
