<?php

return array(
	'api_error' => array(
		'puny_code_not_supported_for_tld'      => 'Punycode is not supported',
		'domain_api_error'                     => 'Error checking the domain',
		'hostname_is_already_hosted'           => 'The selected domain is already hosted with us',
		'system_error'                         => 'An unexpected error has occurred',
		'domain_not_available'                 => 'Domain name not available',
		'invalid_signature'                    => 'Invalid API Security Key',
		'price_not_found'                      => 'Catalog price not found',
		'order_request_not_found'              => 'Order payment not found',
		'parent_item_not_found'                => 'Parent item not selected',
		'item_not_found_in_catalog'            => 'Product not found in catalog',
		'max_group_items'                      => 'Maximum group items reached',
		'de_renewal_period_locked'             => 'The domain cannot be renewed at the moment',
		'order_already_created'                => 'Order already created',
		'resource_does_not_belong_to_reseller' => 'Resource not found',
		'unauthorized'                         => 'Unauthorize',
		'certificate_is_already_in_the_order'  => 'There is already an order for this certificate in your cart.',
		'certificate_is_already_processing'    => 'You have already submitted this SSL certificate for renewal.',
		'advanced_security_already_protected_url' => 'There is already an active "Advanced Security" service for this website.',
		'min_ns_count'                         => 'At least %min% unique nameservers should be defined for a domain name.',
		'max_ns_count'                         => 'No more than %max% unique nameservers can be defined for this domain name.',
		'stripe'                               => [ 'error' => [ 'processing_payment' => 'We were unable to create your order. Please try again.' ] ],
		'max_catalog_items'                    => 'Maximum catalog items reached',
	),
	'api_success' => array(
		'order_create_success' => 'Order completed. Please wait while you are being redirected to the payment processor...',
		'order_update_success' => 'Order updated. Please wait while you are being redirected to the payment processor...',
	),
	'layout'      => array(
		'widget'            => 'Widget',
		'toggle_navigation' => 'Toggle navigation',
		'hosting_widgets'   => 'Hosting widgets',
		'hosting_order'     => 'Hosting order',
		'certificate_order' => 'Certificate order',
		'domain_search'     => 'Domain search',
		'hosting_terms'     => 'Terms of use',
		'embed_code'        => 'Embed code',
	),

	// Order form labels
	'titles'      => array(
		'hosting_order'        => 'Hosting Order',
		'order_request'        => 'Payment Request',
		'order_certificate'    => 'SSL Certificate',
		'domain_search'        => 'Domain Search',
		'hosting_terms'        => 'Terms of Use',
		'hosting_payment'      => 'Hosting Payment',
		'thank_you'            => 'Thank You',
		'hosting_plan'         => 'Hosting Plan',
		'personal_information' => 'Personal Information',
		'payment_method'       => 'Payment Method',
		'embed_code_examples'  => 'Embed Code Examples',
		'terms_and_agreements' => 'Terms and Agreements',
		'error'                => 'Error',
	),

	'resources'=> array(
		'storage'=> 'SSD Storage',
		'traffic'=> 'Traffic',
		'traffic_mo' => 'Traffic /mo',
		'subdomain'=> 'Subdomains',
		'mysql_db'=> 'MySQL Databases',
		'domain_parking' => 'Domain Parking Slots',
		'ftp_account'=> 'FTP Accounts',
		'addon_domain' => 'Addon domain',
		'dedicated_cpu' => 'Dedicated CPU',
		'dedicated_ram' => 'Dedicated Memory',
	),

	'product_type' => array(
		'hosting'          => 'Web hosting',
		'domain'           => 'Domain',
		'ssl'              => 'SSL certificate',
		'hosting_resource' => 'Hosting resource',
		'server' => 'Server',
	),

	'purchase_actions' => array(
		'hosting'          => array(
			'order'   => 'Web Hosting',
			'renewal' => 'Web Hosting Renewal',
			'upgrade' => 'Web Hosting Upgrade',
			'migrate' => 'Migration',
		),
		'hosting_resource' => array(
			'order'   => 'Hosting Add-on',
			'renewal' => 'Hosting Add-on Renewal',
			'upgrade' => 'Hosting Add-on Upgrade',
		),
		'ssl'              => array(
			'order'   => 'SSL Certificate',
			'renewal' => 'SSL Certificate Renewal',
		),
		'domain'           => array(
			'register' => 'Domain Registration',
			'renewal'  => 'Domain Registration Renewal',
			'transfer' => 'Domain Transfer',
		),
		'domain_resource'  => array(
			'order'   => 'Domain Add-on',
			'renewal' => 'Domain Add-on',
		),
		'advanced_service' => array(
			'order' => 'Order Service',
			'renewal' => 'Service Renewal',
		),
		'server' => array(
			'order' => 'Server',
			'renewal' => 'Server Renewal',
			'upgrade' => 'Server Upgrade',
			'migrate' => 'Migration'
		),
	),

	'domain_search' => array(
		'available'          => array(
			'0' => 'Not Available',
			'1' => 'Available',
		),
		'register'           => 'Register',
		'transfer'           => 'Transfer',
		'unavailable'        => 'Transfer not supported',
		'error'              => 'Error checking domain',
		'invalid'            => 'Invalid domain',
		'not_offered'        => 'Registration not offered',
		'choose_search_tlds' => 'Please select search TLDs',
		'check_all'          => 'Check all'
	),

	'ssl' => array(
		'approver_emails_error'   => 'Unable to get valid approver emails',
		'confirm_new_ip_item'     => 'Please confirm that you want to add a new dedicated IP address to your order items.',
		'valid_approver_emails'   => 'Please select approval email address',
		'valid_common_name_first' => 'Please add valid common name first',
		'geotrust_terms' => 'I have read and will abide by the <a href="%url%" target="_blank" class="external">Hosting Terms of Use</a> and <a href="https://www.digicert.com/content/dam/digicert/pdfs/legal/Certificate-Terms-of-Use.pdf" target="_blank">GeoTrust® SSL Certificate Subscriber Agreement</a>',
		'sectigo_terms' => 'I have read and will abide by the <a href="%url%" target="_blank" class="external">Hosting Terms of Use</a> and <a href="https://sectigo.com/uploads/files/Certificate-Subscriber-Agreement-v2.1.pdf" target="_blank">Sectigo Certificate Agreement</a>',
	),

	'locations' => array(
		'centurylink' => 'USA',
		'iadvantage' => 'Asia (Hong Kong)',
		'neterra' => 'Europe',
	),

	'item_action' => array(
		'order'   => 'Order',
		'renewal' => 'Renewal',
		'upgrade' => 'Upgrade',
		'migrate' => 'Migrate',
	),

	'periodicity' => array(
		'YR' => 'yr',
		'MO' => 'mo',
	),

	'please_select'                      => 'Please select',
	'domain_placeholder'                 => 'example.com',
	'order_total'                        => 'Order Total',
	'total'                              => 'Total',
	'balance_due'                        => 'Total due',
	'payments_received_title'            => 'Payments received for this order',
	'available_in_public_embed_examples' => 'To embed a code sample into your web page, please copy the appropriate code from the examples below, and paste it in your web page HTML code where you would like the widget to appear.',
	'embed_example_order'                => 'Hosting order page',
	'embed_example_request'              => 'Payment request page',
	'embed_example_thankyou'             => 'Thank you page',
	'embed_example_payment'              => 'Payment page',
	'embed_example_domain_search'        => 'Domain search page',
	'no_offered_hosting_plans'           => 'No hosting plans are offered',

	'labels' => array(
		'domain_name'        => 'Domain Name',
		'new_domain'         => 'New domain name',
		'existing_domain'    => 'Use my already registered domain name',
		'server_location'    => 'Server Location',
		'hosting_plan'       => 'Hosting Plan',
		'period'             => 'Period',
		'terms'              => 'Terms of Use',
		'terms_agree'        => 'I have read and will abide by the <a href="%url%" target="_blank">Hosting Terms of Use</a>',
		'icann_verification' => 'ICANN Verification',
		'icann_agree'        => 'I understand that my data should be verified as per ICANN regulations',
		'icann_info'         => 'As per ICANN regulations, the registrant contact details of any domain are a subject of verification. If your registrant contact details have not been verified before, you will receive an email verification at this e-mail address. You will need to follow the verification link within 15 days, to avoid any disruption of your domain.',
		'renew_at'           => 'Renew at'
	),

	'btns' => array(
		'order'   => 'Order',
		'check'   => 'Check',
		'pay_now' => 'Pay now',
		'pay'     => 'Pay',
		'close' => 'Close',
		'more' => 'More',
		'less' => 'Less',
	),

	'periods' => array(
		'YR' => 'year|years',
		'MO' => 'month|months',
	),

	'contacts'            => array(
		'firstname'     => 'First Name',
		'lastname'      => 'Last Name',
		'address'       => 'Address 1',
		'address2'      => 'Address 2',
		'company'       => 'Company',
		'city'          => 'City',
		'state'         => 'State/Province',
		'zip'           => 'ZIP (Postal Code)',
		'country'       => 'Country',
		'email'         => 'Email',
		'email2'        => 'Alternative Email',
		'phone'         => 'Phone Number',
		'phone_country' => 'Phone Code',
		'fax'           => 'Fax Number',
		'fax_country'   => 'Fax Code',
	),

	// Payment & Thank you labels
	'your_order_thankyou' => 'Thank you for your order #%order_id%',
	'hosting_order_due'   => 'Order #%order_id%',
	'order_summary'       => 'Here is a summary of your order',
	'payment_details'     => 'Enter payment details',
	'payment_info_text'   => 'There is an outstanding amount of <b>%outstanding%</b> required for the completion of your order.',
	'thankyou_info_text'  => 'Our team will process it as soon as possible. You will be contacted if needed.',

	'invalid' => array(
		'username'      => 'Invalid Username',
		'store_title'   => 'Invalid Store Brand Name',
		'store_name'    => 'Invalid Store Slug',
		'password'      => 'Invalid Password',
		'full_name'     => 'Please provide valid first and last name',
		'auth_token'    => 'Invalid API Token',
		'order_id'      => 'Invalid order number',
		'hosting'       => array(
			'hostname' => 'Invalid domain',
		),
		'sld'           => 'Invalid SLD',
		'tld'           => 'Invalid TLD',
		'firstname'     => 'Invalid First Name',
		'lastname'      => 'Invalid Last Name',
		'address'       => 'Invalid Address 1',
		'address2'      => 'Invalid Address 2',
		'city'          => 'Invalid City',
		'state'         => 'Invalid State/Province',
		'zip'           => 'Invalid ZIP/Postal Code',
		'country'       => 'Invalid Country',
		'email'         => 'Invalid Email',
		'email2'        => 'Invalid Alternative Email',
		'phone'         => 'Invalid Phone Number',
		'phone_country' => 'Invalid Phone Code',
		'fax'           => 'Invalid Fax Number',
		'fax_country'   => 'Invalid Fax Code',
		'contact'       => array(
			'firstname'     => 'Invalid First Name',
			'lastname'      => 'Invalid Last Name',
			'address'       => 'Invalid Address 1',
			'address2'      => 'Invalid Address 2',
			'city'          => 'Invalid City',
			'state'         => 'Invalid State/Province',
			'zip'           => 'Invalid ZIP/Postal Code',
			'country'       => 'Invalid Country',
			'email'         => 'Invalid Email',
			'email2'        => 'Invalid Alternative Email',
			'phone'         => 'Invalid Phone Number',
			'phone_country' => 'Invalid Phone Code',
			'fax'           => 'Invalid Fax Number',
			'fax_country'   => 'Invalid Fax Code',
		),
		'ssl'           => array(
			'common_name'       => 'Invalid common name',
			'approver_email'    => 'Invalid Approval Email',
			'common_name'       => 'Invalid Common Name',
			'organization'      => 'Invalid Organization/Company Name',
			'organization_unit' => 'Invalid Organizational Unit Name (Section)',
			'city'              => 'Invalid Locality/City Name',
			'state'             => 'Invalid State or Province Name (full name)',
			'email'             => 'Invalid E-mail Address',
			'address'           => 'Invalid Address',
			'address2'          => 'Invalid Address 2',
			'address3'          => 'Invalid Address 3',
			'zip'               => 'Invalid ZIP (Postal Code)',
		),
		'domain'        => [
			'epp'              => 'Invalid domain EPP',
			'epp' => 'Invalid domain EPP',
			'ns1' => 'Please enter a valid nameserver hostname.',
			'ns2' => 'Please enter a valid nameserver hostname.',
			'ns3' => 'Please enter a valid nameserver hostname.',
			'ns4' => 'Please enter a valid nameserver hostname.',
			'ns5' => 'Please enter a valid nameserver hostname.',
			'extra_attributes' => [
				'eu_adr_lang' => 'Please choose an alternate dispute resolution language.',
				/* .US */
				// Вид на регистранта / Type of Registrant / Domaininhaber
				'us_nexus'    => 'The value for Type of Registrant is invalid',
				// Предназначение за ползване / Purpose of Use / Nutzung
				'us_purpose'  => 'The value for Purpose of Use is invalid',

				/* .EU */
				// Промяна на резолюцията за диспут / Alternate Dispute Resolution / Alternatives Streitbeilegungsverfahren (ADR)
				'eu_adr_lang' => 'The value for Alternate Dispute Resolution is invalid',
				'eu_country_of_citizenship' => '<p>A country from the European Union (EU) which grants the right to register .eu domains.</p>
<p>Following regulation (EU) 2019/517, which introduces the possibility for all EU citizens to register .eu domain names regardless of residence, you must:</p>
<p>1. Have a place of residence established in any of the EU or EEA countries, regardless of whether you are an individual or an organization.</p>
<p>or</p>
<p>2. Be a natural person (individual) and have a European citizenship granted from one of the EU Member States, regardless of your place of residence.</p>',

				/* .HK */
				// Вид на регистранта / Domain Category / Domaininhaber
				'cattype'     => 'The value for Domain Category is invalid',
				// Вид документ на регистранта / Registrant Document Type / Dokumententyp
				'custom21'    => 'The value for Registrant Document Type is invalid',
				// Номер на документ / Registrant Document Number / Dokumentennummer
				'custom31'    => 'The value for Registrant Document Number is invalid',
				// Регистрантът е под 18 години / Registrant Under 18 years / Registrant ist unter 18 Jahre alt
				'custom51'    => 'The value for Registrant Age is invalid',
				// Име на организацията / Registrant Company / Unternehmen
				'company1'    => 'The value for Registrant Company is invalid',
				// Допълнителни данни за вид на документа / Registrant Other Document Type / Weitere Documententypangabe
				'other1'      => 'The value for Registrant Other Document Type is invalid',

				// .CA
				'cira_legal_type' => 'The value for Legal Type for Registrant Contact is invalid',
				'cira_language' => 'The value for Preferred Language for Communication is invalid',
				'cira_agreement_value' => 'You must read and agree to the CIRA Registrant Agreement 2.1',

				// .CO.UK
				'uk_legal_type' => 'The value for Legal Type of Registrant is invalid',
				'uk_reg_co_no' => 'The value for Company Identification Number is invalid',
				'registered_for' => 'Registrant Name (Company or Person) is invalid',
			]
		],
		'service' => [
			'url' => 'Invalid Protected URL'
		],
		'id'              => 'Invalid ID',
		'client_store_id' => 'Client not found',
	),

	'weak' => array(
		'password' => 'The password is too simple.'
	),

	'invalid.contact.country_eu' => 'To register a .eu domain, you must provide a valid address in a country in the European Union.',
	'invalid.contact.email_eu' => 'The email address of a .eu domain should not contain the domain name you want to register.',
	'invalid.contact.country_de' => 'To register a .de domain, you must provide a valid address in Germany.',
	'invalid.contact.country_ca' => 'To register a .ca domain, you must provide a valid address in Canada.',
	'invalid.contact.country_bg' => 'An EU member state is required for .BG domain registrations.',

	'required' => array(
		'username'                         => 'Username is required',
		'password'                         => 'Password is required',
		'full_name'                        => 'Full Name is required',
		'firstname'                        => 'First Name is required',
		'lastname'                         => 'Last Name is required',
		'address'                          => 'Address 1 is required',
		'city'                             => 'City is required',
		'zip'                              => 'ZIP (Postal Code) is required',
		'country'                          => 'Country is required',
		'state'                            => 'State/Province is required',
		'email'                            => 'Email is required',
		'phone_country'                    => 'Phone Code is required',
		'fax_country'                      => 'Fax Code is required',
		'phone'                            => 'Phone Number is required',
		'contact'                          => array(
			'firstname'     => 'First Name is required',
			'lastname'      => 'Last Name is required',
			'address'       => 'Address 1 is required',
			'city'          => 'City is required',
			'state'         => 'State/Province is required',
			'zip'           => 'ZIP/Postal Code is required',
			'country'       => 'Country is required',
			'email'         => 'Email is required',
			'phone'         => 'Phone Number is required',
			'phone_country' => 'Phone Code is required',
		),
		'order_id'                         => 'Required order number',
		'service'                          => array(
			'period' => 'Please select service period',
		),
		'you_must_agree_with_terms_of_use' => 'You have to agree with our Hosting Terms Of Use.',
		'you_must_agree_with_icann_terms'  => 'Please confirm the registrant verification statement.',
		'items'                            => 'Please select at least one item to purchase.',
		'hosting'                          => array(
			'hostname' => 'Main domain is required',
		),
		'domain'                           => array(
			'sld'              => 'Domain name is required',
			'epp'              => 'EPP code is required',
			'extra_attributes' => array(
				'eu_adr_lang' => 'Please choose an alternate dispute resolution language.',
				/* .US */
				// Вид на регистранта / Type of Registrant / Domaininhaber
				'us_nexus'    => 'Type of Registrant is required',
				// Предназначение за ползване / Purpose of Use / Nutzung
				'us_purpose'  => 'Purpose of Use is required',
				'global_cc_us' => 'Nexus Country is required',

				/* .EU */
				// Промяна на резолюцията за диспут / Alternate Dispute Resolution / Alternatives Streitbeilegungsverfahren (ADR)
				'eu_adr_lang' => 'Alternate Dispute Resolution is required',

				/* .HK */
				// Вид на регистранта / Domain Category / Domaininhaber
				'cattype'     => 'Domain Category is required',
				// Вид документ на регистранта / Registrant Document Type / Dokumententyp
				'custom21'    => 'Registrant Document Type is required',
				// Номер на документ / Registrant Document Number / Dokumentennummer
				'custom31'    => 'Registrant Document Number is required',
				// Регистрантът е под 18 години / Registrant Under 18 years / Registrant ist unter 18 Jahre alt
				'custom51'    => 'Registrant Age is required',
				// Име на организацията / Registrant Company / Unternehmen
				'company1'    => 'Registrant Company is required',
				// Допълнителни данни за вид на документа / Registrant Other Document Type / Weitere Documententypangabe
				'other1'      => 'Registrant Other Document Type is required',

				// .CA
				'cira_legal_type' => 'Legal Type of Registrant Contact is required',
				'cira_language' => 'Preferred Language for Communication is required',
				'cira_agreement_value' => 'You must read and agree to the CIRA Registrant Agreement 2.1',

				// .CO.UK
				'uk_legal_type' => 'Legal Type of Registrant is required',
				'uk_reg_co_no' => 'Company Identification Number is required',
				'registered_for' => 'Registrant Name (Company or Person) is required'
			),
		),
		'ssl'                              => array(
			'common_name'       => 'Common Name is required',
			'approver_email'    => 'Approval Email is required',
			'organization'      => 'Organization/Company Name is required',
			'organization_unit' => 'Organizational Unit Name (Section) is required',
			'city'              => 'Locality/City Name is required',
			'state'             => 'State or Province Name (full name) is required',
			'address'           => 'Address is required',
			'zip'               => 'ZIP (Postal Code) is required',
			'country'           => 'Country is required'
		),
		'terms' => 'You must agree to the Terms of Use.',
		'store_name'=> 'Store Slug is required',
		'store_title'=> 'Store Brand Name is required',
	),
	'units'    => array(
		'GB'    => 'GB',
		'GB/MO' => 'GB/mo',
	),
	'plan_group' => array(
		'shared' => 'Shared Hosting',
		'app' => 'Node.js Hosting',
		'vps' => 'Managed VPS',
		'server' => 'SmartVPS Servers'
	),

	'existing' => [
		'store_name' => 'Store slug already taken, please type another one',
		'email' => 'There is an account already using the provided email address, please choose another one.',
		'username' => 'The username is not available.'
	],

	'error' => array(
		'generic'                           => 'Application error',
		'payment_failed'                    => 'We were unable to process your payment. Please try again.',
		'product_not_found'                 => 'The selected product cannot be found.',
		'failed_to_retrieve_order_data'     => 'Failed to retrieve order data.',
		'failed_to_retrieve_supported_tlds' => 'Failed to retrieve supported TLDs',
		'tld_not_supported'                 => 'The registration of this top-level domain name is not offered with the current setup selection, or at all.',
		'item_not_found_in_catalog'         => 'The product you have selected is no longer available',
		'empty_items_list'                  => 'Empty order items list',
		'order_create_failed'               => 'We were unable to create your order. Please try again.',
		'invalid_signature'                 => 'Invalid API Security Key',
		'adding_payment'                    => 'An error occurred, and the payment could not be added.',
		'detecting_payment'                 => 'The payment could not be registered.',
		'link_expired'                      => 'The payment request has expired.',
		'order_request_details'             => 'Error getting payment request details.',
		'bad_nonce'                         => 'Action failed. Please refresh the page and retry.',

		'username' => [
			'long_20' => 'The username cannot be longer than 20 characters.',
		],
		'full_name' => [
			'long_60' => 'The maximum length of each name must be less than 60 characters.',
			'long_120' => 'Your names cannot be longer than 120 symbols.',
		],
		'getting_approver_email' => 'Error getting approver email.',
		'invalid_order_request' => 'The link you have followed is incorrect or outdated. If you have typed the address manually, please make sure that you are typing it correctly.',
	),
	'payment_error' => array(
		'payment_failed' => 'Payment failed, please try again.',
		'payment_declined' => 'Your payment has been declined, please try again or contact your card issuer.',
	),
	'stripe' => array(
		'label_card' => 'Card Number',
		'label_exp'  => 'Expiration Date',
		'label_cvc'  => 'Security Code',

		'hint_card' => 'The 16 digits on the front of your card.',
		'hint_exp'  => 'The date your card expires (MM/YYYY). Find this on the front of your card.',
		'hint_cvc'  => 'The last 3 digits displayed on the back of your card (CVC/CVV).',

		'placeholder_card' => 'Card Number',
		'placeholder_cvc'  => 'CVC/CVV',

		'card_error' => array(
			'invalid_number'       => 'The supplied credit card number is invalid.',
			'invalid_expiry_month' => 'The expiration month of the credit card is invalid.',
			'invalid_expiry_year'  => 'The expiration year of the credit card is invalid.',
			'invalid_exp'          => 'The expiration date of the credit card is invalid.',
			'invalid_cvc'          => 'The security code of the credit card is invalid.',
			'incorrect_number'     => 'The supplied credit card number is invalid.',
			'expired_card'         => 'The supplied credit card has expired.',
			'incorrect_cvc'        => 'The security code of the credit card is invalid.',
			'incorrect_zip'        => 'The zip code of the credit card is invalid.',
			'card_declined'        => 'The supplied credit card was declined.',
			'missing'              => 'There is no card on a customer that is being charged.',
			'processing_error'     => 'An error occurred while processing the credit card.'
		),
		'error'  => array('processing_payment' => 'Your payment has been declined, please try again or contact your card issuer.'),
	),

	'countries' => array(
		'AF' => 'Afghanistan',
		'AX' => 'Åland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BQ' => 'Bonaire, Sint Eustatius and Saba',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'VG' => 'British Virgin Islands',
		'BN' => 'Brunei',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CW' => 'Curaçao',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'CD' => 'DR Congo',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern and Antarctic Lands',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island and McDonald Islands',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'CI' => 'Ivory Coast',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'XK' => 'Kosovo',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Laos',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macau',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'KP' => 'North Korea',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestine',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn Islands',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'CG' => 'Republic of the Congo',
		'RE' => 'Réunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthélemy',
		'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'São Tomé and Príncipe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SX' => 'Sint Maarten',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia',
		'KR' => 'South Korea',
		'SS' => 'South Sudan',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syria',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Minor Outlying Islands',
		'VI' => 'United States Virgin Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VA' => 'Vatican City',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'WF' => 'Wallis and Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	),

	'request' => array(
		'title'                           => 'Request new order',
		'desc'                            => 'Build store payment request and send it to your client for payment',
		'store_label'                     => 'Hosting store',
		'item_action_label'               => 'Item action',
		'new_item_label'                  => 'New item',
		'hosting_account_label'           => 'Hosting account',
		'domain_label'                    => 'Domain',
		'certificate_label'               => 'Certificate',
		'link_expiration_label'           => 'Link expiration days',
		'link_locked_label'               => 'Lock item selections',
		'order_total_label'               => 'Total',
		'upgrade_from_plan_label'         => 'Current plan',
		'upgrade_to_plan_label'           => 'New plan',
		'send_notification_label'         => 'Client notification',
		'send_notification_sublabel'      => 'Send email',
		'link_locked_sublabel'            => 'Locked',
		'order_items_subtitle'            => 'Order items',
		'item_config_subtitle'            => 'Item config',
		'item_contact_subtitle'           => 'Item contacts',
		'item_advanced_security_subtitle' => 'Advanced security service',
		'personal_information_subtitle'   => 'Client information',
		'request_settings_subtitle'       => 'Request settings',
		'transfer_information_subtitle'   => 'Transfer information',
		'search_result_subtitle'          => 'Search result',
		'epp_code'                        => 'EPP Code',
		'tld_specific_data'               => 'TLD specific data',
		'email_notification_desc'         => 'Notification will be sent to personal email to complete request on: :url',
		'email_notification_warning_desc' => 'Please set store widget url',
		'item_action_th'                  => 'Action',
		'item_type_th'                    => 'Type',
		'item_name_th'                    => 'Item',
		'item_expiration_th'              => 'Expiration',
		'item_new_expiration_th'          => 'New expiration',
		'item_quantity_th'                => 'Quantity',
		'item_period_th'                  => 'Period',
		'item_price_th'                   => 'Price',
		'order_actions'                   => array(
			'order'    => 'Order',
			'renewal'  => 'Renewal',
			'upgrade'  => 'Upgrade',
			'migrate'  => 'Migration',
			'register' => 'Register',
			'transfer' => 'Transfer',
			'refill'   => 'Refill',
			'purchase' => 'Purchase',
		),
		'item_action'                     => array(
			'order'    => 'Order',
			'renewal'  => 'Renew',
			'upgrade'  => 'Upgrade',
			'migrate'  => 'Migrate',
			'register' => 'Register',
			'transfer' => 'Transfer',
			'new'      => 'Add resource',
		),
		'item_type'                       => array(
			'hosting'                => 'Hosting',
			'domain'                 => 'Domain',
			'bonus:domain'           => 'Discounted Domain',
			'extra:domain'           => 'Extra Domain',
			'standalone:domain'      => 'Standalone Domain',
			'ssl'                    => 'SSL Certificate',
			'bonus:ssl'              => 'Bonus SSL Certificate',
			'extra:ssl'              => 'SSL Certificate',
			'standalone:ssl'         => 'Standalone SSL Certificate',
			'extra:hosting_resource'       => 'Hosting Resource',
			'extra:advanced_service' => 'Advanced Service',
			'advanced_service'       => 'Advanced Service',
			'server'                 => 'Server',
		),
		'domain_type' => [
			'main' => 'main',
			'addon' => 'addon',
			'new' => 'new',
		],
		'btn'                             => array(
			'add_item' => 'Add item',
			'create'   => 'Create',
			'expire'   => 'Expire',
			'search'   => 'Search',
			'done'     => 'Done',
			'reset'    => 'Reset',
		),
		'item_btn'                        => array(
			'add_title'      => 'Add extra',
			'config_title'   => 'Config item',
			'contact_title'  => 'Item contact',
			'discount_title' => 'Item discount',
			'delete_title'   => 'Delete item',
		),

		'item_extra_resource_subtitle' => 'Extra resource',
		'item_hosting_subtitle'        => 'Hosting',
		'item_domain_subtitle'         => 'Domain',
		'item_ssl_subtitle'            => 'Certificate',
		'item_upgrade_subtitle'        => 'Upgrade',
		'item_migrate_subtitle'        => 'Migrate',

		'hosting'                  => array(
			'hostname' => 'Main domain',
		),
		'domain'                   => array(
			'domain' => 'Domain',
		),
		'service' => array(
			'url' => 'Protected URL',
		),
		'ssl_ip_type'              => array(
			'noip'      => 'No installation',
			'sni'       => 'Shared IP',
			'dedicated' => 'Existing Dedicated IP',
			'new'       => 'New Dedicated IP',
		),
		'ssl_add_new_dedicated_ip' => 'Add new dedicated IP',
		'domain_search'            => array(
			'epp_info' => 'Transfer authorization code provided by domain registrar'
		),
		'valid_approver_emails'    => 'Please select approval email address',

		'ssl' => array(
			'approver_email'    => 'Approval Email',
			'common_name'       => 'Common Name',
			'organization'      => 'Organization/Company Name',
			'organization_unit' => 'Organizational Unit Name (Section)',
			'address'           => 'Address',
			'address2'          => 'Address 2',
			'address3'          => 'Address 3',
			'city'              => 'Locality/City Name',
			'state'             => 'State or Province Name (full name)',
			'zip'               => 'ZIP (Postal Code)',
			'country'           => 'Country Name',
			'email'             => 'E-mail Address',
			'ip_type'           => 'Installation Type',
			'ip'                => 'IP Address',
			'ip_type.noip'      => 'No installation',
			'ip_type.sni'       => 'Without a dedicated IP address',
			'ip_type.dedicated' => 'On a dedicated IP address',
		),

		'recommended_upgrade_generic' => 'Your account is currently on the %current_plan% plan. Get more out of your hosting account by upgrading it to the %upgrade_plan% plan.',

		'recommended_upgrade_plan' => 'We recommend that you upgrade from your current %current_plan% plan to the %upgrade_plan% plan. If you choose to upgrade your account will have about %space_diff% times more space and %services_diff% times more slots for subdomains, databases, parking domains, and FTP accounts. Your price of the %upgrade_plan% plan is %monthly_price% / month.',

		'recommended_upgrade_vps' => 'We recommend that you upgrade from your current %current_plan% plan to the %upgrade_plan% plan. If you choose to upgrade your account will have %cpu_count% dedicated CPUs, %memory_count% dedicated memory, about %space_diff% times more space, and %services_diff% times more slots for subdomains, databases and FTP accounts. Your price of the %upgrade_plan% plan is %monthly_price% / month.',

		'item_contact_desc'         => 'Setup custom item contact information if it is different from the order billing contacts.',
		'use_order_billing_contact' => 'Use order billing contacts',
		'use_custom_contact'        => 'Use custom contacts',

		'price_rules' => [
			'hosting_order'       => 'Hosting order',
			'hosting_renewal'     => 'Hosting renewal',
			'hosting_upgrade'     => 'Hosting upgrade',
			'hosting_migrate'     => 'Hosting migrate',
			'bonus_extra_order'   => 'Order without hosting',
			'bonus_extra_renewal' => 'Renewal without hosting',
			'rgp_renewal'         => 'RGP renewal',
			'customer_discount'   => 'Customer discount',
			'eu_10_10_10' => 'Special Offer',
			'premium_domain_renewal' => 'Premium domain renewal',
		],

		'dns_configuration' => 'DNS configuration',
		'hostname' => 'Hostname',
		'ns' => 'Name Server %num%',
		'use_global_dns' => 'I want to use my global DNS configuration for the above domain',
		'dns_configuration_info' => 'If you check this option and you have custom DNS records set via the DNS Manager in the hosting Control Panel, then the newly-registered domain name will be parked and set with your custom DNS records. Otherwise, the domain will be parked using the default DNS and mail settings.',
		'host_as_info' => '<p>You can host the new domain name as parked to existing main/addon domain or as a completely new addon domain name.</p>
		<ul>
			<li>Addon domain - Can be used for an additional website, it has its own web and mail services. </li>
			<li>Parked domain - Only a pointer to an existing website and mail service at your account.</li>
		</ul>',
		'this_will_add_1_additional_addon_slot' => 'This will add upgrade for 1 addon domain to the order',
		'not_enough_addon_slots_available' => 'Not enough addon slots available for some domains. They will be parked to account main domain.',

		'max_group_items' => 'Maximum group items reached',
		'max_group_items_pending_order' => 'There is a %group% pending order #%order_id% for %parent%',
		'resource_not_renewable' => 'The resource cannot be renewed at the moment.',
		'account_cannot_be_upgraded' => 'This account is currently inactive and cannot be upgraded.',
		'account_cannot_be_upgraded_expired' => 'Expired accounts cannot be upgraded. The account needs to be renewed first.',
		'account_cannot_be_upgraded_without_renewal' => 'The resource cannot be renewed if it expires after the hosting account. To renew it, you need to renew the hosting account as well.',
		'hosting_not_renewable' => 'The hosting account %item% cannot be renewed at the moment.',
		'domain_not_renewable' => 'The domain %item% cannot be renewed at the moment.',
		'ssl_not_renewable' => 'The SSL certificate %item% cannot be renewed at the moment.',
		'upgrade_min_period' => 'There is less than a month until the expiration of your account: %item%',
		'upgrade_min_period_renew_url' => 'There is less than a month until the expiration of your account. You can upgrade with account renewal.',
		'migrate_min_period' => 'Expired accounts cannot be migrated. The account needs to be renewed first.',
		'max_renew_period' => 'This service cannot be extended further.',
		'no_request_items' => 'No request items',
		'catalog_price_not_enabled' => 'Catalog price not enabled: %item%',
		'upgrade_no_available_plans' => 'Plan not upgradeable',
		'migrate_no_avaliable_plans' => 'No plans available for migration',
		'resource_not_in_store' => 'Resource is not in the selected store',
		'hosting_not_in_store' => 'The hosting account %item% is not in the selected store.',
		'domain_not_in_store' => 'The domain %item% is not in the selected store.',
		'ssl_not_in_store' => 'The SSL certificate %item% is not in the selected store.',
		'advanced_service_not_in_store' => '%item% is not in the selected store.',
		'hosting_resource_not_in_store' => '%item% is not in the selected store.',
		'server_not_in_store' => '%item% is not in the selected store.',
		'parent_item_not_found' => 'Parent item not selected',
		'order_request' => 'Payment request',
		'enter_valid_common_name' => 'Please enter a valid common name first',
		'calculate_domain_prices_fail' => 'An error occurred and the prices could not be calculated. Please try again.',
		'migrate_no_available_plans' => 'There are no possible hosting plans for a migration.',
		'de_renewal_period_locked' => 'The domain %item% cannot be renewed at the moment. You will be able to extend this domain after <strong>%date%</strong>. Please contact our support team for more information.',
		'de_renewal_period_expired' => 'The domain %item% cannot be renewed at the moment. Please contact our support team for more information.',
		'domain_is_already_in_the_order' => 'The domain is already added to the order.',
		'existing_order_hosting' => 'There is already a pending order #%order_id% for this account.',
		'existing_order_domain' => 'There is already a pending order #%order_id% for this domain.',
		'existing_order_ssl' => 'There is already a pending order #%order_id% for this SSL certificate.',
		'existing_order_hosting_resource' => 'There is already a pending order #%order_id% for this hosting resource.',
		'existing_order_server' => 'There is already a pending order #%order_id% for this account.',
		'existing_order_advanced_service' => 'There is already a pending order #%order_id% for this advanced service.',
		'hosting_resource_not_renewable' => 'The resource cannot be renewed at the moment.',
		'transfer_check_failed' => 'There was an error checking the transfer requirements. Please try again, and if the problem persists, contact our Support Department.',
		'domain_api_error' => 'A problem occurred while trying to process your request. Please try again, and if the problem persists, contact our Support Department.',
		'please_select_quantity_and_period' => 'Please select quantity and period',
		'server_not_renewable' => 'The server %item% cannot be renewed at the moment.',
	),

	'extra_attributes' => [
		// US
		'us_nexus_label'      => 'Type of Registrant',
		'us_nexus'            => [
			'C11' => 'US Citizen',
			'C12' => 'Permanent Resident',
			'C21' => 'Business Entity',
			'C31' => 'Foreign Entity',
			'C32' => 'US Based Office',
		],
		'us_nexus_help_title' => 'Nexus Category',
		'us_nexus_help_info'  => '
	<ul>
		<li><strong>US Citizen:</strong> A natural person who is a US Citizen.</li>
		<li><strong>Permanent Resident:</strong> A natural person who is a Permanent Resident.</li>
		<li><strong>Business Entity:</strong> An entity or organization that is (i) incorporated within one of the fifty US states, the District of Columbia, or any of the US possessions or territories, or (ii) organized or otherwise constituted under the laws of a state of the US, the District of Columbia or any of its possessions and territories (including federal, state, or local government of the US, or a political subdivision thereof, and non-commercial organizations based in the US.).</li>
		<li><strong>Foreign Entity:</strong> A foreign organization that regularly engages in lawful activities (sales of goods or services or other business, commercial, or non-commercial, including not for profit relations) in the United States.</li>
		<li><strong>US Based Office:</strong> An organization has an office or other facility in the U.S.</li>
	</ul>',

		'us_purpose_label' => 'Purpose of Use',
		'us_purpose'       => [
			'P1' => 'Business Use for Profit',
			'P2' => 'Non-profit Business',
			'P3' => 'Personal Use',
			'P4' => 'Educational Purposes',
			'P5' => 'Government Purposes',
		],

		'global_cc_us_label'     => 'Nexus Country',

		// EU
		'eu_adr_lang_label'      => 'Alternate Dispute Resolution',
		'eu_adr_lang'            => [
			'bg' => 'Bulgarian',
			'cs' => 'Czech',
			'da' => 'Danish',
			'de' => 'German',
			'el' => 'Greek',
			'en' => 'English',
			'es' => 'Spanish',
			'et' => 'Estonian',
			'fi' => 'Finnish',
			'fr' => 'French',
			'hu' => 'Hungarian',
			'it' => 'Italian',
			'lt' => 'Lithuanian',
			'lv' => 'Latvian',
			'mt' => 'Maltese',
			'nl' => 'Dutch',
			'pl' => 'Polish',
			'pt' => 'Portugese',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'sv' => 'Swedish',
			'ro' => 'Romanian',
		],
		'eu_adr_lang_help_title' => 'Alternate Dispute Resolution',
		'eu_adr_lang_help_info'  => 'The language you will be handling your .EU legal disputes in.',

		'eu_country_of_citizenship_label' => 'EU/EEA Country of Citizenship',
		'eu_country_of_citizenship_placeholder' => 'Leave blank if your country of residence is in the EU or EEA',
		'eu_country_of_citizenship_help_title' => 'EU/EEA Country of Citizenship',
		'eu_country_of_citizenship_help_info' => '<p>A country from the European Economic Area (EEA), which includes all European Union (EU) countries and also Iceland, Liechtenstein and Norway.</p><br /><p>Leave blank if your country of residence is in the EU or EEA.</p>',

		'eu_whoispolicy_label'      => 'Registry Data Transfer Policy',
		'eu_whoispolicy'            => [
			'I AGREE' => 'I agree'
		],
		'eu_whoispolicy_help_title' => 'Registry Data Transfer Policy',
		'eu_whoispolicy_help_info'  => 'I hereby agree that the Registry is entitled to transfer the data contained in this application to third parties (i) if ordered to do so by a public authority, carrying out its legitimate tasks; and (ii) upon demand of an ADR Provider as mentioned in section 16 of the Terms and Conditions which are published at www.eurid.eu; and (iii) as provided in Section 2 (WHOIS look-up facility) of the .eu Domain Name WHOIS Policy which is published at www.eurid.eu.',

		// HK
		'cattype_label'             => 'Domain Category',
		'cattype'                   => [
			'I' => 'Individual Domain',
			'O' => 'Organization Domain'
		],
		'custom21_label'            => 'Registrant Document Type',

		'custom21' => [
			// Individual
			'HKID'        => 'Hong Kong Identity Number',
			'OTHID'       => 'Other\'s Country Identity Number',
			'PASSNO'      => 'Passport No.',
			'BIRTHCERT'   => 'Birth Certificate',
			'OTHIDV'      => 'Others Individual Document',

			// Organization
			'BR'          => 'Business Registration Certificate',
			'CI'          => 'Certificate of Incorporation',
			'CRS'         => 'Certificate of Registration of a School',
			'HKSARG'      => 'Hong Kong Special Administrative Region Government Department',
			'HKORDINANCE' => 'Ordinance of Hong Kong',
			'OTHORG'      => 'Others Organization Document',
		],

		'custom31_label' => 'Registrant Document Number',
		'custom41_label' => 'Registrant Document Origin Country',
		'custom51_label' => 'Registrant Age',
		'custom51'       => [
			'1' => 'Registrant is NOT under 18 years old',
			'0' => 'Registrant is under 18 years old',
		],
		'custom61_label' => 'Registrant Industry Type',
		'company1_label' => 'Registrant Company',
		'other1_label'   => 'Registrant Other Document Type',

		// CA
		'cira_legal_type_label' => 'Legal Type of Registrant Contact',
		'cira_legal_type' => [
			'CCO' => 'Corporation',
			'CCT' => 'Canadian Citizen',
			'RES' => 'Canadian Resident',
			'GOV' => 'Government Entity',
			'EDU' => 'Educational',
			'ASS' => 'Unincorporated Association',
			'HOP' => 'Hospital',
			'PRT' => 'Partnership',
			'TDM' => 'Trade-mark',
			'TRD' => 'Trade Union',
			'PLT' => 'Political Party',
			'LAM' => 'Library, Archive or Museum',
			'TRS' => 'Trust',
			'ABO' => 'Aboriginal Peoples',
			'INB' => 'Indian Band',
			'LGR' => 'Legal Representative',
			'OMK' => 'Official Mark',
			'MAJ' => 'The Queen',
		],

		'cira_legal_type_help_title' => 'Legal Type of Registrant Contact',
		'cira_legal_type_help_info' => '
			<ul>
				<li><strong>Corporation</strong>: A corporation under the laws of Canada or any province or territory of Canada, Charities, Cooperative, Council.
				<p><u>Requirements</u>: Registrant name must be the full legal name of the corporation and must be followed by the jurisdiction of incorporation (eg. Canada, Ontario, NWT...), unless it is obvious from the corporation&#39;s name. Canadian Internet Registration Authority (Canada). For Charities, the NFP should be included in the organization name. If this is a not-for-profit organization, you are also required to submit the registration number i.e.: 123456789 RR0001</p>
				</li>

				<li><strong>Canadian Citizen</strong>: A Canadian citizen of the age of majority under the laws of the province or territory in Canada in which he or she resides or last resided.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the full legal name of the Canadian Citizen who will hold the domain name registration as the name would appear on a passport, driver&#39;s license or other identification document issued by a government. The name can be followed by a space and &quot;o/a xxxx&quot; where &quot;o/a&quot; stands for &quot;Operating As&quot; and &quot;xxxx&quot; can be any alpha-numeric string designated by the applicant and subject to applicable rules and length restrictions (e.g. &quot;John Doe o/a Doe Consulting Group&quot;). The name may also be followed by a space and a degree granted to the registrant (eg. PhD). Full legal names of individuals may only consist of alphabetic characters and the special characters: single quote mark(&#39;), hyphen(-), period(.).</p>
				</li>

				<li><strong>Canadian Resident</strong>: A permanent resident as defined in the Immigration and Refugee Protection Act (Canada) S.C. 2001, c.27, as amended from time to time, who is &quot;ordinarily resident&quot; (as defined below) in Canada and of the age of majority under the laws of the province or territory in Canada in which he or she resides or last resided. &quot;Ordinarily resident in Canada&quot; means an individual who resides in Canada for more than 183 days in the twelve month period immediately preceding the date of the applicable request for registration of the .ca domain name or sub-domain name and in each twelve month period thereafter for the duration of the domain name registration.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the full legal name of the Permanent Resident who will hold the domain name registration as the name would appear on a driver&#39;s license or other identification document issued by a government. The full legal name may be followed by a space and &quot;o/a xxxx&quot; where &quot;o/a&quot; stands for &quot;Operating As&quot; and &quot;xxxx&quot; can be any alpha-numeric string designated by the applicant and subject to applicable rules and length restrictions (e.g. &quot;John Doe o/a Doe Consulting Group&quot;). The name may also be followed by a space and a degree granted to the registrant by a recognized degree granting institution or a recognized professional designation which the registrant has the right to use (eg. PhD, MD, DDS). Full legal names of individuals may only consist of alphabetic characters and the special characters: single quote mark(&#39;), hyphen(-), period(.).</p>
				</li>

				<li><strong>Government Entity</strong>: Her Majesty the Queen in right of Canada, a province or a territory; an agent of Her Majesty the Queen in right of Canada, of a province or of a territory; a federal, provincial or territorial Crown corporation, government agency or government entity; or a regional, municipal or local area government.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the entity that will hold the domain name registration, without any abbreviations. (A common abbreviation may follow the official name in parentheses). If the Registrant is not a government, the Registrant&#39;s name must be followed by the name of the jurisdiction (eg. Canada, province, territory, municipality, etc) to which the Registrant is related.</p>
				</li>

				<li><strong>Educational</strong>: (1) A university or college which is located in Canada and which is authorized or recognized as a university or college under an Act of the legislature of a province or territory of Canada; or (2) A college, post-secondary school, vocational school, secondary school, pre-school or other school or educational institution which is located in Canada and which is recognized by the educational authorities of a province or territory of Canada or licensed under or maintained by an Act of Parliament of Canada or of the legislature of a province or territory of Canada.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the institution that will hold the domain name registration, without any abbreviations. A common abbreviation may follow the official name in parentheses. The Registrant name must be followed by the jurisdiction (e.g. name of province, municipality) in which the institution is accredited if not obvious from the Registrant&#39;s name.</p>
				</li>

				<li><strong>Unincorporated Association</strong>: This Registrant Type is principally intended for religious congregations, social and sports clubs and community groups, council (not registered) or society (not registered) which are based and operating in Canada. An unincorporated organization, association or club: (1) At least 80% of whose members: (A) are ordinarily resident in Canada (if such members are individuals); or (B) meet the requirements of any one of the following Registrant Types: Corporation (Canada or Canadian province or territory), Canadian citizen, Permanent Resident of Canada, Partnership Registered in Canada, Trust established in Canada, Legal Representative of a Canadian Citizen or Permanent Resident; and (2) At least 80% of whose directors, officers, employees, managers, administrators or other representatives are ordinarily resident in Canada.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete name of the association that will hold the domain name registration, without any abbreviations. (A common abbreviation may follow the complete name in parentheses).</p>
				</li>

				<li><strong>Hospital</strong>: A hospital which is located in Canada and which is licensed, authorized or approved to operate as a hospital under an Act of the legislature of a province or territory of Canada
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the hospital that will hold the domain name registration, without any abbreviations. (A common abbreviation may follow the complete name in parentheses). The Registrant&#39;s name must be followed by the jurisdiction (eg. name of province) which accredited the hospital if not obvious from the Registrant&#39;s name.</p>
				</li>

				<li><strong>Partnership</strong>: A partnership, more than 66 2/3 percent of whose partners meet the requirements of one of the following Registrant Types: Corporation (Canada or Canadian province or territory), Canadian citizen, Permanent Resident of Canada, Trust established in Canada or a Legal Representative of a Canadian Citizen or Permanent Resident, which is registered as a partnership under the laws of any province or territory of Canada.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the registered name of the partnership that will hold the domain name registration. The Registrant name must be followed by the jurisdiction of registration (eg. Alberta) and the registration number.</p>
				</li>

				<li><strong>Trade-mark</strong>: A Person which does not fall under any other registrant type, but which is the owner of a trade-mark which is the subject of a registration under the Trade-marks Act (Canada) R.S.C. 1985, c.T-13 as amended from time to time, but in this case such permission is limited to a request to register a .ca domain name consisting of or including the exact word component of that registered trade-mark. This Registrant Type is only intended for Registrants which do not meet the requirements associated with any other registrant type but which have a trade-mark registered in Canada. (Trade-marks subject of trade-mark applications and trade-marks registered in other jurisdictions, such as the United States, do not qualify). The domain name to be registered must include the trade-mark. (eg. If the trade-mark is AVEA this type of registrant can register avea.ca or aveaisus.ca but not xyz.ca).</li>

				<li><strong>Trade Union</strong>: A trade union which is recognized by a labour board under the laws of Canada or any province or territory of Canada and which has its head office in Canada.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the trade union that will hold the domain name registration, without abbreviations. (A common abbreviation may follow the official name in parentheses). The Registrant name must be followed by the jurisdiction in Canada which recognizes it (if it is not obvious from the Registrant&#39;s name).</p>
				</li>

				<li><strong>Political Party</strong>: A political party registered under a relevant electoral law of Canada or any province or territory of Canada
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the political party holding the domain name registration, without abbreviations. (A common abbreviation may follow the official name in parentheses). The Registrant name must also by followed by the jurisdiction in Canada in which it is registered (if it is not obvious from the official name).</p>
				</li>

				<li><strong>Library, Archive or Museum</strong>: An institution, whether or not incorporated, that: (1) is located in Canada; and (2) is not established or conducted for profit or does not form part of, or is not administered or directly or indirectly controlled by, a body that is established or conducted for profit in which is held and maintained a collection of documents and other materials that is open to the public or to researchers.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete legal name of the institution which will hold the domain name registration without abbreviations. (A common abbreviation may follow the complete name in parentheses).</p>
				</li>

				<li><strong>Trust</strong>: A trust established and subsisting under the laws of a province or territory of Canada, more than 66 2/3 percent of whose trustees meet the requirements of one of the following Registrant Types: Corporation (Canada or Canadian province or territory), Canadian citizen, Permanent Resident of Canada, or a Legal Representative of a Canadian Citizen or Permanent Resident.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the trust, without any abbreviations. (A common abbreviation may follow the official name in parentheses). The Registrant name must also indicate the total percentage of the trustees that meet one or more of the following requirements: Canadian citizen, permanent resident, Canadian corporation, legal representative.
				</p>
				</li>

				<li><strong>Aboriginal Peoples</strong>: Any individual belonging to any Inuit, First Nation, Metis or other people indigenous to Canada.
				<p><u>Requirements</u>: The Registrant&#39;s name must be the full legal name of the Aboriginal Person applying for the Registration as it would appear on a driver&#39;s license or other identification document issued by government. The Registrant may enter its full legal name followed by a space and &quot;o/a xxxx&quot; where &quot;o/a&quot; stands for &quot;Operating As&quot; and &quot;xxxx&quot; can be any alpha-numeric string designated by the applicant and subject to applicable rules and length restrictions (e.g. &quot;John Doe o/a Doe Consulting Group&quot;). The name may also be followed by a space and a degree granted to the registrant by a recognized degree granting institution or a recognized professional designation which the registrant has the right to use (eg. PhD, MD, DDS). Full legal names of individuals may only consist of alphabetic characters and the special characters: single quote mark(&#39;&#39;), hyphen(-), period(.). The Registrant&#39;s name must be the complete official name of the indigenous people a collectivity of Aboriginal Persons or, if there is no official name, the name by which the collectivity is commonly known.</p>
				</li>

				<li><strong>Indian Band</strong>: Any Indian band as defined in the Indian Act, R.S.C. 1985, c. I-5, as amended from time to time, and any group of Indian bands.
				<p><u>Requirements</u>: The name of Registrant must be the Indian Band Name as registered with the Department of Indian and Northern Affairs, Canada.</p>
				</li>

				<li><strong>Legal Representative</strong>: An executor, administrator or other legal representative of a Person listed as a Canadian Citizen or Permanent Resident of Canada.
				<p><u>Note</u>: This registrant type is only available to a person or entity that has been appointed by legal process to represent an individual who is not competent to represent him or herself. It is not available to anyone who represents a Canadian or foreign corporation in any capacity.</p>
				<p><u>Requirements</u>: The Registrant&#39;s name must be the full legal name of the Canadian Citizen or Permanent Resident of Canada who is being represented as it would appear on a passport, driver&#39;s license or other government identification document. This must be followed by the full legal name and capacity of at least one of the official representatives. The representative should be identified as the administrative contact for these registrations.</p>
				</li>

				<li><strong>Official Mark</strong>: A Person which does not meet the requirements for any other Registrant Type, but which is a Person intended to be protected by Subsection 9(1) of the Trade-Marks Act (Canada) at whose request the Registrar of Trade-marks has published notice of adoption of any badge, crest, emblem, official mark or other mark pursuant to Subsection 9(1), but in this case such permission is limited to a request to register a .ca domain name consisting of or including the exact word component of such badge, crest, emblem, official mark or other mark in respect of which such Person requested publications.
				<p><u>Notes</u>: This registrant type is only intended for Registrants which do not meet the requirements associated with any other registrant type but which have an Official Mark registered in Canada. The domain name must include the official mark (eg. If the official mark is WIPO, the registrant can register wipo.ca but not intellectual-property.ca)</p>
				<p><u>Requirements</u>: The Registrant&#39;s name must be the complete official name of the entity holding the domain name registration without any abbreviations. (A common abbreviation may follow the complete name in parentheses.) The registration number of the official mark must follow the Registrant Name.</p>
				</li>

				<li><strong>The Queen</strong>: Her Majesty Queen Elizabeth the Second and her successors.
				<p><u>Requirements</u>: The Registrant&#39;s name must be that of Her Majesty the Queen or, after a succession, the name of her successor.</p>
				</li>
			</ul>
		',

		'cira_language_label' => 'Preferred Language for Communication',
		'cira_language' => [
			'en' => 'English',
			'fr' => 'French',
		],

		'cira_agreement_value_label' => 'CIRA Registrant Agreement',
		'cira_agreement_value' => [
			'Y' => 'I have read and agreed to the <a target="_blank" href="https://cira.ca/registrant-agreement">CIRA Registrant Agreement 2.1</a>',
		],

		// .CO.UK
		'uk_legal_type_label' => 'Legal Type of Registrant',
		'uk_legal_type' => [
			'IND' => 'UK Individual',
			'FIND' => 'Non-UK Individual (representing self)',
			'LTD' => 'UK Limited Company',
			'PLC' => 'UK Public Limited Company',
			'PTNR' => 'UK Partnership',
			'LLP' => 'UK LLP',
			'STRA' => 'UK Sole Trader',
			'RCHAR' => 'UK Registered Charity',
			'IP' => 'UK Industrial/Provident Registered Company',
			'SCH' => 'UK School',
			'FOTHER' => 'Non-UK Entity',
			'GOV' => 'UK Government Body',
			'CRC' => 'UK Corporation by Royal Charter',
			'STAT' => 'UK Statutory Body',
			'OTHER' => 'UK Entity (other)',
			'FCORP' => 'Non-UK Corporation'
		],
		'uk_reg_co_no_label' => 'Company Identification Number',
		'registered_for_label' => 'Registrant Name (Company or Person)',

		'help_msg' => [
			'custom31' => [
				'br_only_first_digits' => 'Please provide the first eight (8) digits of the certificate number',
			],
		],

		'validation_messages' => [
			'default'              => 'Invalid format', // This is shown by default if no translation is found
			'custom31_valid_hk_br' => 'The certificate number you have provided is invalid.',
		],
	],
	'uk_transfer_notice' => 'Once you place the order, you have to change the IPS Tag of your domain name to <strong>ENOM</strong> within the next 7 days. You can do this through the current registrar company for your domain name.',
	'hk_documents_notice' => 'The registration of the top-level domain (TLD) you have chosen requires <a href="https://www.hkirc.hk/en/our_services/domain_services/eligibility_required_documents/">additional documentation</a>, as per the regulations of the HKIRC. Please complete the "TLD-specific Data" section below. After you submit the form, you need to send to us scanned copies of the respective documents, by email to <a href="mailto:accountmanager@suresupport.com">accountmanager@suresupport.com</a>.',
	'headers' => [
		'home' => '<div id="products-carousel" class="carousel slide mb-4 border-bottom shadow-sm" data-ride="carousel" data-interval="0">
	<ol class="carousel-indicators text-dark">
		<li data-target="#products-carousel" data-slide-to="0" class="active bg-dark"></li>
		<li data-target="#products-carousel" data-slide-to="1" class="bg-dark"></li>
		<li data-target="#products-carousel" data-slide-to="2" class="bg-dark"></li>
		<li data-target="#products-carousel" data-slide-to="3" class="bg-dark"></li>
	</ol>
	<div class="carousel-inner bg-light" style="min-height: 280px">
		<div class="carousel-item active bg-light">
			<div class="container my-4 text-dark text-left">
				<h2>Shared Web Hosting</h2>
				<div class="row">
					<div class="col-md-4">
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Advanced Control Panel</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Let\'s Encrypt SSL Certificates</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Managed Email Service with Spam Protection</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Daily Backups</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> SSH Access</div>
					</div>
					<div class="col-md-8">
						<p class="text-justify text-bigger">Our shared web hosting plans are designed for small personal to medium business websites, offering performance and stability at a reasonable price.</p>
						<p><a href=":url_plans?location=centurylink#shared-centurylink" class="btn btn-primary" role="button">See the plans</a></p>
					</div>
				</div>
			</div>
		</div>
		<div class="carousel-item bg-light">
			<div class="container my-4 text-dark text-left">
				<h2>Node.js Hosting</h2>
				<div class="row">
					<div class="col-md-4">
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Node.js Out-Of-The-Box</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Dedicated RAM</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Supervisor Service</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Persistent Filesystem</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Django, Go and Ruby</div>
					</div>
					<div class="col-md-8">
						<p class="text-justify text-bigger">The WebApps plans allow you to deploy and run Node.js projects in a fully managed runtime environment. You just need to upload your project to the server and start it through our graphical interface (or the handy CLI tool). You can also manually install other runtime environments, such as Python/Django, Go, and Ruby. </p>
						<p><a href=":url_plans?location=centurylink#app-centurylink" class="btn btn-primary" role="button">See the plans</a></p>
					</div>
				</div>
			</div>
		</div>
		<div class="carousel-item bg-light">
			<div class="container my-4 text-dark text-left">
				<h2>Single site VPS</h2>
				<div class="row">
					<div class="col-md-4">
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Control Panel</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Guaranteed CPU and RAM Allocation</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Dedicated IP Addresses</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> PCI Compliance</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> RAID6 Enterprise SSD Storage</div>
					</div>
					<div class="col-md-8">
						<p class="text-justify text-bigger">The single site VPS plans are our heavy weight lifters for corporate and high load websites. They are the right choice when you need your site to perform at its best.</p>
						<p><a href=":url_plans?location=centurylink#vps-centurylink" class="btn btn-primary" role="button">See the plans</a></p>
					</div>
				</div>
			</div>
		</div>
		<div class="carousel-item bg-light">
			<div class="container my-4 text-dark text-left">
				<h2>Managed VPS</h2>
				<div class="row">
					<div class="col-md-4">
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Control Panel</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Unlimited Websites and Mailboxes</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Guaranteed CPU and RAM Allocation</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> Free Let\'s Encrypt SSL/HTTPS Certificates</div>
						<div class="mb-2"><i class="fas fa-check mr-3"></i> RAID6 Enterprise SSD Storage</div>
					</div>
					<div class="col-md-8">
						<p class="text-justify text-bigger">The managed VPS plans allow hosting an unlimited number of websites.</p>
						<p><a href=":url_plans?location=centurylink#server-centurylink" class="btn btn-primary" role="button">See the plans</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>',
		'plans' => '<div class="position-relative overflow-hidden p-4 m-3 text-center bg-light">
	<div class="col-md-5 mx-auto my-5">
		<h1 class="display-4 font-weight-normal">Order a hosting account</h1>
		<p class="lead font-weight-normal">Enjoy super fast website performance on our optimized servers.</p>
	</div>
</div>',
		'order' => '<div class="position-relative overflow-hidden p-4 m-3 text-center bg-light">
	<div class="col-md-5 mx-auto my-5">
		<h1 class="display-4 font-weight-normal">Order a hosting account</h1>
		<p class="lead font-weight-normal">Enjoy super fast website performance on our optimized servers.</p>
	</div>
</div>',
		'domains' => '<div class="position-relative overflow-hidden p-4 m-3 text-center bg-light">
	<div class="col-md-5 mx-auto my-5">
		<h1 class="display-4 font-weight-normal">Domain name search</h1>
		<p class="lead font-weight-normal">Register a domain name and secure your online presence.</p>
	</div>
</div>',
		'certificates' => '<div class="position-relative overflow-hidden p-4 m-3 text-left bg-light">
	<div class="col-md-5 mx-auto my-5">
		<h1 class="display-4 font-weight-normal">SSL Certificates</h1>
		<p class="lead font-weight-normal">Secure your site with a personal SSL certificate issued to your domain name. If you accept sensitive data from your site visitors, using SSL (HTTPS) is the right way to protect your customers/visitors data.</p>
	</div>
</div>',
	],


	'certificate' => [
		'certificates_not_offered' => 'SSL certificates are not offered at the moment.',
		'heading'                  => [
			'personal_information'    => 'Personal information',
			'ssl_certificate'         => 'SSL Certificate',
			'certificate_information' => 'Certificate Information (CSR)',
		],
		'label'                    => [
			'certificate'       => 'Certificate',
			'period'            => 'Period',
			'price'             => 'Price',
			'certificates_info' => 'Certificates information',
		],
		'ssl_info'                 => [
			'desc'  => [
				'geotrust' => 'GeoTrust® is the world\'s second largest digital certificate provider. More than 100,000 customers in over 150 countries trust GeoTrust to secure online transactions and conduct business over the Internet. Their digital certificate and trust products enable organizations of all sizes to maximize the security of their digital transactions cost-effectively.',
				'sectigo'  => 'Sectigo is one of the most recognized brands in online security, offering flexible and affordable certificate options that fit a wide range of needs.'
			],
			'label' => [
				'price_per_year'             => 'Price per year',
				'validation_type'            => 'Validation type',
				'domain_validation'          => 'Domain Validation',
				'warranty'                   => 'Warranty',
				'subdomains_secured'         => 'Subdomains secured',
				'single'                     => 'Single',
				'unlimited'                  => 'Unlimited',
				'issuance'                   => 'Issuance',
				'validity_options'           => 'Validity options',
				'10_min'                     => '10 minutes',
				'1_2_hours'                  => '1-2 hours',
				'1_2_days'                   => '1-2 days',
				'1_2_years'                  => '1 year',
				'5_days'                     => '5 days',
				'25_days'                    => '25 days',
				'site_seal'                  => 'Site seal',
				'static'                     => 'Static',
				'dynamic'                    => 'Dynamic',
				'level_of_encryption'        => 'Level of encryption',
				'browser_compatibility'      => '99% Browser compatibility',
				'up_to_256_bit'              => 'up to 256-bit',
				'browser_security_lock'      => 'Browser security lock',
				'idn_support'                => 'IDN support',
				'renewal_reminders'          => 'Renewal reminders',
				'renewal_benefits'           => 'Early renewal benefits',
				'revocation_and_replacement' => 'Revocation and replacement',
				'free'                       => 'Free',
				'free_refund'                => 'Free refund'
			],
			'tip'   => [
				'validation_type'            => 'Before a certificate authority issues an SSL Certificate, they validate the information provided in the certificate request. The more complete the validation, the more site visitors trust the certificate.',
				'domain_validation'          => 'Validates domain is registered and someone with admin rights is aware of and approves the certificate request.',
				'issuance'                   => 'The period needed for the issuance of the SSL certificate',
				'validity_options'           => 'The period for which a certificate can be purchased',
				'site_seal'                  => 'An option to have a seal on the site, showing your visitors that your site certificate is valid',
				'dynamic'                    => 'Dynamic Site Seal - Displays company name and date/time stamp.',
				'browser_security_lock'      => 'When the site is accessed over SSL, the browser will display a security lock showing the visitor that the site is accessed over a secure channel',
				'idn_support'                => 'Support for International Domain Names',
				'renewal_benefits'           => 'Renewing 46-90 days before expiration results in 3 months of bonus service, 16-45 days before expiration results in 2 bonus months of service and renewing 15 days prior expiration to 15 days after expiration will result in 1 month of bonus service.',
				'revocation_and_replacement' => 'Changing the details in the certificate signing request',
				'free_refund'                => 'Free revocation and refund of your SSL certificate'
			]
		],
	],
);
