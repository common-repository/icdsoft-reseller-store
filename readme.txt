=== ICDSoft Reseller Store ===
Contributors: icdsoft, madjarov
Tags: web hosting, domain, reseller, shortcode, products, ecommerce, store
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 2.4.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Start reselling web hosting services, domains and SSL Certificates on your website. Create your own web hosting company.

== Description ==
Start selling web hosting services online. This plugin allows you to start reselling web hosting services with a few clicks.

You can set your own prices, preferred currencies and have a fully automated web hosting store solution backed up by the experience and reliability of ICDSoft.

The product catalog includes shared web hosting plans as well as high performance managed VPS hosting solutions on Linux servers, domain name registrations, SSL certificates, and various hosting add-ons.

= Features =
Easily place hosting and domain order forms on your website by adding shortcodes to your WordPress pages/posts.
Add hosting plans feature and comparison tables on your pages.

See more at [ICDSoft Partner Program](https://www.icdsoft.com/en/reseller).

= Languages Supported =
English

= Support =

If you need help using the plugin, please send us an email to support@icdsoft.com, or submit a support ticket via the ICDSoft Account Panel -> 24/7 Support, and we will do our best to assist you. We respond to enquiries within 15 minutes.

== Installation ==

= Minimum Requirements =

* WordPress 4.8 or greater
* PHP version 5.4 or greater
* MySQL version 5.0 or greater

= We recommend your host supports: =

* PHP version 7.0 or greater
* MySQL version 5.6 or greater
* WordPress Memory limit of 64 MB or greater (128 MB or higher is preferred)

= Installation =

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to ICDSoft Hosting > Settings.
4. Press 'Free Sign-up' or 'I have an account' button.



== Frequently Asked Questions ==

= How do I place order forms on my website? =

Once you have installed and activated the plugin, you can add order forms on your website by adding shortcodes directly in your WordPress posts/pages.

= What shortcodes are supported? =

The following shortcodes are supported:

**Hosting Order Form**

[hostingorder]

The hostingorder shortcode places a hosting order form on your post/page. It allows customers to order hosting plans from your online store. It also allows customers to register a domain (along with the hosting purchase) from one of the supported TLDs that we offer registration services for.

**Domain Search Form**

[domaincheck]

The domaincheck shortcode places a domain check form on your post/page. It allows visitors to check the availability of domains from the TLDs that we offer registration services for.

**SSL Certificate Order Form**

[certificates]

Support for SSL certificate orders has arrived. The certificates shortcode places an SSL certificate order form on your post/page. It allows visitors to order commercial SSL certificates from the list of certificates we offer.

**Plan info block**

[plan_info plan=&#34;Product Name&#34; location=&#34;Data Center&#34;]

The plan_info shortcode can be used to display the main features of a given hosting product. **plan** can take one of the following values - economy, business, business-plus, economy-sureapp, business-sureapp, firstclass, firstclass-startup, firstclass-ultimate, multivps, multivps-pro. **location** can take one of the following values - eu, us, hk.

***Example:*** The following shortcode will display a table with the characteristics of the Economy plan at the Europe data center

[plan_info plan=&#34;economy&#34; location=&#34;eu&#34;]

**Plan compare block**

[compare_plans location=&#34;Data Center&#34;]

The compare_plans shortcode can be used to show a comparison table of the hosting plans in a given datacenter. **location** can take one of the following values - eu, us, hk. The number of plans displayed per row is configured from the Settings menu of the plugin. The default value is 3.

***Example:*** The following shortcode will show a comparison table of the hosting plans at the USA data center

[compare_plans location=&#34;us&#34;]

***Note:***
When placing shortcodes, please make sure that you use the correct quotes (double quotes &#34;) for their parameters. You can use the "Paste as plain text" feature (ctrl + shift + v for Windows, command + shift + v for Mac) to paste the shortcode as plain text in the page/post editor.

= How do I create an online store? =

The online store would be created and connected automatically (with default settings) for users that sign up through the Welcome screen shown upon activating the plugin. Existing resellers can create and configure an online store through the [ICDSoft Account Panel](https://reseller.icdsoft.com/) -> Online Stores -> Management.

= How do I connect the plugin to my online store? =

Existing resellers that have manually created their online store would have to connect to it by entering its Authentication Key and HMAC Secret at the plugin's Settings menu. You can obtain the Authentication Key and HMAC Secret for your online store through the ICDSoft Account Panel -> Online Stores -> Management -> Edit (for the store in question) -> Main Settings -> API settings.

The online store would be connected automatically for users that sign up through the Welcome screen shown upon activating the plugin.

= What payment processors are supported? =

To be able to accept payments, you need to have an active business/merchant account with at least one of the following payment processors: PayPal, 2Checkout, ePay.bg, Authorize.Net, Stripe, PayU.com (South America), PayDollar.

= What currencies are supported in the online store? =

The supported currencies are - USD, EUR, HKD, BGN, CAD, AUD.

== Changelog ==
= 2.4.5 =
* Fix problem with some PreLoaders

= 2.4.4 =
* Update translations

= 2.4.3 =
* Fix inline PayPal payment button

= 2.4.2 =
* Fix plan_info, compare_plans order button shortcode translations

= 2.4.1 =
* Fix plan_info shortcode price translations

= 2.4.0 =
* Fix compare_plans shortcode translations

= 2.3.9 =
* Fix missing PayPal template

= 2.3.8 =
* New PayPal payment flow integration

= 2.3.7 =
* Enable API cache

= 2.3.6 =
* Add .eu email notice translation

= 2.3.5 =
* Fix request payment redirect
* Fix cancel url for payment processors

= 2.3.4 =
* Fix certificate page translation params
* Tweak reduce api calls

= 2.3.3 =
* Fix custom translation path
* Fix pretty links for pagename-locale slug
* New option to ignore pretty links

= 2.3.2 =
* Fix php warnings in certificate views

= 2.3.1 =
* Fix Undefined refill form data

= 2.3.0 =
* NEW Added а ssl order form

= 2.2.0 =
* New Add show monthly prices option

= 2.1.3 =
* Fix PHP8 issue with registration call
* Fix Requests - missing tooltip label

= 2.1.2 =
* Fix client ip header

= 2.1.0 =
* Fix 2CheckOut payment method
* Fix AuthorizeNet payment method
* New translations

= 2.0.1 =
* Fix missing prefix in AuthorizeNet payment processor

= 2.0.0 =
* New show/hide renewal price option
* New addon domains
* New .co.uk domains
* New vendor updates
* New Stripe, Braintree upgrade
* New 3D secure payments
* New VAT support in payment processors
* Fix CSS compatibility with new theme
* Fix PHP8 compatibility
* Fix payment requests
* Fix domain extra attributes
* Tweak order widget plans by group improvement

= 1.1.5 =
* Fix rewrite rules for subdirectories

= 1.1.4 =
* Fix - more short tags

= 1.1.3. =
* Fix - remove shorttags
* Fix - ajax error response

= 1.1.2 =
* Fix - widget version parameter to avoid sending outdated notifications
* New - send user agent/language headers
* New - translations

= 1.1.1 =
* Fix - stripe multiple instances
* Fix - missing translation

= 1.1.0 =
* New - Braintree payment method integration
* New - .bg domains registrations
* Fix - Payment request template view
* Fix - Readme new plans text

= 1.0.8 =
* Fix - payment request template
* Fix - remove conflicting debug function

= 1.0.7 =
* New - Support multivps plans
* New - SmartVps domain order as extra domain
* Fix - Hostname validation

= 1.0.6 =
* New - Payment request advanced security product
* Fix - Fix compare plans view
* Fix - Fix tld extra attributes form
* Fix - Fix add new ip in payment request
* Fix - Add missing translations
* Fix - Fix AuthorizeNet
* Fix - Fix domain search uppercase
* Fix - Remove page header container from templates

= 1.0.5 =
* Fix - Fix payment date format
* Fix - Fix item periods
* Fix - Fix translations
* Fix - Fix epp modal view

= 1.0.4 =
* Fix - Fix URL endpoints
* Fix - Shortcode css fix - compare_plans, plan_info

= 1.0.3 =
* Fix - Fix payment processors

= 1.0.2 =
* New - Add new shortcode location parameters
* Fix - Fix plan_info breaking tag issues
* Fix - Fix payment request redirect url
* Tweak - Domain search improvements
* Tweak - Admin settings default value
* Translations - Add new registration error translations

= 1.0.1 =
* Fix - Fix upgrade plan text
* Fix - Fix translation strings
* Fix - Fix url parameters
* Fix - Fix admin panel texts and titles
* Fix - Fix compare_plans warning when wrong shortcode attribute is passed
* Fix - Fix templates

= 1.0 =
* Initial release.
