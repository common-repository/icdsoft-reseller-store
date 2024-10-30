<div class="modal fade" id="certificates-modal" tabindex="-1" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-xl modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo icd_hosting_tr('certificate.label.certificates_info') ?></h5>
			</div>

			<div class="modal-body">
				<table class="table table-striped1 mt-4 mb-4">
					<thead>
					<tr>
						<th class="border-top-0"></th>
						<th class="border-top-0 text-center" colspan="2">
							<img alt="GeoTrust" src="<?php echo \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/img/geotrust_digicert.svg'?>" width="160" class="tooltipui" data-placement="bottom" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.desc.geotrust') ?>">
						</th>
						<th class="border-top-0 text-center pb-3" colspan="2">
							<img alt="Sectigo" src="<?php echo \ICD\Hosting\ICD_Hosting()->plugin_url() . '/assets/img/sectigo.png'?>" class="tooltipui" data-placement="bottom" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.desc.sectigo') ?>">
						</th>
					</tr>
					<tr>
						<th></th>
						<th class="text-center text-pink-darker">RapidSSL®</th>
						<th class="text-center text-pink-darker">Geotrust QuickSSL® Premium</th>
						<th class="text-center text-pink-darker">Sectigo Essential</th>
						<th class="text-center text-pink-darker">Sectigo Essential Wildcard</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.price_per_year') ?></td>
						<td class="text-center price_geotrust_rapidssl">-</td>
						<td class="text-center price_geotrust_quickssl_premium">-</td>
						<td class="text-center price_sectigo_essential">-</td>
						<td class="text-center price_sectigo_essential_wildcard">-</td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.validation_type') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.validation_type') ?>"></span>
						</td>
						<td class="text-center">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.domain_validation') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.domain_validation') ?>"></span>
						</td>
						<td class="text-center">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.domain_validation') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.domain_validation') ?>"></span>
						</td>
						<td class="text-center">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.domain_validation') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.domain_validation') ?>"></span>
						</td>
						<td class="text-center">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.domain_validation') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="left" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.domain_validation') ?>"></span>
						</td>
					</tr>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.warranty') ?></td>
						<td class="text-center">$10K</td>
						<td class="text-center">$100K</td>
						<td class="text-center">$10K</td>
						<td class="text-center">$10K</td>
					</tr>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.subdomains_secured') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.single') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.single') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.single') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.unlimited') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.issuance') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.issuance') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_hours') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.10_min') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_hours') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_days') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.validity_options') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.validity_options') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_years') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_years') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_years') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.1_2_years') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.site_seal') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui"  data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.site_seal') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.static') ?></td>
						<td class="text-center">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.dynamic') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.dynamic') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.static') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.static') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.level_of_encryption') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.up_to_256_bit') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.up_to_256_bit') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.up_to_256_bit') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.up_to_256_bit') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.browser_compatibility') ?></td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.browser_security_lock') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.browser_security_lock') ?>"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.idn_support') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.idn_support') ?>"></span>
						</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
					</tr>
					<tr>
						<td class="font-weight-bold"><?php echo icd_hosting_tr('certificate.ssl_info.label.renewal_reminders') ?></td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
					</tr>

					<!--<tr>
						<td class="font-weight-bold">
							<?php /*echo icd_hosting_tr('certificate.ssl_info.label.renewal_benefits') */?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-content="<?php /*echo icd_hosting_tr('certificate.ssl_info.tip.renewal_benefits') */?>"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
						<td class="text-center">
							<span class="glyphicon glyphicon-ok text-primary"></span>
						</td>
					</tr>-->

					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.revocation_and_replacement') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui"  data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.revocation_and_replacement') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.free') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.free') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.free') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.free') ?></td>
					</tr>
					<tr>
						<td class="font-weight-bold">
							<?php echo icd_hosting_tr('certificate.ssl_info.label.free_refund') ?>
							<span class="glyphicon glyphicon-info-sign tooltipui" data-placement="right" data-content="<?php echo icd_hosting_tr('certificate.ssl_info.tip.free_refund') ?>"></span>
						</td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.5_days') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.5_days') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.25_days') ?></td>
						<td class="text-center"><?php echo icd_hosting_tr('certificate.ssl_info.label.25_days') ?></td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="glyphicon glyphicon-close"></i> <?php echo icd_hosting_tr('btns.close') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="certificate-installation-modal" tabindex="-1" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo icd_hosting_tr('certificate.label.certificate_installation') ?></h5>
			</div>

			<div class="modal-body">
				<?php echo icd_hosting_tr('certificate.desc.certificate_installation') ?>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-close"></i> <?php echo icd_hosting_tr('certificate.btn.close') ?></button>
			</div>
		</div>
	</div>
</div>
