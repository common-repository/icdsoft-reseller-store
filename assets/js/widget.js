(function ($) {
	function validPath(obj, path) {
		var i, key, keys = path.replace('\\.', '§').split('.');
		for (i = 0; i < keys.length; i++) {
			key = keys[i].replace('§', '.');
			if (typeof obj[key] == "undefined") {
				return false;
			}

			obj = obj[key];
		}
		return true;
	}

	function scrollTo(obj) {
		$('html, body').animate({
			scrollTop: obj.offset().top - 20
		}, 300);
	}

	function array_get(arr, key, d) {
		if (typeof d === 'undefined'){
			d = null;
		}

		if (typeof key === 'undefined'){
			return arr;
		}

		if (typeof arr[key] !== 'undefined'){
			return arr[key];
		}

		var found = false;
		var parts = key.toString().split('.');
		var sub = [];
		for(a in parts){
			sub.push(parts[a]);
			parts.splice(a,1);
			if (arr[sub.join('.')]){
				found = true;
				break;
			}
		}

		if (!found){
			return d;
		}

		var new_arr = arr[sub.join('.')];
		var new_key = parts.length > 0 ? parts.join('.') : null;
		return array_get(new_arr, new_key, d);

	}

	HostingOrderForm = {
		formdata: {},
		order_currency: 'USD',

		init: function (formdata) {
			this.formdata = formdata;
			this.order_currency = formdata.order_currency;

			// Firefox - fix back button from payment processor page
			$('#hosting-order-form :input').prop('disabled', false);

			$('#hosting-order-form')
				.off('submit.form').on('submit.form', $.proxy(this.placeOrder, this))
				.off('change.period').on('change.period', '#hosting-order-period', $.proxy(this.changePeriod, this))
				.off('change.plan').on('change.plan', '.hosting-order-plan', $.proxy(this.rebuildPeriods, this))
				.off('change.new_domain').on('change.new_domain', '.hosting-new-domain', $.proxy(this.changeNewDomain, this))
				.off('click.search').on('click.search', '#domain-search-btn', $.proxy(this.checkDomain, this))
				.off('change.location').on('change.location', '.hosting-order-location', $.proxy(this.rebuildPlanSelector, this))
				//.off('blur.domain').on('blur.domain', '#domain-search-domain', $.proxy(this.computePrices, this))
				.off('keydown.domain').on('keydown.domain', '#domain-search-domain', function (e) {
				if (e.keyCode == 13 && $('#hosting-order-form .hosting-new-domain:checked').val() == '1') {
					$('#domain-search-btn').trigger('click');
					e.preventDefault();
				}
			}).off('change.domain').on('change.domain', '#domain-search-domain', function (e) {
				HostingOrderForm.computePrices();
				if ($('#hosting-order-form .hosting-new-domain:checked').val() == '1') {
					HostingOrderForm.checkDomain();
					e.preventDefault();
				} else {
					DomainSearchForm.validateDomain(1);
				}
			})
				.off('change.country').on('change.country', '#contact_country', function (e) {
				var iso2 = $(this).val();
				if ($('#contact_phone').val() == '' || $('#contact_phone_country').val() == '')
					$('#contact_phone_country').val(iso2);
				if ($('#contact_fax').val() == '' || $('#contact_fax_country').val() == '')
					$('#contact_fax_country').val(iso2);
			})
				.off('change.phone').on('change.phone', '#contact_phone,#contact_fax', function (e) {
				$(this).val($(this).val().replace(/[^\d]/g, ''));
			}).off('click.check-tlds').on('click.check-tlds', 'a.check-all-tlds', DomainSearchForm.checkTLDs)

			.off('click.select_domain').on('click.select_domain', 'a[data-domain]', function () {
				Widget.clearMsgs('#domain-search-form');

				$('#domain-search-result span.domain-search-selected:visible').hide();
				$(this).next().show();
				$('#domain-search-domain').val($(this).attr('data-domain'));

				DomainSearchForm.showExtraAttributes($(this).attr('data-tld'));
				HostingOrderForm.computePrices();
				return false;
			})
				.on('change.extra-attributes', 'select.extra-attributes-select', function () {
					$('#' + $(this).attr('id') + '_error').hide();
					HostingOrderForm.refreshExtraAttributes($(this).closest('div.extra-attributes-domain'));
				});

			HostingOrderForm.refreshExtraAttributes($('div.extra-attributes-inner', '#domain-extra-attributes-container'));
			this.computePrices();
		},
		changePeriod: function (e) {
			this.formdata.period = $(e.target).val();
			this.computePrices();
		},
		changeNewDomain: function (e) {
			this.formdata.new_domain = $(e.target).val();

			if (this.formdata.new_domain == '1' && DomainSearchForm.domainParts()) {
				this.checkDomain();
			}

			this.computePrices();
		},
		placeOrder: function () {
			if ($('.hosting-new-domain:checked').val() == 1 && !$('a[data-domain="' + $('#domain-search-domain').val() + '"]')[0]) {
				HostingOrderForm.checkDomain();
				scrollTo($('#hosting-order-form'));
				return false;
			}

			var request = new Request({progress: true, disabled: '#hosting-order-form :input'})
				.success(function (result) {
					if (result.payment_form)
						$('#hosting-order-form').after(result.payment_form);
				})
				.always(function (result) {
					if (result.payment_form || result.redirect_to)
						Widget.spi('#hosting-order-form :input');
				})
				.post(Widget.url($('#hosting-order-form').attr('action')), $('#hosting-order-form').serialize());

			return false;
		},
		checkDomain: function () {
			DomainSearchForm.domainSearch();
			DomainSearchForm.showExtraAttributes();
			this.computePrices();
		},
		computePrices: function () {
			var plandata =  HostingOrderForm.getPlansData(true);
			var new_domain = HostingOrderForm.formdata.new_domain;
			var qty = HostingOrderForm.formdata.period; //$('#hosting-order-period').val();
			var plan_price = validPath(plandata, 'order.' + qty + '.price') ? parseFloat(plandata.order[qty].price) : 0;
			var domain_parts = DomainSearchForm.domainParts();
			var tld = domain_parts && domain_parts[2] ? domain_parts[2] : '';
			var domain_group = (plandata['product_type'] != 'undefined' && plandata['product_type'] == 'server') ? 'extra:domain' : 'bonus:domain';

			if (plandata['bonus:domain'] || plandata['extra:domain']) {
				$('#hosting-order-form .hosting-new-domain').prop('disabled', false);
				$('#hosting-order-form .hosting-new-domain[value=' + new_domain + ']').prop('checked', true);
				$('#hosting-order-form .domain-search-controls').css('display', new_domain == '1' ? '' : 'none');
				if (new_domain == '1' && $('#domain-search-tlds input[value="' + tld + '"]').data('icann') == '1' && validPath(plandata, domain_group + '.' + tld + '.order.' + qty + '.price')) {
					$('#order-domain-icann').show().find(':input').prop('required', true);
				} else {
					$('#order-domain-icann').hide().find(':input').prop('required', false);
					$('#icann-info').hide();
				}

			} else {
				new_domain = 0;
				$('#hosting-order-form .hosting-new-domain[value=0]').prop('checked', true);
				$('#hosting-order-form .hosting-new-domain').prop('disabled', true);
				$('#hosting-order-form .domain-search-controls').hide();
				$('#order-domain-icann').hide().find(':input').prop('required', false);
				$('#icann-info').hide();
			}

			var domain_price = 0;
			var domain_total = '';
			var currency = HostingOrderForm.order_currency;
			if (new_domain == 1 && domain_parts && domain_parts[1]) {
				domain_total = Widget.tr('domain_search.not_offered');
				if (validPath(plandata, domain_group + '.' + tld.replace(/\./, '\\.') + '.order.' + qty + '.price')) {
					domain_price = parseFloat(plandata[domain_group][tld].order[qty].price);
					domain_total = currency + ' ' + domain_price.toFixed(2);
				}
				$('#hosting-price-domain').html(domain_total).show();
				$('#domain-search-tlds :input:checked').each(function () {
					var domain_total = '-', tld = $(this).val();

					if (validPath(plandata, domain_group + '.' + tld.replace(/\./, '\\.') + '.order.' + qty + '.price'))
						domain_total = currency + ' ' + parseFloat(plandata[domain_group][tld].order[qty].price).toFixed(2);

					$('#domain-search-result .domain-price-' + tld.replace(/\./, '\\.')).html(domain_total);
				});

				if (DomainSearchForm.extra_attributes_html[tld]) {
					$('div.extra-attributes').show();
				}
			} else {
				$('#hosting-price-domain').hide();
				$('div.extra-attributes').hide();
			}

			$('#hosting-price-plan').html(currency + ' ' + plan_price.toFixed(2));
			$('#hosting-price-order').html(currency + ' ' + (plan_price + domain_price).toFixed(2));

			$('#hosting-order-form #payment-information').css('display', plan_price + domain_price > 0 ? '' : 'none');
		},
		rebuildPeriods: function (e) {
			if (e) this.formdata.plan = $(e.target).val();

			$('.plan-row.bg-light').removeClass('bg-light');
			$('.hosting-order-plan:checked').closest('.plan-row').addClass('bg-light');

			var periods_data = this.getPlansData(true);
			var periods = periods_data.order;
			var select = $('#hosting-order-period');

			select.empty();
			for (var p in periods) {
				select.append('<option value="' + p + '"' + (this.formdata.period == p ? ' selected="selected"' : '') + '>' + p + ' ' + periods[p].period_label + ' - ' + this.order_currency + ' ' + parseFloat(periods[p].price).toFixed(2) + '</option>');
			}

			this.formdata.period = select.val();
			this.computePrices();
		},
		rebuildPlanSelector: function (e) {
			this.formdata.location = $(e.target).val();

			var container = $('#grouped-plans-' + this.formdata.location);
			var selected_plan = $('.hosting-order-plan[value="' + this.getPlansData(true, true) + '"]', container);

			$('.grouped-plans').hide();
			container.show();
			selected_plan.prop('checked', true);

			this.rebuildPeriods();
		},
		getPlansData: function (selection, keyonly) {
			var plans = this.formdata.catalog[this.formdata.location];
			if (!selection)
				return plans;

			var plan = this.formdata.plan;
			if (plan.lastIndexOf('_') != -1)
				plan = plan.substring(0, plan.lastIndexOf('_'));

			var selected_plan;
			for (var key in plans) {
				if (!selected_plan || key == plan || key.indexOf(plan + '_') != -1)
					selected_plan = key;
			}

			this.formdata.plan = selected_plan;
			return keyonly ? selected_plan : plans[selected_plan];
		},
		refreshExtraAttributes: function (container, init) {
			container = $(container);

			// Hide all not required fields
			container.find('select, input, textarea').each(function () {
				if ($(this).data('ext-required') > 1) {
					$(this).prop('disabled', true);
					$(this).closest('div.form-group').hide();

					$(this).removeClass('required');
				} else {
					$(this).addClass('required');
				}
			});

			container.find('.ea-help-msg').hide();

			container.find('select').each(function () {
				var selected = $(this).val() ? $('option[value=' + $(this).val() + "]", this) : false;

				if (selected) {
					var requires = selected.data('ext-requires');

					if (requires) {
						requires.split(',').forEach(function(r) {
							var ref = container.find(':input.extra-attributes-' + r);
							ref.prop('disabled', false).addClass('required').closest('div.form-group').show();
						});
					}
				}

				// hide based on filter (data-ext-hide)
				var select_ref = $(this);
				var filter_options = select_ref.find('option[data-ext-hide]');

				filter_options.each(function () {
					var hide = $(this).data('ext-hide');
					var splitted = hide.split(':');

					if (container.find(':input.extra-attributes-' + splitted[0]).val() == splitted[1]) {
						$(this).prop('disabled', true);
					} else {
						$(this).prop('disabled', false);
					}
				});

				// Help messages
				if (selected) {
					var help_msg = selected.data('ext-help-msg');

					if (help_msg) {
						var splitted = help_msg.split(':');
						container.find('.ea-help-msg-' + splitted[0] + '-' + splitted[1]).show();
					}
				}

				// If the option is hidden by the filter - show placeholder
				if (select_ref.val() == null) {
					select_ref.val(null).trigger('change.select2');
				}

				// Only one option - select it on init
				if (init && select_ref.find('option[value!=""]:enabled').length == 1) {
					select_ref.val(select_ref.find('option[value!=""]:enabled').val()).trigger('change');
				}
			});
		}
	}

	DomainSearchForm = {
		parts: {},
		search_requests: {},
		search_results: {},
		extra_attributes_html: {},
		_domain_check_nonce: '',
		init: function () {
			DomainSearchForm._domain_check_nonce = $('#_domain_check_nonce').val();

			$('#domain-search-form')
				.off('submit.domain_check').on('submit.domain_check', $.proxy(this.domainSearch, this))
				.off('click.check-tlds').on('click.check-tlds', 'a.check-all-tlds', DomainSearchForm.checkTLDs)
			;
		},
		checkTLDs: function () {
			var checked = $(this).data('checked') == '1' ? 0 : 1;
			$(this).data('checked', checked);
			$('input', '#domain-search-tlds').prop('checked', checked);
			return false;
		},
		globalTLDs: function() {
			return ["asia", "at", "be", "bg", "biz", "bz", "ca", "cc", "ch", "cn", "co", "co.com", "co.nz", "co.uk",
				"com", "com.au", "com.cn", "com.hk", "com.mx", "com.tw", "de", "edu.hk", "es", "eu", "eu.com",
				"fr", "hk", "hosting", "idv.hk", "idv.tw", "in", "info", "it", "jp", "me", "me.uk", "mobi", "name",
				"net", "net.au", "net.cn", "net.hk", "net.nz", "nl", "nu", "org", "org.cn", "org.hk", "org.nz", "org.tw", "org.uk",
				"support", "tv", "tw", "uk", "us", "us.com", "ws", "\u0435\u044e"];
		},
		domainParts: function (domain) {
			if (!domain)
				domain = $('#domain-search-domain').val();

			domain = $.trim(domain).toLowerCase().replace(/^http(?:s)?:\/\//i, '').replace(/^www\./i, '');

			var tlds = DomainSearchForm.globalTLDs();
			var pattern = DomainSearchForm.hostnameCharsPattern();
			var exp =  new RegExp('((?!-)' + pattern + '{1,63}(?!-))(?:\\.(' + tlds.join('|').replace(/\./g, '\\\.')  + '))?$', 'u');

			return exp.exec(domain);
		},
		hostnameCharsPattern: function() {
			return '[a-z0-9\-\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0-\u08B2\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58\u0C59\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D60\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F4\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19C1-\u19C7\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FCC\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA69D\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA78E\uA790-\uA7AD\uA7B0\uA7B1\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB5F\uAB64\uAB65\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]';
		},
		validHostname: function(hostname) {
			var pattern = DomainSearchForm.hostnameCharsPattern();
			var exp = new RegExp('^(' + pattern + '+\.)+' + pattern + '{2,15}$', 'u');
			return exp.exec(hostname);
		},
		validateDomain: function(existing) {
			Widget.clearMsgs('#domain-search-form');
			var search_domain = $('#domain-search-domain').val();
			if (search_domain === '')
				return false;

			var seach_domain_lower = $.trim(search_domain).toLowerCase();
			if (seach_domain_lower !== search_domain) {
				search_domain = seach_domain_lower;
				$('#domain-search-domain').val(seach_domain_lower);
			}

			DomainSearchForm.parts = DomainSearchForm.domainParts(search_domain);
			if (!DomainSearchForm.parts || existing && !DomainSearchForm.validHostname(search_domain)) {
				Widget.msg('error', Widget.tr('domain_search.invalid'), 'domain');
				return false;
			};

			return true;
		},
		domainSearch: function () {
			if (!DomainSearchForm.validateDomain()) {
				return false;
			}

			if (DomainSearchForm.parts[2]) {
				$('input[value="' + DomainSearchForm.parts[2] + '"]', '#domain-search-tlds').prop('checked', 1);
			}

			if ($(':checkbox:checked', '#domain-search-tlds').length < 1) {
				Widget.msg('error', Widget.tr('domain_search.choose_search_tlds'));
				return false;
			}

			// abort not finished old requests
			if (DomainSearchForm.search_requests) {
				for (i in DomainSearchForm.search_requests) {
					DomainSearchForm.search_requests[i].abort();
				}
			}
			DomainSearchForm.search_requests = [];
			DomainSearchForm._domain_check_nonce = $('#_domain_check_nonce').val();

			var requests = [];
			var result_container = $('#domain-search-result').empty();
			$(':checkbox:checked', '#domain-search-tlds').each(function () {
				var domain = DomainSearchForm.parts[1] + '.' + $(this).val();
				var tld = $(this).val();
				result_container.append('<div id="' + Widget.hashId(domain) + '" class="row">' +
					'<div class="col-md-5 col-xs-12 domain-name" style="word-break: break-word">' + domain + '</div>' +
					'<div class="col-md-4 col-xs-6 domain-action"><span class="loader-small"></span></div>' +
					'<div class="col-md-3 col-xs-6 price-amount domain-price-' + $(this).val() + ' text-right text-success"></div>' +
					'</div>');

				var callback = (function (domain_name, tld) {
					return function (result) {
						DomainSearchForm.search_results[domain_name] = result;

						if (result.status && result.data.available == 'register') {
							var html = '<strong>' +
								'<a href="' + result.data.order_link + '" data-tld="' + tld + '" data-domain="' + domain_name + '">' +
								Widget.tr('domain_search.available.1') + '</a> ' +
								'<span class="domain-search-selected" style="' + (domain_name != DomainSearchForm.parts[0] ? 'display:none' : '') + '">✓</span>' +
								'</strong>';
						} else if (result.status && (result.data.available == 'transfer' || result.data.available == 'unavailable')) {
							var html = '<span class="text-warning">' + Widget.tr('domain_search.available.0') + '</span>';
						} else {
							var html = '<i class="glyphicon glyphicon-exclamation-sign tooltipui alert-danger" data-content="' +
								Widget.tr('domain_search.error') + '"></i>';
						}

						if (result.status && result.data.extra_attributes_html) {
							DomainSearchForm.extra_attributes_html[tld] = result.data.extra_attributes_html;
						}

						$('.domain-action', '#' + Widget.hashId(domain_name)).html(html);
					}
				})(domain, tld);

				if (DomainSearchForm.search_results[domain]) {
					callback(DomainSearchForm.search_results[domain]);
				} else {
					var req = new Request;
					DomainSearchForm.search_requests.push(req.success(callback).get(Widget.url($('#domain-search-form').attr('action')), {
						'action': 'icd_hosting_domain_check',
						'domain': domain,
						'render_extra_attributes': 1,
						'setup': 'order',
						'idx': 1,
						'prefix': 'domain_extra_attributes',
						'_domain_check_nonce': DomainSearchForm._domain_check_nonce,
					}));
				}
			});

			// Widget.spi('button');
			$.when.apply(null, DomainSearchForm.search_requests)
				.done(function () {
					var selected = $('a[data-domain="' + $('#domain-search-domain').val() + '"]');
					if ($('#hosting-order-form')[0] && !selected[0])
						$('a:first', '#domain-search-result').click();
					else if (selected[0])
						DomainSearchForm.showExtraAttributes(selected[0].attributes['data-tld'].value);
				})
				//.always(function () {
				//	Widget.hpi('button');
				//})
				;

			return false;
		},
		showExtraAttributes: function (tld) {
			var container = '#domain-extra-attributes-container';

			if (DomainSearchForm.extra_attributes_html[tld]) {
				var extra_form = $('div.extra-attributes-inner', container);
				if (extra_form.data('tld') != tld) {
					extra_form.data('tld', tld).html(DomainSearchForm.extra_attributes_html[tld]);
					HostingOrderForm.refreshExtraAttributes(extra_form);
				}

				$('div.extra-attributes', $(container)).show();
			} else {
				$('div.extra-attributes', $(container)).hide();
			}
		},
	}

	PaymentForm = {
		isOffline: function (pm) {
			if (pm == 'Offline' || pm == 'Bank' || pm == 'Cash' || pm == 'Check' || pm == 'WesternUnion')
				return 1;
			return 0;
		},
		init: function () {
			var p_method = $('#hosting-payment-form .payment_procs:checked:first').data();
			var selecterd_p_method =  $('input[name=payment_method]:checked', '#hosting-payment-form');

			// Firefox - fix back button from payment processor page
			$('#hosting-payment-form :input').prop('disabled', false);

			if (p_method && PaymentForm.isOffline(p_method.type) || p_method.type == 'PayPal' && p_method.onsite == 1) {
				$('#payment-btn').hide();
			}

			$('#hosting-payment-form')
				.off('submit.form')
				.on('submit.form', $.proxy(this.makePayment, this))
				.off('change.pm')
				.on('change.pm', '.payment_procs', $.proxy(this.changeSelection, this));

			if ($('.payment_procs:checked').length) {
				$('.payment_procs:checked').trigger('change');
			}
		},
		startPaypal: function (container) {
			var processor_id = container.data('processor-id');
			let order_id = container.data('order_id');
			let element_id = '#paypal-container-' + processor_id;
			if (!$(element_id).length) {
				return
			}
			$('#complete-order').hide();
			$(element_id).empty();
			paypal.Buttons({
				async createOrder(data, actions) {
					Widget.spi();
					var form = document.getElementById('hosting-payment-form');
					const form_data = new FormData(form);
					form_data.append('session', true);

					const response = await fetch(form.attributes['action'].value, {
						headers: {
							'Accept': 'application/json',
						},
						method: "POST",
						body: form_data
					});
					const result = await response.json();

					Widget.hpi();

					if (result.session) {
						return result.session.id;
					} else {
						Widget.msgs(result.messages);
					}
				},
				async onApprove(data, actions) {
					Widget.spi();
					actions.order.capture().then(function (result) {
						if (!order_id) {
							order_id = result['purchase_units'][0]['custom_id'];
						}
						let webhook_data = {
							processor_id: processor_id,
							order_id: order_id,
							data: {
								'resource': result,
								'resource_type': 'capture',
								'event_type': 'PAYMENT.CAPTURED.SELF',
							},
							action: 'icd_hosting_process_payment'
						};
						var form = document.getElementById('hosting-payment-form');
						new Request().post(form.attributes['action'].value, webhook_data).success(function(result) {
							document.location.reload();
						});
					});
				},
				onCancel(data) {
					document.location.reload();
				},
				onError(err) {
					Widget.hpi();
					Widget.msg('error', Widget.tr('error.paypal_order_create_failed'));
					//document.location.reload();
				}
			}).render(element_id);
			container.show();
		},
		startBraintree: function(container) {
			var processor_id = container.data('processor-id');
			$('#braintree-dropin-' + processor_id).empty();

			$.ajax({
				'type': 'GET',
				'url': 'https://js.braintreegateway.com/web/dropin/1.26.0/js/dropin.min.js',
				'dataType': 'script',
				'cache': true,
				'success': function() {
					container.show();

					braintree.dropin.create({
						authorization: container.data('client-token'),
						container: '#braintree-dropin-' + processor_id,
						card: { cardholderName: { required: true } },
						threeDSecure: true
					}, function (createErr, instance) {
						if (createErr) {
							Widget.msg('error', createErr);
							return;
						}

						$('.braintree-placeholder', container).empty();

						$('#payment-btn').on('click.braintree', function(event) {
							event.preventDefault();
							var form = $('#hosting-payment-form');
							instance.requestPaymentMethod(
								{
									threeDSecure: {
										amount: form.attr('data-total'),
										email: form.attr('data-email'),
										billingAddress: {
											locality: form.attr('data-city'),
											postalCode: form.attr('data-zip'),
											countryCodeAlpha2: form.attr('data-country')
										}
									}
								},
								function(err, payload) {
									if (err) {
										if (err.name != 'DropinError')
											Widget.msg('error', err.message);

										return;
									}

									// Add the nonce to the form and submit
									$('#braintree-nonce-' + processor_id).val(payload.nonce);
									PaymentForm.makePayment();
								}
							);

							return false;
						});
					});
				}
			});
		},
		startStripe: function(container) {
			var processor_id = container.data('processor-id');
			$('.stripe-card, .stripe-payment-request, .stripe-error', container).empty();

			$.ajax({
				'type': 'GET',
				'url': 'https://js.stripe.com/v3/',
				'dataType': 'script',
				'cache': true,
				'success': function() {
					var stripe = Stripe(container.data('key'));

					var elements = stripe.elements();

					var card = elements.create('card', {
						hidePostalCode: true,
						style: { invalid: { color: '#a94442', iconColor: '#a94442' } }
					});

					card.mount($('.stripe-card', container)[0]);

					container.show();

					card.addEventListener('change', function(event) {
						$('.stripe-error', container).text(event.error ? event.error.message : '');
					});

					$('#payment-btn').on('click.stripe', function(event) {
						event.preventDefault();

						stripe.createToken(card).then(function(result) {
							if (!result.error) {
								var form = $('#hosting-payment-form');
								var form_data = form.serialize();
								Widget.spi(form.find(':input'));

								new Request()
									.success(function(result) {
										if (!result.payment_request) {
											Widget.hpi(form.find(':input'));
											return;
										}

										stripe
											.confirmCardPayment(result.payment_request.token_id, {
												payment_method: {
													card: card
												}
											})
											.then(function(confirmed) {
												Widget.hpi(form.find(':input'));

												if (confirmed.paymentIntent && confirmed.paymentIntent.status == 'succeeded') {
													new Request().post(form.attr('action'), {
														order_id: result.payment_request.order_id,
														processor_id: processor_id,
														data: confirmed,
														action: 'icd_hosting_process_payment'
													});
												} else {
													$('.stripe-error', container).text(Widget.tr('payment_error.payment_declined'));
												}
											});
									})
									.post(Widget.url(form.attr('action')), form_data);
							}
						});

						return false;
					});
				}
			});
		},
		makePayment: function () {
			var request, p_form = $('#hosting-payment-form');

			request = new Request({progress: true, disabled: p_form.find(':input')})
				.success(function (result) {
					if (result.payment_form)
						p_form.after(result.payment_form);
				})
				.always(function (result) {
					if (result.payment_form || result.redirect_to)
						Widget.spi(p_form.find(':input'));
				})
				.post(Widget.url(p_form.attr('action')), p_form.serialize());

			return false;
		},
		changeSelection: function(e) {
			var el = $(e.target), el_data = el.data();

			$('.hosting-widget .payment-info, .hosting-widget .payment-details').hide();

			$('#payment-btn').off('click').show();

			if (PaymentForm.isOffline(el_data.type)) {
				$('#procinfo-' + el.val()).show();
				$('#payment-btn').hide();
			}
			else if (el_data.type == 'Stripe') {
				PaymentForm.startStripe($('#payment-details-stripe-' + el.val()));
			}
			else if(el_data.type == 'Braintree') {
				PaymentForm.startBraintree($('#payment-details-braintree-' + el.val()));
			}
			else if (el_data.type == 'PayPal') {
				if (el_data.onsite == 1)
					$('#payment-btn').hide();

				PaymentForm.startPaypal($('#payment-details-paypal-' + el.val()));
			}

			if (el_data.onsite) {
				$('#payment-details-' + el_data.type.toLowerCase() + '-' + el.val()).show();
			}
		}
	}

	RequestForm = {
		params: {
			requests: [],
			search_results: {},
			extra_attributes_html: {},
			icann_domains: 0
		},
		init: function (params) {
			$.extend(this.params, params);

			$('body')
				.off('submit.form')
				.on('submit.form', '#hosting-order-form', this.submitOrder)
				.off('click.create-order')
				.on('click.create-order', '#create-order', this.submitOrder)
				.off('change.select-item')
				.on('change.select-item', 'input.select-item', this.selectItem)
				.off('change.select-price')
				.on('change.select-price', 'select.price', this.changePeriod)
				.off('change.item-quantity')
				.on('change.item-quantity', 'select.quantity', this.itemQuantity)
				.off('click.toggle-item')
				.on('click.toggle-item', 'button.toggle-item', this.toggleItem)
				.off('click.del-item')
				.on('click.del-item', 'button.del-item', this.delItem)
				.off('click.item-done')
				.on('click.item-done', 'button.btn-done', this.itemDone)
				.off('click.add-extra')
				.on('click.add-extra', 'button.add-extra-item', this.addExtraItem)
				.off('change.ssl-installation')
				.on('change.ssl-installation', 'select.ssl-installation', this.sslInstallation)
				.off('click.add-ip')
				.on('click.add-ip', 'button.add-new-ip', this.addIP)
				.off('click.domain-search')
				.on('click.domain-search', 'button.domain-search-btn', this.domainSearch)
				.off('click.select-domain')
				.on('click.select-domain', 'button.select-domain', this.selectDomain)
				.off('keydown.domain-search')
				.on('keydown.domain-search', 'input.domain-search-input', this.domainSearchEnter)
				.off('show.bs.dropdown')
				.on('show.bs.dropdown', 'span.ssl-approver-email', this.sslApproverEmails)
				.off('click.approver-email')
				.on('click.approver-email', 'span.ssl-approver-email a', this.sslApproverEmailSelect)
				.off('change.ssl-common-name')
				.on('change.ssl-common-name', 'input.ssl-common-name', function () {
					$('input.approver-email', $(this).closest('div.item-config')).val('');
				})
				.off('focus.input-approver-email')
				.on('focus.input-approver-email', 'input.approver-email', function (e) {
					if (!$('span.ssl-approver-email', $(this).parent()).hasClass('open'))
						$('button.widget-dropdown-toggle', $(this).parent()).trigger('click');
				})
				.off('click.input-approver-email')
				.on('click.input-approver-email', 'input.approver-email', function (e) {
					e.stopPropagation();
				})
				.off('change.extra-attributes')
				.on('change.extra-attributes', 'select.extra-attributes-select', function () {
					$('#' + $(this).attr('id') + '_error').hide();
					HostingOrderForm.refreshExtraAttributes($(this).closest('div.extra-attributes-domain'));
				})
				.off('click.custom-contact')
				.on('click.custom-contact', 'input.custom-contact', function (e) {
					var id = $(this).closest('div.item-icontact').data('id');
					$('#custom-item-icontact-' + id).toggle($(this).val() == 1);
				})
				.off('click.check-tlds').on('click.check-tlds', 'a.check-all-tlds', this.checkTLDs)
				.off('click.ns-configuration-toggle')
				.on('click.ns-configuration-toggle', '.ns-configuration-toggle a', function(e) {
					e.preventDefault();
					var container = $(this).closest('.ns-configuration');

					$('.ns-configuration-toggle a', container).toggleClass('display-none');

					if ($(this).hasClass('ns-show-more')) {
						container.find('.form-group.display-none').show();
						container.find(':input').prop('disabled', false);
					} else {
						container.find(':input').slice(2).filter(function() { return this.value == ''; }).prop('disabled', true).closest('.form-group').hide();
					}
				})
				.off('click.add-new-addon-domain')
				.on('click.add-new-addon-domain', '.add-new-addon-domain', function() {
					var extra_params = RequestForm.newAddonItem($(this).closest('.item-config'));
					if (typeof extra_params == 'string')
						loadRequest($(':input', '#requested-order').serialize() + extra_params);
				})
				.off('change.domain-parked-to')
				.on('change.domain-parked-to', '.domain-parked-to', function() {
					$('.domain-parked-to-details', $(this).closest('.parking-options-inner')).toggleClass('collapse', $(this).val() != '');
					RequestForm.changeAddon($(this));
				})
				.off('change.addon_domain-quantity')
				.on('change.addon_domain-quantity', '.addon_domain-quantity', function() {
					RequestForm.changeAddon($(this));
				})
				.off('change.country').on('change.country', '#contact_country', function(e) {
					var iso2 = $(this).val();
					if ($('#contact_phone').val() == '' || $('#contact_phone_country').val() == '')
						$('#contact_phone_country').val(iso2);
					if ($('#contact_fax').val() == '' || $('#contact_fax_country').val() == '')
						$('#contact_fax_country').val(iso2);
					})
				.off('change.phone').on('change.phone', '#contact_phone,#contact_fax', function(e) {
					$(this).val($(this).val().replace(/[^\d]/g, ''));
				})
			;

			$('button.click-search').trigger('click');
			$('div.extra-attributes-domain').each(function () {
				$(this).closest('div.extra-attributes').show();
				HostingOrderForm.refreshExtraAttributes($(this), true);
			});
		},
		checkTLDs: function() {
			var checked = $(this).data('checked') == '1' ? 0 : 1;
			$(this).data('checked', checked);
			$('input', $(this).closest('.domain-search-controls')).prop('checked', checked);
			return false;
		},
		selectItem: function () {
			var input = $(this);
			RequestForm.unselectChildren(input);
			RequestForm.selectParents(input);
			RequestForm.loadRequest($('#hosting-order-form').serialize(), null, function () {
				input.prop('checked', !input.prop('checked'));
			});
		},
		changePeriod: function () {
			var input = $(this);
			RequestForm.childrenPeriod(input);
			RequestForm.loadRequest($('#hosting-order-form').serialize());
		},
		unselectChildren: function (input) {
			if (input.prop('checked') == 1)
				return;

			var id = input.closest('tr').data('id');
			$('tr.parent-' + id).each(function () {
				var input = $('input.select-item', this);
				if (input) {
					input.prop('checked', false);
					RequestForm.unselectChildren(input);
				}
			});
		},
		selectParents: function (input) {
			if (input.prop('checked') == 0)
				return;

			var parent = input.closest('tr').data('parent');
			if (parent) {
				var parent_input = $('#select-item-' + parent);
				parent_input.prop('checked', true);
				RequestForm.selectParents(parent_input);
			}
		},
		childrenPeriod: function(input) {
			var id = input.closest('tr').data('id');
			$('tr.parent-' + id + '.bonus-item').each(function() {
				var child = $('select.price', this);
				if (child[0]) {
					var p = input.val(), ip = input.data('periodicity'), cp = child.data('periodicity');
					if (ip == 'MO' && cp == 'YR') {
						p = Math.ceil(p / 12);
					} else if (ip == 'YR' && cp == 'MO') {
						p = Math.ceil(p * 12);
					}

					if (p < 1) {
						p = 1;
					}
				}

				if (child.find('option[value="' + p + '"]')[0]) {
					child.val(p);
					//childrenPeriod(child);
				}
			});
		},
		submitOrder: function() {
			var request, form = $('#hosting-order-form');
			$('button.toggle-item').removeClass('btn-warning btn-danger');

			//addon paid option
			var addon_not_paid = $('.domain-parked-to-details:not(.collapse) .addon-slot-paid:not(.collapse)')
				.filter(function() {
					return $('.select-item', $(this).closest('tr').prev()).prop('checked');
				});

			if (addon_not_paid.length) {
				var addon_not_paid_upgrade = addon_not_paid.filter(function() {
					return $('.add-new-addon-domain', this).length ? true : false;
				});

				if (addon_not_paid_upgrade.length && !confirm(Widget.tr('not_enough_addon_slots_available'))) {
					return false;
				}

				addon_not_paid.each(function() {
					var parked_to = $('.domain-parked-to', $(this).closest('.parking-options-inner'));
					parked_to.val($('option.hosted-main', parked_to).attr('value')).trigger('change');
				});
			}

			request = new Request({progress: true, disabled: form.find(':input')})
				.success(function(result) {
					if (result.payment_form) {
						form.after(result.payment_form);
					}
					else {
						RequestForm.outlineErrors();
					}
				})
				.always(function(result) {
					if (result.payment_form || result.redirect_to)
						Widget.spi(form.find(':input'));
				})
				.post(Widget.url(form.attr('action')), form.serialize());

			return false;
		},
		loadRequest: function (params, success_callback, error_callback) {
			var request, form = $('#hosting-order-form');
			params = params.replace(/action=/g, "action=" + form.data('url-prices') + "&old_action=");
			$('button.toggle-item').removeClass('btn-warning btn-danger');

			request = new Request({progress: true, disabled: form.find(':input')})
				.success(function (result) {
					if (result.html) {
						form.parent().parent().html(result.html); //.find('button.click-search').trigger('click');
						$('button.click-search').trigger('click');
						$('div.extra-attributes-domain').each(function () {
							$(this).closest('div.extra-attributes').show();
							HostingOrderForm.refreshExtraAttributes($(this), true);
						});

						if (typeof success_callback === 'function') {
							success_callback();
						}

					} else {
						RequestForm.outlineErrors();
						if (typeof error_callback === 'function') {
							error_callback();
						}
					}

				})
				.post(Widget.url(form.attr('action')), params);
		},
		outlineErrors: function () {
			var configs = ['config', 'icontact'];
			for (var i = 0; i < configs.length; i++) {
				$('div.item-' + configs[i]).each(function () {
					var container = $(this);
					if (container.find(':input.error').length) {
						var item_id = container.data('id');
						$('#item-' + configs[i] + '-' + item_id).val(1);
						$('#toggle-' + configs[i] + '-' + item_id).addClass('btn-danger');
						container.show();
						container.closest('tr').show();
					}
				});
			}
		},
		itemQuantity: function (e) {
			var total = 0, catalog_total = 0;
			var price = $('option:selected', this).data('price');
			var catalog_price = $('option:selected', this).data('catalog-price');

			$('span.item-price', $(this).closest('tr')).data('price', price).text(price)
				.next().data('price', catalog_price).text(catalog_price);

			$('span.item-price').each(function () {
				var price = parseFloat($(this).data('price'));
				var catalog_price = parseFloat($(this).next().data('price')) || price;
				total = total + price;
				catalog_total = catalog_total + catalog_price;
			});

			if ($(this).closest('tr').find('.select-item').prop('checked')) {
				$('#order-total').text(total.toFixed(2));
				$('#order-catalog-total').text(catalog_total.toFixed(2));
			}
		},
		toggleItem: function (e) {
			var toggle = $(this).data('toggle');
			var block = $('#' + toggle);
			var hidden = $('#item-' + toggle);
			var container = block.parent();

			block.toggleClass('display-none');
			if (hidden.length) {
				hidden.val(block.hasClass('display-none') ? 0 : 1);
			}

			if (container.children().length > container.children('.display-none').length) {
				block.closest('tr').show();
			} else {
				block.closest('tr').hide();
			}

			return false;
		},
		delItem: function (e) {
			//$('tr.row-item-' + $(this).data('id') + ', tr.parent-' + $(this).data('id')).remove();
			//RequestForm.loadRequest($('#hosting-order-form').serialize());
			RequestForm.loadRequest($(':input', '#hosting-order-form')
				.not('tr.row-item-' + $(this).data('id') + ' :input, tr.parent-' + $(this).data('id') + ' :input').serialize());
			return false;
		},
		itemDone: function (e) {
			var toggle = $(this).data('toggle');
			var hidden = $('#item-' + toggle);
			hidden.val(0);

			var extra_params = '&validate_item_id=' + $(this).closest('tr').data('id');

			var addon_item_params = RequestForm.newAddonItem($(this).closest('.item-config'));
			if (addon_item_params === false)
				return;
			else if (typeof addon_item_params == 'string')
				extra_params = extra_params + addon_item_params;

			RequestForm.loadRequest($('#hosting-order-form').serialize() + extra_params);
			return false;
		},
		addExtraItem: function (e) {
			var container = $(this).closest('div.extra-item-form');
			var params = $(':input.extra-item', container).each(function () {
				$(this).attr('name', 'request[' + $(this).data('name') + ']');
			})
				.add(':input', '#hosting-order-form').serialize();

			$(':input.extra-item', container).removeAttr('name');
			RequestForm.loadRequest(params);
			return false;
		},
		sslInstallation: function (e) {
			var id = $(this).closest('tr').data('id');
			var val = $(this).val();
			var click_new = val == 'new' && $('#new-options-' + id + ' option').length == 0;

			if (click_new && !RequestForm.newIPItem(id)) {
				$(this).val($(this).data('value'));
				return false;
			}

			$(this).data('value', val);
			if (val == 'new' && !click_new) {
				$('#ssl-ip-' + id).html($('#' + val + '-options-' + id + ' option').clone());
				$('#dedicated-' + id).show();
				//$('#add-new-ip-' + id).show();
			} else if (val == 'dedicated') {
				$('#ssl-ip-' + id).html($('#' + val + '-options-' + id + ' option').clone());
				$('#dedicated-' + id).show();
				//$('#add-new-ip-' + id).hide();
			} else {
				$('#dedicated-' + id).hide();
			}
		},
		addIP: function (e) {
			newIPItem($(this).data('id'));
		},
		domainSearch: function (e) {
			var container = $(this).closest('div.domain-search-container');
			var idx = container.data('id');
			var search = $('input.domain-search-input', container);
			var parts = DomainSearchForm.domainParts(search.val());
			if (!parts) {
				Widget.msg('error', Widget.tr('domain_search.invalid'), 'domain_sld_' + idx);
				return false;
			}

			if (parts[2]) {
				$('input[value="' + parts[2] + '"]', container).prop('checked', 1);
			}

			if ($(':checkbox:checked', container).length < 1) {
				Widget.msg('error', Widget.tr('domain_search.choose_search_tlds'));
				return false;
			}

			search.removeClass('error');
			$('div.inline-error', container).remove();

			// abort not finished old requests
			if (RequestForm.params.requests[idx]) {
				for (i in RequestForm.params.requests[idx]) {
					RequestForm.params.requests[idx][i].abort();
				}
			}

			RequestForm.params.requests[idx] = [];

			$('#epp-code-' + idx).hide();
			$('#extra-attributes-' + idx).hide();
			$('#ns-configuration-' + idx).hide();
			$('#domain-done-' + idx).hide();

			var domain = '', dom_price;
			var result_container = $('#domain-search-result-' + idx).show().find('div.domain-search-result').html('<table class="table"></table>');
			var domain_tlds = RequestForm.params.tlds[container.data('parent-id')];
			$('input.domain-sld', container).val(parts[1]);

			for (var catalog_id in domain_tlds) {
				if (!$('input[data-catalog_id="' + catalog_id + '"]:checked', container)[0]) {
					continue;
				}

				domain = parts[1] + '.' + domain_tlds[catalog_id].tld;
				dom_price = validPath(domain_tlds, catalog_id + '.price.price') ? parseFloat(domain_tlds[catalog_id].price.price).toFixed(2) : 'n/a';

				$('table', result_container).append('<tr id="dom-' + idx + '-' + catalog_id + '">' +
					'<td style="width:2%;"><span class="domain-selected" style="visibility:hidden">✓</span></td>' +
					'<td style="width:60%" class="domain-name">' + domain + '</td>' +
					'<td style="width:19%" class="domain-price text-right"><strong>' + RequestForm.params.currency + ' ' +
					dom_price + '</strong></td>' +
					'<td style="width:19%" class="domain-action text-center"><span class="loader-small"></span></td>' +
					'</tr>');

				var callback = (function (sld, tld, id, index) {
					return function (result) {
						var dom = sld + '.' + tld;
						RequestForm.params.search_results[dom] = result;

						if (result.status && result.data.available == 'register') {
							var html = '<button type="button" class="btn btn-success btn-xs select-domain" ' +
								'data-action="register" data-catalog-id="' + id + '" data-domain="' + dom + '" data-icann="' + result.data.icann + '">' +
								Widget.tr('domain_search.register') + '</button>';
						} else if (result.status && result.data.available == 'transfer') {
							var html = '<button type="button" class="btn btn-warning btn-xs select-domain" ' +
								'data-action="transfer" data-catalog-id="' + id + '" data-domain="' + dom + '" data-icann="' + result.data.icann + '">' +
								Widget.tr('domain_search.transfer') + '</button>';
						} else if (result.status && result.data.available == 'unavailable') {
							var html = '<i class="glyphicon glyphicon-exclamation-sign tooltipui alert-warning" data-content="' + Widget.tr('domain_search.unavailable') + '"></i>';
						} else {
							var html = '<i class="glyphicon glyphicon-exclamation-sign tooltipui alert-danger" data-content="' + Widget.tr('domain_search.error') + '"></i>';
						}

						if (result.status && result.data.extra_attributes_html)
							RequestForm.params.extra_attributes_html[tld] = result.data.extra_attributes_html;

						var dom_action = $('td.domain-action', '#dom-' + index + '-' + id);
						dom_action.html(html);
						if (dom == parts[0])
							dom_action.find('button').trigger('click');
					}
				})(parts[1], domain_tlds[catalog_id].tld, catalog_id, idx);

				if (RequestForm.params.search_results[domain]) {
					callback(RequestForm.params.search_results[domain]);
				} else {
					var req = new Request;
					RequestForm.params.requests[idx].push(req.success(callback).get(
						$('#hosting-order-form').attr('action'),
						{
							'action': $('#hosting-order-form').data('url-domain-search'),
							'domain': domain,
							'render_extra_attributes': 1,
							'setup': 'request',
							'idx': idx,
							'_domain_check_nonce':  $('#_domain_check_nonce').val(),
						}
					));
				}
			}

			result_container.append('</table>');
			return false;
		},
		selectDomain: function (e) {
			var container = $(this).closest('div.domain-search-container');
			var domain_tlds = RequestForm.params.tlds[container.data('parent-id')];
			var catalog_id = $(this).data('catalog-id');
			var tld = domain_tlds[catalog_id].tld;
			var selected_domain = $(this).data('domain');
			var search_domain = $('input.domain-search-input', container);

			$('span.domain-selected', container).css('visibility', 'hidden');
			$('span.domain-selected', $(this).closest('tr')).css('visibility', '');

			$('div.domain-done', container).show();
			$('div.epp-code', container).hide();

			if ($(this).data('action') == 'transfer') {
				if (domain_tlds[catalog_id].epp) {
					$('div.epp-code', container).show();
				}

				$('input.domain-action', container).val('transfer');
				$('.ns-configuration', container).hide().find(':input').val('');
			} else {
				$('input.domain-action', container).val('register');
				$('.ns-configuration', container).toggle(search_domain.data('group') == 'standalone:domain');
			}

			if (RequestForm.params.extra_attributes_html[tld]) {
				var extra_form = $('div.extra-attributes-inner', container);
				if (extra_form.data('tld') != tld) {
					extra_form.data('tld', tld).html(RequestForm.params.extra_attributes_html[tld]);
					HostingOrderForm.refreshExtraAttributes(extra_form);
				}

				$('div.extra-attributes', container).show();
			} else {
				$('div.extra-attributes', container).hide();
				//$('div.extra-attributes-inner', container).empty();
			}

			var selected_domain = $(this).data('domain');
			var search_domain = $('input.domain-search-input', container);
			var hosting_hostname = $('input.hosting-hostname', 'tr.row-item-' + container.closest('tr').data('parent'));

			if (search_domain.data('group') == 'bonus:domain' && (!hosting_hostname.val() || hosting_hostname.val() == search_domain.data('domain'))) {
				hosting_hostname.val(selected_domain);
			}

			search_domain.val(selected_domain).data('domain', selected_domain);
			$('input.domain-tld', container).val(tld);

			// icann verification option
			$('input.domain-catalog-id', container).val(catalog_id).removeClass('icann-show');
			if ($(this).data('icann') && $('.select-item', '.row-item-' + container.data('id')).is(':checked')) {
				$('input.domain-catalog-id', container).addClass('icann-show');
			}
			$('#order-domain-icann').toggle($('.icann-show').length > 0);

			return false;
		},
		domainSearchEnter: function (e) {
			if (e.keyCode == 13) {
				var container = $(this).closest('div.domain-search-container');
				$('button.domain-search-btn', container).trigger('click');
			}
		},
		sslApproverEmails: function (e) {
			var ref = $(this);
			var form = $('#hosting-order-form');
			var common_name = $.trim($('#common-name-' + ref.data('id')).val()).toLowerCase().replace(/^http(?:s)?:\/\//i, '').replace(/^\*\./i, '');
			if (common_name == '' || common_name == ref.data('valid-emails-for'))
				return;

			ref.find('li').not('.dropdown-header, .loading-email-spinner').remove();

			if (!common_name.match(/^([a-z0-9-]+\.)+[a-z]{2,15}$/)) {
				$('li.dropdown-header', ref).text(Widget.tr('ssl.valid_common_name_first'));
				return;
			}

			$('li.dropdown-header', ref).text(Widget.tr('ssl.valid_approver_emails'));
			ref.find('li.loading-email-spinner').show();

			var request = new Request({progress: true, disabled: '#hosting-order-form :input'})
				.success(function (result) {
					ref.find('li.loading-email-spinner').hide();

					if (validPath(result, 'data.emails')) {
						ref.data('valid-emails-for', common_name);
						for (var i = 0; i < result.data.emails.length; i++) {
							ref.find('ul').append('<li><a href="#" data-email="' + result.data.emails[i] + '">' + result.data.emails[i] + '</a></li>');
						}
					} else {
						ref.find('ul').append('<li class="text-center p-15 text-warning">' + Widget.tr('ssl.approver_emails_error') + '</li>');
					}
				})
				.get(Widget.url(form.attr('action')), {
					action: form.data('url-approver-emails'),
					hostname: common_name,
					type: ref.data('product'),
					_approver_email_nonce:  $('#_approver_email_nonce').val(),
				});
		},
		sslApproverEmailSelect: function (e) {
			$(this).closest('span.ssl-approver-email').prev().val($(this).data('email'));
			$(this).closest('span.ssl-approver-email').find('button.widget-dropdown-toggle').trigger('click');
			e.preventDefault();
		},
		newIPItem: function (id) {
			if (!confirm(Widget.tr('ssl.confirm_new_ip_item')))
				return false;

			var btn = $('#add-new-ip-' + id + ' button');
			var ip_type = $('select.ssl-installation', btn.closest('div.item-config'));
			var ip_type_value = ip_type.data('value');
			RequestForm.loadRequest(btn.data('request') + '&' + $(':input', '#hosting-order-form').serialize(),
				function () {
					var new_options = $('#new-options-' + id + ' option').clone();
					$('#ssl-ip-' + id).html(new_options).val(new_options.last().attr('value'));
					$('#dedicated-' + id).show();
				},
				function () {
					ip_type.val(ip_type_value);
				}
			);
			return true;
		},

		newAddonItem: function(container) {
			var addon_btn = $('.add-new-addon-domain', container);
			if (addon_btn[0] && addon_btn.is(':visible')) {
				if (!confirm(Widget.tr('this_will_add_1_additional_addon_slot')))
					return false;

				if (container.data('account_id')) {
					var addon_quantity = $('.addon_domain-quantity', 'tr[data-account_id=' + container.data('account_id') + ']');
				} else {
					var addon_quantity = $('.addon_domain-quantity', 'tr[data-parent=' + container.data('parent') + ']');
				}

				var addon_quantity_val = parseInt(addon_quantity.val()) + 1;
				var select_item = $('.select-item', addon_quantity.closest('tr'));

				if (addon_quantity[0] && !select_item.prop('checked')) {
					select_item.prop('checked', 1);
					return '';
				} else if (addon_quantity[0] && $('option[value="' + addon_quantity_val + '"]')[0]) {
					addon_quantity.val(addon_quantity_val).trigger('change');
				} else {
					return '&' + addon_btn.data('request');
				}
			}
		},

		changeAddon: function(el) {
			var container = el.closest('tr');
			if (container.data('account_id')) {
				var parked_details = $('tr[data-account_id="' + container.data('account_id') + '"] .domain-parked-to-details');
				var addon_quantity = $('.addon_domain-quantity', 'tr[data-account_id=' + container.data('account_id') + ']');
				var addon_btn = $('.add-new-addon-domain', 'tr[data-account_id=' + container.data('account_id') + ']');
			} else {
				var parked_details = $('tr[data-parent="' + container.data('parent') + '"] .domain-parked-to-details');
				var addon_quantity = $('.addon_domain-quantity', 'tr[data-parent=' + container.data('parent') + ']');
				var addon_btn = $('.add-new-addon-domain', 'tr[data-parent=' + container.data('parent') + ']');
			}

			var used_slots_order = 0;
			var quantity = addon_quantity[0] && $('.select-item', addon_quantity.closest('tr')).prop('checked') ?
				parseInt(addon_quantity.val()) : 0;

			parked_details.each(function() {
				var slots = parseInt($(this).data('addon_slots'));
				var extra_slots_total = parseInt($(this).data('addon_extra_slots')) + quantity;
				var used_slots_total = parseInt($(this).data('addon_used_slots')) + used_slots_order;
				var parked_to = $('.domain-parked-to', $(this).closest('.parking-options-inner'));
				if ($('.select-item', $(this).closest('tr').prev()).prop('checked') && parked_to.val() == '')
					used_slots_order = used_slots_order + 1;

				var is_free = slots + extra_slots_total - used_slots_total > 0;
				$('.addon-used-slots', this).text(used_slots_total)
					.toggleClass('text-success', is_free).toggleClass('text-danger', !is_free);
				$('.addon-extra-slots', this).text(extra_slots_total > 0 ? ' +' + extra_slots_total : '');
				$('.addon-slot-free', this).toggleClass('collapse', !is_free);
				$('.addon-slot-paid', this).toggleClass('collapse', is_free);

				if (!is_free && !addon_btn[0]) {
					$('option[value=""]', parked_to).prop('disabled', 1);
					if (!parked_to.val())
						parked_to.val($('option:not([disabled]):first', parked_to).attr('value')).trigger('change');
				}
				else {
					$('option[value=""]', parked_to).prop('disabled', 0);
				}
			});
		}
	}

	CertificatesForm = {
		formdata: {},
		order_currency: 'USD',

		init: function(formdata) {
			$.extend(this.formdata, formdata);

			$('body')
				.on('change.hosting-plan', '.hosting-plan', function() {
					$('.hosting-options-existing').toggle($(this).val() == 'existing');
					CertificatesForm.rebuildSSLPrices();
				})
				.on('change.hosting-plan-account', '#hosting-plan-account', function() {
					CertificatesForm.rebuildSSLPrices();
					CertificatesForm.rebuildSSLIPOptions();
				})
				.on('change.order-period', '#order-period', function() {
					CertificatesForm.sslPrices();
				})
				.on('change.order-certificate', '.order-certificate', function() {
					var content, selected;
					var currency = CertificatesForm.formdata.currency;
					var product = $('.order-certificate:checked').data('product');
					var product_prices = CertificatesForm.hostingOptionPrices(product);
					var is_sectigo = product.match(/sectigo/);

					if (is_sectigo) {
						$('.madatory-sectigo').show();
					} else {
						$('.madatory-sectigo').hide();
					}

					$('.madatory-sectigo').closest("div").find("input").attr('required', is_sectigo);

					content = '';
					if (product_prices) {
						selected = $('#order-period').val();
						for (i in product_prices.price) {
							content = content + '<option value="' + i + '"' + (i == selected ? ' selected' : '') + '>' +
								i + ' ' + product_prices.price[i].periodicity + ' - ' + currency + ' ' + product_prices.price[i].price.toFixed(2) +
								'</option>';
						}
					}

					$('#order-period').html(content).trigger('change');
					//reloadSelect2('#order-period');
				})
				.on('change.ssl-installation', '#ssl-ip-type', function() {
					var installation = $(this).val();
					if (installation == 'noip') {
						$('.dedicated-ips, .ssl-ip-period').hide();
					} else if (installation == 'sni') {
						$('.dedicated-ips, .ssl-ip-period').hide();
					} else if (installation == 'dedicated') {
						$('.dedicated-ips').show();
						$('.ssl-ip-period').hide();
						$('#ssl-ip').html($('#dedicated-options').html());
						//reloadSelect2('#ssl-ip');
					} else if (installation == 'new') {
						var account_id = $('#hosting-plan-account').val();
						if (CertificatesForm.formdata.cart_ips[account_id]) {
							$('.dedicated-ips').show();
							$('.ssl-ip-period').hide();
							$('#ssl-ip').html($('#new-options').html());
							//	reloadSelect2('#ssl-ip');
						} else {
							$('.dedicated-ips').hide();
							$('.ssl-ip-period').show();
							$('#ssl-ip').html('<option value=""></option>');
							//	reloadSelect2('#ssl-ip');
						}
					}
					CertificatesForm.sslPrices();
				})
				.on('submit.certificates-form', '#certificates-form', function() {
					var form = $(this);
					if (!$('.hosting-plan:checked')[0] || $('.hosting-plan:checked').val() == 'new') {
						location.href = form.data('url') + $('.ssl-common-name').val();
					} else {
						Widget.spi();
						CertificatesForm.placeOrder();
					}
					return false;
				})
				.off('show.bs.dropdown')
				.on('show.bs.dropdown', 'span.ssl-approver-email', CertificatesForm.sslApproverEmails)
				.off('click.approver-email')
				.on('click.approver-email', 'span.ssl-approver-email a', CertificatesForm.sslApproverEmailSelect)
				.off('change.ssl-common-name')
				.on('change.ssl-common-name', 'input.ssl-common-name', function() {
					$('input.approver-email').val('');
				})
				.off('focus.input-approver-email')
				.on('focus.input-approver-email', 'input.approver-email', function(e) {
					if (!$('span.ssl-approver-email', $(this).parent()).hasClass('open'))
						$('button.dropdown-toggle', $(this).parent()).trigger('click');
				})
				.off('click.input-approver-email')
				.on('click.input-approver-email', 'input.approver-email', function(e) {
					e.stopPropagation();
				})
				.off('change.country')
				.on('change.country', '#contact_country', function(e) {
					var iso2 = $(this).val();
					if ($('#contact_phone').val() == '' || $('#contact_phone_country').val() == '')
						$('#contact_phone_country').val(iso2);
					if ($('#contact_fax').val() == '' || $('#contact_fax_country').val() == '')
						$('#contact_fax_country').val(iso2);
				})
				.off('change.phone').
			on('change.phone', '#contact_phone,#contact_fax', function(e) {
				$(this).val($(this).val().replace(/[^\d]/g, ''));
			})
			;

			CertificatesForm.rebuildSSLPrices();
			CertificatesForm.rebuildSSLIPOptions();
		},
		sslApproverEmails: function(e) {
			var ref = $(this);
			var form = $('#certificates-form');
			var common_name = $.trim($('#common-name-' + ref.data('id')).val()).toLowerCase().replace(/^http(?:s)?:\/\//i, '').replace(/^\*\./i, '');

			if (common_name != '' && common_name == ref.data('valid-emails-for'))
				return;

			ref.find('li').not('.dropdown-header, .loading-email-spinner').remove();
			if (!common_name.match(/^([a-z0-9-]+\.)+[a-z]{2,15}$/)) {
				$('li.dropdown-header', ref).text(Widget.tr('ssl.valid_common_name_first'));
				return;
			}

			$('li.dropdown-header', ref).text(Widget.tr('ssl.valid_approver_emails'));
			ref.find('li.loading-email-spinner').show();

			var request = new Request({ progress: true, disabled: '#certificates-form :input' })
				.success(function(result) {
					ref.find('li.loading-email-spinner').hide();

					if (validPath(result, 'data.emails')) {
						ref.data('valid-emails-for', common_name);
						for (var i = 0; i < result.data.emails.length; i++) {
							ref.find('ul').append('<li><a href="#" data-email="' + result.data.emails[i] + '">' + result.data.emails[i] + '</a></li>');
						}
					} else {
						ref.find('ul').append('<li class="text-center p-15 text-warning">' + Widget.tr('ssl.approver_emails_error') + '</li>');
					}
				})
				.get(Widget.url(form.attr('action')), {
					action: form.data('url-approver-emails'),
					hostname: common_name,
					type: ref.data('product'),
					_approver_email_nonce:  $('#_approver_email_nonce').val(),
				});
		},
		sslApproverEmailSelect: function(e) {
			$(this).closest('span.ssl-approver-email').prev().val($(this).data('email'));
			$(this).closest('span.ssl-approver-email').find('button.widget-dropdown-toggle').trigger('click');
			e.preventDefault();
		},
		sslPrices: function() {
			var currency = CertificatesForm.formdata.currency;
			var product = $('.order-certificate:checked').data('product');
			var period = $('#order-period').val();
			var account_id = $('#hosting-plan-account').val();
			var product_prices = CertificatesForm.hostingOptionPrices(product);

			if (product_prices) {
				var ssl_price = product_prices.price[period].price;
				var hosting_plan = $('.hosting-plan:checked').val();
				var ip_type = $('#ssl-ip-type').val();
				var ip_price = 0;
				if (hosting_plan == 'existing' && ip_type == 'new' && !CertificatesForm.formdata.cart_ips[account_id]) {
					ip_price = $('#ip-price').data('price');
				}

				var total_price = ssl_price + ip_price;
				$('#ssl-price').text(currency + ' ' + ssl_price.toFixed(2));
				$('#price-order').text(currency + ' ' + total_price.toFixed(2));
			} else {
				$('#ssl-price').text('-');
				$('#price-order').text('-');
			}

			CertificatesForm.completeTerms(product);
		},
		hostingOptionPrices: function(product) {
			var group_id = CertificatesForm.formdata.default_hosting_option;
			var hosting_plan = $('.hosting-plan:checked').val();
			var hosting_plan_account = $('#hosting-plan-account').val();

			if (hosting_plan != 'existing')
				group_id = CertificatesForm.formdata.prices_map[hosting_plan];
			else if (hosting_plan_account != '')
				group_id = CertificatesForm.formdata.prices_map[hosting_plan_account];

			if (product)
				return array_get(CertificatesForm.formdata, 'prices.' + group_id + '.prices.' + product);

			return array_get(CertificatesForm.formdata, 'prices.' + group_id);
		},
		rebuildSSLPrices: function() {
			var currency = CertificatesForm.formdata.currency;
			var option_prices = CertificatesForm.hostingOptionPrices();

			if (option_prices) {
				var content, i;
				var items = option_prices.prices;
				var selected = $('.order-certificate:checked').data('product');
				var price;

				content = '';
				for (i in items) {
					price = Object.values(items[i].price)[0];

					$('.price_' + i).text(currency + ' ' + price.price.toFixed(2));
					content = content + '<div>' +
						//'<div class="pull-right">' + currency + ' ' + price.price.toFixed(2) + '</div>' +
						'<label>' +
						'<input type="radio" name="ssl[catalog_id]" value="' + items[i].catalog_id + '" class="order-certificate"' +
						'data-product="' + i + '"' + (selected == i ? ' checked' : '') + '> ' + items[i].name +
						'</label>' +
						'</div>';
				}

				content = $(content);
				if (!$('input:checked', content)[0]) {
					$('input', content).first().prop('checked', 1);
				}

				$('#ssl-products').html(content);
			}

			$('.order-certificate:checked').trigger('change');
		},
		rebuildSSLIPOptions: function() {
			var i, content, data;
			var account_id = $('#hosting-plan-account').val();
			var currency = CertificatesForm.formdata.currency;

			$('#parent_resource_id').val($('#hosting-plan-account option:selected').data('resource_id'));

			content = '';
			if (CertificatesForm.formdata.new_ips[account_id]) {
				$('#ssl-ip-type option[value="new"]').prop('disabled', false);
				data = CertificatesForm.formdata.new_ips[account_id];
				$('#ip-catalog-id').val(data.catalog_id);
				content = '<option value="' + data.period + '" data-price="' + data.price + '">' +
					data.period + ' ' + data.periodicity + ' - ' + currency + ' ' + data.price.toFixed(2) + '</option>';

				$('#ip-price').text(currency + ' ' + data.price.toFixed(2)).data('price', data.price);
			} else {
				$('#ssl-ip-type option[value="new"]').prop('disabled', true);
			}
			$('#ssl-ip-period').html(content);
			//reloadSelect2('#ssl-ip-period');

			content = '';
			if (CertificatesForm.formdata.cart_ips[account_id]) {
				data = CertificatesForm.formdata.cart_ips[account_id];
				for (i in data) {
					content = content + '<option value="' + data[i] + '">' + data[i] + '</option>';
				}
			}
			$('#new-options').html(content);

			content = '';
			if (CertificatesForm.formdata.dedicated_ips[account_id]) {
				$('#ssl-ip-type option[value="dedicated"]').prop('disabled', false);
				data = CertificatesForm.formdata.dedicated_ips[account_id];
				for (i in data) {
					content = content + '<option value="' + data[i] + '">' + data[i] + '</option>';
				}
			} else {
				$('#ssl-ip-type option[value="dedicated"]').prop('disabled', true);
				if (!$('#ssl-ip-type').val())
					$('#ssl-ip-type').val('sni');
			}
			$('#dedicated-options').html(content);
			//reloadSelect2('#ssl-ip-type');

			$('#ssl-ip-type').trigger('change');
		},
		placeOrder: function() {
			var request = new Request({ progress: true, disabled: '#certificates-form :input' })
				.success(function(result) {
					if (result.payment_form)
						$('#certificates-form').after(result.payment_form);
				})
				.always(function(result) {
					if (result.payment_form || result.redirect_to)
						Widget.spi('#certificates-form :input');
				})
				.post(Widget.url($('#certificates-form').attr('action')), $('#certificates-form').serialize());

			return false;
		},
		completeTerms: function(certificate) {
			var sectigo_terms = Widget.tr('ssl.sectigo_terms');
			var geotrust_terms = Widget.tr('ssl.geotrust_terms');

			if ($.inArray(certificate, ['sectigo_essential_wildcard', 'sectigo_essential']) !== -1) {
				label_html = sectigo_terms
			} else {
				label_html = geotrust_terms;
			}

			$('.ssl-terms-text').html(label_html);
		},
	}

	RegisterForm = {
		init: function () {
			$('#hosting-register-form')
				.off('submit.form').on('submit.form', $.proxy(this.registerReseller, this))
				.off('change.input').on('change.input', $.proxy(this.changeInput, this));
		},
		registerReseller: function (e) {
			var request = new Request({progress: true, disabled: '#hosting-register-form :input'})
				.success(function (result) {
					//console.log(result)
				})
				.always(function (result) {

				})
				.post(Widget.url($('#hosting-register-form').attr('action')), $('#hosting-register-form').serialize());

			return false;
		},
		changeInput: function (e) {
			$(e.target).removeClass('error').parent().find('.inline-error').hide();
		}
	}

	WidgetRouter = {
		routes: [],
		mode: null,
		root: '/',
		current: '',
		getFragment: function () {
			var fragment = '';
			if (this.mode === 'history') {
				fragment = this.clearSlashes(decodeURI(location.pathname + location.search));
				fragment = fragment.replace(/\?(.*)$/, '');
				fragment = this.root != '/' ? fragment.replace(this.root, '') : fragment;
			} else {
				var match = window.location.href.match(/#(.*)$/);
				fragment = match ? match[1] : '';
			}
			return this.clearSlashes(fragment);
		},
		clearSlashes: function (path) {
			return path.toString();
			/*return path.toString().replace(/\/$/, '').replace(/^\//, '');*/
		},
		add: function (re, handler) {
			if (typeof re == 'function') {
				handler = re;
				re = '';
			}
			this.routes.push({re: re, handler: handler});
			return this;
		},
		check: function (f) {
			var fragment = f || this.getFragment();
			for (var i = 0; i < this.routes.length; i++) {
				var match = fragment.match(this.routes[i].re);
				if (match) {
					match.shift();
					this.routes[i].handler.apply({}, match);
					return this;
				}
			}
			return this;
		},
		listenHandler: function () {
			var self = WidgetRouter;
			if (self.current !== self.getFragment()) {
				self.current = self.getFragment();
				self.check(self.current);
			}
		},
		listen: function () {
			// Listen on hash change and page load
			if (window.addEventListener) {
				window.addEventListener('hashchange', WidgetRouter.listenHandler);
				window.addEventListener('load', WidgetRouter.listenHandler);
			} else if (window.attachEvent) {
				window.attachEvent('onhashchange', WidgetRouter.listenHandler);
				window.attachEvent('onload', WidgetRouter.listenHandler);
			}
			return this;
		},
		getUrlParameter: function (sParam) {
			var sPageURL = window.location.search.substring(1);
			var sURLVariables = sPageURL.split('&');
			for (var i = 0; i < sURLVariables.length; i++) {
				var sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] == sParam)
					return sParameterName[1];
			}
		},
	}

	Widget = {
		tr_storage: {},

		resetPaymentForm: function(){
			Widget.hpi('#hosting-order-form :input');
			Widget.hpi('#hosting-payment-form :input');
		},

		url: function (url) {
			// if (typeof widget_location != 'undefined')
			// url += (url.indexOf('?') == -1 ? '?' : '&') + 'widget=' + widget_location + '&' + widget_params.substring(1);

			return url;
		},
		loadTr: function (json) {
			if (typeof json === 'object')
				this.tr_storage = json;
		},
		tr: function (tr_key) {
			if (typeof tr_key !== 'string' || tr_key === '')
				return;

			var key_parts = tr_key.split('.'), tmp = this.tr_storage, i, n;

			for (i = 0, n = key_parts.length; i < n; i++) {
				if (tmp !== null && tmp.hasOwnProperty(key_parts[i])) {
					tmp = tmp[key_parts[i]];
				} else {
					return;
				}
			}

			return tmp;
		},
		msg: function (type, msg, field, container, dont_scroll) {
			var styles = {'error': 'alert-danger', 'success': 'alert-success'};
			var style = styles[type] ? styles[type] : 'alert-warning';
			var input = $('[data-field="' + field + '"]', typeof container === 'string' ? container : '.hosting-widget');
			var mid = this.hashId(msg);

			if (!$('#' + mid)[0] && !input[0]) {
				$('<div class="alert ' + style + ' alert-dismissible" role="alert" id="' + mid + '" data-type="' + type + '">' +
					'<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
					msg + '</div>').appendTo('#hosting-widget-errors');
			}

			if (type == 'error' && input[0]) {
				var parent = input.parent();
				if (parent.hasClass('input-group'))
					parent = parent.parent();

				parent.find('div.inline-error').remove();
				parent.append('<div class="text-danger inline-error">' + msg + '</div>')
					.find('input:not([type="hidden"]),select,button')
					.addClass('error');
			}

			var errors_container = $('#hosting-widget-errors');
			if (errors_container.children().length && !errors_container.isOnScreen() && dont_scroll != true) {
				scrollTo(errors_container);
			} else if (input[0] && !input.isOnScreen() && dont_scroll != true) {
				scrollTo(input);
			}
		},
		msgs: function (msgs) {
			for (var i = 0; i < msgs.length; i++) {
				if (msgs[i].hasOwnProperty('container'))
					this.msg(msgs[i]['type'], msgs[i]['message'], msgs[i]['field'], msgs[i]['container'], true);
				else
					this.msg(msgs[i]['type'], msgs[i]['message'], msgs[i]['field'], false, true);
			}

			var errors_container = $('#hosting-widget-errors');
			var field_error = $('.error:first');
			if (errors_container.children().length && !errors_container.isOnScreen()) {
				scrollTo(errors_container);
			} else if (field_error[0] && !field_error.isOnScreen()) {
				scrollTo(field_error);
			}
		},
		clearMsgs: function (container) {
	/*
			//This is from widget version 3.28, but its not working here.

			container = (typeof container === 'string') ? container : '.hosting-widget';
			$('#hosting-widget-errors').empty();
			$('.inline-error', container).remove();
			$('.error', container).removeClass('error');
			*/

			$('#hosting-widget-errors').empty();
			$('div.inline-error').remove();
			$('.error', (typeof container === 'string') ? container : '.hosting-widget').removeClass('error');
		},
		hashCode: function (s) {
			return s.split("").reduce(function (a, b) {
				a = ((a << 5) - a) + b.charCodeAt(0);
				return a & a
			}, 0);
		},
		hashId: function (string, prefix) {
			if (!prefix) prefix = 'id';
			return prefix + this.hashCode(prefix + string);
		},
		spi: function (elements) {
			$('body').css('cursor', 'wait');
			if (elements)
				$(elements).prop('disabled', true);
		},
		hpi: function (elements) {
			$('body').css('cursor', 'auto');
			if (elements)
				$(elements).prop('disabled', false);
		}
	}

	Request = function (options) {
		if (!options)
			options = {};

		this.options = $.extend({
			url: '',
			method: 'GET',
			data: {},
			async: true,
			response: 'json',
			success: function (result, status, request) {
			},
			error: function (request, status, error) {
			},
			always: function (resultOrReuqest, status, error) {
			},
			progress: false, // <-- Show progress indicatior
			disabled: '',    // <-- Disable elements selector
			beforeRequest: function (context) {
				Widget.clearMsgs();
				if (context.options.progress)
					Widget.spi(context.options.disabled);
			},
			afterRequest: function (context) {
				if (context.options.progress)
					Widget.hpi(context.options.disabled);
			},
		}, options);

		for (var thesetter in this.options) {
			this[thesetter] = (function (setter) {
				return function (value) {
					this.options[setter] = value;
					return this;
				}
			}(thesetter));
		}

		this.send = function (url, data, method) {
			var options = this.options;
			if (url)
				options.url = url;
			if (data)
				options.data = data;
			if (method)
				options.method = method;

			var ajax_options = {
				url: options.url,
				type: options.method,
				data: options.data,
				async: options.async,
				dataType: options.response,
				context: this,
			}

			var req = this;
			if (options.beforeRequest)
				options.beforeRequest(req);

			return $.ajax(ajax_options)
				.done(function(result, status, request) {
					if (options.response == 'html') {
						try {
							var json_response = JSON.parse(result);
							result = json_response;
						}
						catch (e) {
						}
					}

					if (typeof result == 'object') {
						if (result.redirect_to) {
							location.href = result.redirect_to;
							return;
						}
						if (result.messages)
							Widget.msgs(result.messages);
					}

					options.success(result, status, request);
				})
				.fail(function(request, status, error) {
					options.error(request, status, error);
				})
				.always(function(resultOrReuqest, status, error) {
					if (options.afterRequest)
						options.afterRequest(req);

					options.always(resultOrReuqest, status, error);
				})
				;
		}

		this.get = function (url, data) {
			return this.send(url, data, 'GET');
		}

		this.post = function (url, data) {
			return this.send(url, data, 'POST');
		}
	}

	$.fn.isOnScreen = function () {
		var win = $(window);
		var viewport = {
			top: win.scrollTop(),
			left: win.scrollLeft()
		};
		viewport.right = viewport.left + win.width();
		viewport.bottom = viewport.top + win.height();

		try {
			var bounds = this.offset();
			bounds.right = bounds.left + this.outerWidth();
			bounds.bottom = bounds.top + this.outerHeight();
			return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
		} catch (e) {
		}

		return true;
	};

	$('body')
		.on('click.close', 'button.close', function () {
			$(this).parent().remove();
		})
		// Disable background scrolling
		.on('show.bs.modal', function() {
			$('body').css('overflow-y', 'hidden');
		})
		.on('hidden.bs.modal', function() {
			$('body').css('overflow-y', 'auto');
		})

		/*.tooltip({
			selector: '.tooltipui',
			//container: 'body',
		})*/
		.on('mouseenter', '.tooltipui', function() {
			var _this = $(this);
			var placement = (typeof _this.data('placement') == "undefined") ? "top" : _this.data('placement');

			try{
				if (!_this.data('bs.tooltip')) {
					_this.popover({
						html: true,
						placement: placement,
						trigger: 'manual',
						template: '<div class="popover popoverui" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
						animation: false
					});
				}
				_this.popover('show');
				$('#' + _this.attr('aria-describedby')).on('mouseleave', function() {
					_this.popover('hide');
				});
			}
			catch(e){

			}
		})
		.on('mouseleave', '.tooltipui', function() {
			var _this = $(this);
			setTimeout(function() {
				if (!$('#' + _this.attr('aria-describedby') + ':hover').length) {
					try{
						_this.popover('hide');
					}
					catch(e){

					}
				}
			}, 200);
		})
		.on('click', '.widget-dropdown-toggle', function(e){
		e.preventDefault();
		var menu = $(this).parent().find('.dropdown-menu')
		if(!$(this).parent().hasClass('open')){
			$(this).parent().addClass('open');
			menu.trigger('show.bs.dropdown');
		}
		else{
			$(this).parent().removeClass('open');
			menu.trigger('hide.bs.dropdown');
		}
	});


	;
})(jQuery);