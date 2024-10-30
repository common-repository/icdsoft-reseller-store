<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class Payment2CheckOut
 * @package ICD\Hosting\Services\Processors
 */
class Payment2CheckOut extends PaymentProcessor {

	/**
	 * @var
	 */
	protected $merchantId;
	/**
	 * @var
	 */
	protected $secret;

	/**
	 * Payment2CheckOut constructor.
	 *
	 * Initialize 2Checkout ID and Secret and sandbox gateway
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct($config, 'https://www.2checkout.com/checkout/purchase');

		$this->merchantId = $config['options']['id'];
		$this->secret     = $config['options']['secret'];
	}

	/**
	 * Payment method specific attributes
	 *
	 * @param $order_id
	 * @param $amount
	 * @param $currency
	 * @param array $order
	 * @param int $discount
	 *
	 * @return array
	 */
	public function paymentData( $order_id, $amount, $currency, $order = array(), $discount = 0 ) {

		$order_total = 0;
		$order_items = [];
		if ( $discount ) {
			$order_items = [
				[
					'name'     => tr( 'hosting_order_due', array( 'order_id' => $order_id ) ),
					'price'    => sprintf( "%01.2f", $amount - $discount ),
					'quantity' => 1,
				]
			];
		} else {
			foreach ( $order['items'] as $item ) {
				$price         = $this->exchangeAmount( $item['price'] + $item['price'] * $item['vat'] / 100 );
				$order_items[] = [
					'name'     => format_item_label( $item ),
					'price'    => sprintf( "%01.2f", $price ),
					'quantity' => 1,
				];
				$order_total   = bcadd( $order_total, $price, 2 );
			}

			if ( $order_total != $amount ) {
				$diff = $order_total - $amount;
				if ( $this->exchange_rate != 1 and abs( $diff ) < 1 ) {
					foreach ( $order_items as $k => $v ) {
						if ( $v['price'] - $diff > 0 ) {
							$order_items[ $k ]['price'] = $v['price'] - $diff;
							break;
						}
					}
				} else {
					$order_items = [
						[
							'name'     => tr( 'hosting_order_due', array( 'order_id' => $order_id ) ),
							'price'    => sprintf( "%01.2f", $amount ),
							'quantity' => 1,
						]
					];
				}
			}
		}

		$billing_contact = [
			'name'     => "{$order['firstname']} {$order['lastname']}",
			'address'  => $order['address'],
			'address2' => $order['address2'],
			'city'     => $order['city'],
			'state'    => $order['state'],
			'zip'      => $order['zip'],
			'country'  => $order['country'],
			'email'    => $order['email'],
			'phone'    => "{$order['phone_country_code']} {$order['phone']}",
		];

		$app_config = [
			'merchant'   => $this->merchantId,
			'iframeLoad' => 'checkout'
		];

		$return_method = [
			'type' => 'redirect',
			'url'  => $this->returnUrl( [ 'order_id' => $order_id ] )
		];

		$payment_ref = str_pad( $this->request_id, 11, '0', STR_PAD_LEFT );

		$signature = $this->getSignature( [
			'merchant'      => $this->merchantId,
			'dynamic'       => true,
			'currency'      => $currency,
			'reference'     => [
				'external' => [
					'order' => $payment_ref,
				],
			],
			'products'      => $order_items,
			'return-method' => $return_method,
			'test'          => $this->config['test'] ? true : false,
		] );

		$cancel_url      = $this->cancelUrl( array( 'order_id' => $order_id ) );
		$app_config      = wp_json_encode( $app_config );
		$billing_contact = wp_json_encode( $billing_contact );
		$order_items     = wp_json_encode( $order_items );
		$return_method   = wp_json_encode( $return_method );
		$test_mode       = $this->config['test'] ? true : false;
		$language        = config( 'locale', 'en' );
		if ( $language == 'hk' ) {
			$language = 'zh';
		}

		$code = <<<EOD
<script>
var script = document.createElement('script');
script.src = 'https://secure.avangate.com/checkout/client/twoCoInlineCart.js';
script.async = true;
script.onload = function() {
	window['TwoCoInlineCart'].register();
	TwoCoInlineCart.setup.setConfig('app', $app_config);
	TwoCoInlineCart.setup.setConfig('cart', {"host":"https:\/\/secure.2checkout.com","customization":"inline"});
	TwoCoInlineCart.setup.setMode('DYNAMIC');
	TwoCoInlineCart.cart.setReset(true);
	TwoCoInlineCart.cart.setCurrency('$currency');
	TwoCoInlineCart.cart.setLanguage('$language');
	TwoCoInlineCart.cart.setCartLockedFlag(true);
	//TwoCoInlineCart.cart.setAutoAdvance(true);
	var order_items = $order_items;
	for (a in order_items) {
		TwoCoInlineCart.products.add(order_items[a]);
	}\n
EOD;

		if (!empty($this->options['prefill_billing_info'])) {
			$code .= <<<EOD
	TwoCoInlineCart.billing.setData($billing_contact);\n
EOD;
		}

		$code .= <<<EOD
	TwoCoInlineCart.cart.setOrderExternalRef('$payment_ref');
	TwoCoInlineCart.cart.setReturnMethod($return_method);
	TwoCoInlineCart.cart.setSignature('$signature');
	TwoCoInlineCart.cart.setTest($test_mode);

	TwoCoInlineCart.events.subscribe('cart:opened', function () {
		resetPaymentForm();
	});
	TwoCoInlineCart.events.subscribe('cart:closed', function () {
		document.location.href = '$cancel_url';
	});
	TwoCoInlineCart.cart.checkout().catch(function(e) {
		resetPaymentForm();
		showMsg('error', 'payment_error.payment_declined');
	});
};
(document.getElementsByTagName('head')[0] || document.documentElement).appendChild(script);
</script>
EOD;


		return [ 'code' => $code ];
	}

	public function getSignature( $payload ) {
		if ( is_array( $payload ) ) {
			$payload = wp_json_encode( $payload );
		}

		$curl = curl_init();
		curl_setopt_array( $curl, [
			CURLOPT_URL            => 'https://secure.2checkout.com/checkout/api/encrypt/generate/signature',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_HTTPHEADER     => [
				'content-type: application/json',
				'merchant-token: ' . $this->jsonWebToken(),
			],
		] );

		$response = curl_exec( $curl );
		$err      = curl_error( $curl );
		curl_close( $curl );

		if ( ! $err ) {
			$response = json_decode( $response, true );

			return $response['signature'];
		}
	}

	public function jsonWebToken() {
		$header          = wp_json_encode( [ 'typ' => 'JWT', 'alg' => 'HS512' ] );
		$base64UrlHeader = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $header ) );

		$time             = time();
		$payload          = wp_json_encode( [ 'sub' => $this->merchantId, 'iat' => $time, 'exp' => $time + 36000 ] );
		$base64UrlPayload = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $payload ) );

		$signature          = hash_hmac( 'sha512', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret, true );
		$base64UrlSignature = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $signature ) );

		$jwt = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;

		return $jwt;
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function validPayment( $input ) {
		if ( isset( $input['key'] ) and isset( $input['order_number'] ) and isset( $input['total'] ) ) {
			$hash = strtoupper( md5( $this->secret . $this->merchantId . $input['order_number'] . $input['total'] ) );

			if ( $hash == $input['key'] ) {
				return array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'             => null,
					'payment_request_id'   => $input['merchant_order_id'],
					'payment_type'         => 'incoming',
					'payment_method'       => $this->paymentMethod(),
					'transaction_id'       => $input['order_number'],
					'payment_date'         => date( 'Y-m-d H:i:s T' ),
					'merchant_id'          => $this->merchantId,
					'total'                => $input['total'],
					'ipn'                  => $input,
				);
			}
		}

		if ( isset( $input['md5_hash'] ) and isset( $input['sale_id'] ) and isset( $input['invoice_id'] ) ) {
			$hash = strtoupper( md5( $input['sale_id'] . $this->merchantId . $input['invoice_id'] . $this->secret ) );

			if ( $hash == $input['md5_hash'] ) {
				return array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'             => null,
					'payment_request_id'   => $input['vendor_order_id'],
					'payment_type'         => $this->paymentType( $input ),
					'payment_method'       => $this->paymentMethod(),
					'transaction_id'       => $input['sale_id'],
					'payment_date'         => $input['timestamp'] . ' EST',
					'merchant_id'          => $this->merchantId,
					'total'                => $this->paymentTotal( $input ),
					'ipn'                  => $input,
				);
			}
		}
	}

	/**
	 * Get payment type
	 *
	 * @param $ipn
	 *
	 * @return string
	 */
	protected function paymentType( $ipn ) {
		if ( in_array( $ipn['message_type'], array( 'ORDER_CREATED' ) ) ) {
			return 'incoming';
		} else if ( in_array( $ipn['message_type'], array( 'REFUND_ISSUED' ) ) ) {
			return 'refund';
		}

		return 'error';
	}

	/**
	 * Total payment amount
	 *
	 * @param $ipn
	 *
	 * @return int
	 */
	protected function paymentTotal( $ipn ) {
		$payment_type = $this->paymentType( $ipn );
		if ( $payment_type == 'incoming' ) {
			return $ipn['invoice_list_amount'];
		}

		if ( $payment_type == 'refund' ) {
			$total = 0;
			for ( $i = 1; $i <= $ipn['item_count']; $i ++ ) {
				if ( $ipn["item_type_$i"] == 'refund' ) {
					$total -= $ipn["item_list_amount_$i"];
				}
			}

			return $total;
		}
	}
}