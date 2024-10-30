<?php

/**
 * Filter by keys
 *
 * @param $params
 * @param $keys
 *
 * @return array
 */
function icd_hosting_filter_keys( $params, $keys ) {
	return array_intersect_key( $params, array_flip( $keys ) );
}

/**
 * Generate full or relative url with parameters
 *
 * @param $name
 * @param array $params
 * @param bool $full
 *
 * @return false|string
 */
function icd_hosting_url( $name, $params = array(), $full = false ) {
	$url        = icd_hosting_get_page_permalink( $name );
	$parameters = array();
	foreach ( $params as $key => $param ) {
		$nextParam    = $full ? $key . '=' . $param : $param;
		$parameters[] = $nextParam;
	}

	$query = wp_parse_url($url, PHP_URL_QUERY);

	if ( $parameters ) {
		if ( $full ) {
			$url .= ($query ? '&' : '?') . implode( '&', $parameters );
		} else {
			$url = implode( '/', $parameters );
		}
	}

	return $url;
}

/**
 * Throw AppException error
 *
 * @param null $msg
 *
 * @throws \ICD\Hosting\Services\AppException
 */
function icd_hosting_error( $msg = null ) {
	throw new \ICD\Hosting\Services\AppException( $msg );
}

/**
 * Get translation of string
 *
 * @param $key
 * @param array $params
 *
 * @return mixed
 */
function icd_hosting_tr( $key, $params = array() ) {
	return \ICD\Hosting\ICD_Hosting()->getApp()->trans->tr( $key, $params );
}

/**
 * Translate string key upon choice (int)
 * @param $key
 * @param int $choice
 * @param array $params
 *
 * @return mixed
 */
function icd_hosting_tr_choice( $key, $choice = 1, $params = array() ) {
	return \ICD\Hosting\ICD_Hosting()->getApp()->trans->tr_choice( $key, $choice, $params );
}

/**
 * Translate key or return null if not found
 *
 * @param $key
 * @param null $default
 * @param array $params
 *
 * @return mixed
 */
function icd_hosting_tr_empty( $key, $default = null, $params = array() ) {
	return \ICD\Hosting\ICD_Hosting()->getApp()->trans->tr_empty( $key, $default, $params );
}

/**
 * Check if translation exist
 *
 * @param $key
 *
 * @return mixed
 */
function icd_hosting_tr_has( $key ) {
	return \ICD\Hosting\ICD_Hosting()->getApp()->trans->find_key( $key );
}

/**
 * Format product item label
 * @param $item
 *
 * @return string
 */
function icd_hosting_format_item_label( $item ) {
	$action = $item['product_type'] == 'domain' ? $item['domain']['action'] : $item['action'];
	$label  = $item['action'] == 'purchase' ?
		nl2br( $item['purchase']['items'] ) : icd_hosting_tr( "purchase_actions.{$item['product_type']}.$action" ) . ': ' . $item['item'];
	//if (!empty($item['hosting']['username']))
	//	$label = "$label ({$item['hosting']['username']}@{$item['hosting']['server']})";

	return $label;
}

/**
 * Load translations used by javascript
 *
 * @param string $section
 * @param array $params
 *
 * @return array
 */
function icd_hosting_js_lang( $section = 'order', $params = array() ) {
	$keys = array(
		'order'   => array( 'domain_search' ),
		'request' => array( 'domain_search', 'ssl' ),
		'payment' => array( 'stripe', 'error', 'payment_error' ),
	);

	$result = array();
	foreach ( $keys[ $section ] as $key ) {
		$result[ $key ] = isset( $params[ $key ] ) ? icd_hosting_tr( $key, $params[ $key ] ) : icd_hosting_tr( $key );
	}

	return $result;
}

/**
 * Parse domain name to top level domain and second level domain
 *
 * @param $domain
 *
 * @return array
 */
function icd_hosting_sld_tld( $domain ) {
	$domain = icd_hosting_valid_domain( $domain );
	if ( ! $domain ) {
		return array();
	}

	$pos = strpos( $domain, '.' );

	return array(
		'sld' => substr( $domain, 0, $pos ),
		'tld' => substr( $domain, $pos + 1 ),
	);
}

/**
 * Domain name validation
 * @param $domain
 *
 * @return string|string[]|null
 */
function icd_hosting_valid_domain( $domain ) {
	$domain = preg_replace( '/^(http(?:s)?:\/\/)?(www\.)?/', '', strtolower( trim( $domain ) ) );
	$result = preg_match( '/^([\p{L}0-9][\p{L}0-9.\-]{0,61}?[\p{L}0-9]*)((?:\.(?:[\p{L}]{2,15})){1,2})$/u', $domain );

	return $result ? $domain : '';
}

/**
 * Send mail to notification_email
 *
 * @param $subject
 * @param $body
 */
function icd_hosting_notification_email( $subject, $body ) {
	if ( empty( \ICD\Hosting\ICD_Hosting()->getApp()->settings['notification_email'] ) ) {
		return;
	}

	icd_hosting_mail_utf8( \ICD\Hosting\ICD_Hosting()->getApp()->settings['notification_email'], $subject, $body );
}

/**
 * Send mail using UTF-8 charset
 *
 * @param $to
 * @param $subject
 * @param $body
 * @param bool $html
 * @param string $from_email
 * @param string $from_name
 * @param string $reply_email
 */
function icd_hosting_mail_utf8( $to, $subject, $body, $html = false, $from_email = '', $from_name = '', $reply_email = '' ) {
	$subject = "=?utf-8?b?" . base64_encode( $subject ) . "?=";

	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/" . ( $html ? 'html' : 'plain' ) . ";charset=utf-8\r\n";

	if ( $from_email ) {
		$headers .= "From: =?utf-8?b?" . base64_encode( $from_name ) . "?= <$from_email>\r\n";
	}
	if ( $reply_email ) {
		$headers .= "Reply-To: $reply_email\r\n";
	}

	mail( $to, $subject, $body, $headers );
}

/**
 * Convert special characters to HTML entities
 *
 * @param $mixed
 * @param null $flags
 * @param null $encoding
 * @param bool $double_encode
 *
 * @return array|string
 */
function icd_hosting_ent( $mixed, $flags = null, $encoding = null, $double_encode = false ) {
	if ( is_null( $flags ) ) {
		$flags = ENT_COMPAT | ENT_HTML401;
	}

	if ( is_null( $encoding ) ) {
		$encoding = ini_get( 'default_charset' );
	}

	if ( is_array( $mixed ) ) {
		foreach ( $mixed as $k => $v ) {
			if ( is_array( $v ) ) {
				$mixed[ $k ] = icd_hosting_ent( $v );
			} else {
				$mixed[ $k ] = htmlspecialchars( $v, $flags, $encoding, $double_encode );
			}
		}

		return $mixed;
	}

	return htmlspecialchars( $mixed, $flags, $encoding, $double_encode );
}

/**
 * Format a local time/date
 *
 * @param $date
 * @param null $format
 *
 * @return false|string
 */
function icd_hosting_format_date( $date, $format = null ) {
	$format = is_null( $format ) ? 'F j, Y' : $format;

	return date( $format, strtotime( $date ) );
}

/**
 * Get end date based on parent end date
 *
 * @param $period
 * @param $parent_end_date
 * @param string $start_date
 *
 * @return false|string
 */
function icd_hosting_calculated_end_date( $period, $parent_end_date, $start_date = '' ) {
	if ( $start_date == '' ) {
		$start_date = date( 'Y-m-d H:i:s' );
	}

	$start_date_offset = icd_hosting_add_period( $start_date, 15, 'DAY' );
	if (
		strtotime( '2000-01-' . substr( $parent_end_date, 8 ) ) >= strtotime( '2000-01-' . substr( $start_date_offset, 8 ) ) and
		substr( $start_date_offset, 5, 2 ) == substr( $start_date, 5, 2 )
	) {
		$period = $period - 1;
	} else if (
		strtotime( '2000-01-' . substr( $parent_end_date, 8 ) ) < strtotime( '2000-01-' . substr( $start_date_offset, 8 ) ) and
		substr( $start_date_offset, 5, 2 ) > substr( $start_date, 5, 2 )
	) {
		$period = $period + 1;
	}

	return icd_hosting_add_period( substr( $start_date, 0, 8 ) . substr( $parent_end_date, 8 ), $period, 'MO' );
}

/**
 * Parse date periodicity
 *
 * @param string $date
 * @param int $period
 * @param string $periodicity
 * @param string $extra
 *
 * @return false|string
 */
function icd_hosting_add_period( $date = '', $period = 1, $periodicity = 'YR', $extra = '' ) {
	$map         = [ 'MO' => 'month', 'YR' => 'year' ];
	$periodicity = isset( $map[ $periodicity ] ) ? $map[ $periodicity ] : $periodicity;

	return date( 'Y-m-d H:i:s', strtotime( "$date +$period $periodicity $extra" ) );
}


/**
 * Sanitize data.
 * Non-scalar values are ignored.
 *
 * @param string|array $data Data to sanitize.
 * @return string|array
 */
function icd_hosting_sanitize_all( $data ) {
	return is_array( $data ) ? array_map( 'icd_hosting_sanitize_all', $data ) : ( is_scalar( $data ) ? sanitize_text_field( $data ) : $data );
}

/**
 * Addon price tip
 *
 * @param $months_left
 * @param $price
 *
 * @return mixed
 */
function icd_hosting_addon_price_tip($months_left, $price) {
	$tip = $months_left . ' ' . icd_hosting_tr_choice('periods.MO', $months_left) . '<br>' .
	       '1 ' . icd_hosting_tr('resources.addon_domain') . '<br>' .
	       $price['currency'] . ' ' . sprintf('%.2f', $price['price']) . ' /' . icd_hosting_tr('periodicity.MO');

	return icd_hosting_ent($tip);
}

/**
 * Get User IP Address
 * @return mixed|string
 */
function icd_hosting_get_user_ip() {
	$headers = array('X_FORWARDED_FOR', 'X_FORWARDED', 'X_CLUSTER_CLIENT_IP', 'CLIENT_IP');

	foreach ($headers as $header) {
		if (array_key_exists($header, $_SERVER) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
			return $_SERVER[$header];
		}
	}

	return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
}

/**
 * Generate random hash
 *
 * @param int $length
 *
 * @return string
 */
function icd_hosting_rand_hash($length = 8) {
	return substr(sha1(uniqid(time() . wp_rand(), true)), 0, $length);
}

/**
 * @param $data
 * @param $path
 * @param null $default
 *
 * @return array|mixed|null
 */
function icd_hosting_dot_path($data, $path, $default = null) {
	if (!is_object($data) and !is_array($data))
		return $default;

	foreach (explode('.', $path) as $key) {
		if (is_object($data) and !isset($data->$key) or is_array($data) and !isset($data[$key]))
			return $default;

		$data = is_array($data) ? $data[$key] : $data->$key;
	}

	return $data;
}

/**
 * @param $file
 * @param $log
 *
 * @return void
 */
function icd_hosting_custom_log($file, $log) {
	if (!empty($file)) {
		file_put_contents($file, gmdate('Y-m-d H:i:s') . " UTC\n" . "| $log\n\n", FILE_APPEND);
	}
}

/**
 * @param $array
 * @param $key
 * @param $default
 *
 * @return mixed|null
 */
function icd_hosting_array_get($array, $key, $default = null) {
	if (is_null($key))
		return $array;

	if (isset($array[$key]))
		return $array[$key];

	$found = false;
	$sub = array();
	$split = explode('.', $key);

	foreach ($split as $k => $v) {
		$sub[] = $v;
		unset($split[$k]);
		if (isset($array[implode('.', $sub)])) {
			$found = true;
			break;
		}
	}

	if (!$found)
		return $default;

	$new_array = $array[implode('.', $sub)];
	$new_key = $split ? implode('.', $split) : null;
	return array_get($new_array, $new_key, $default);
}