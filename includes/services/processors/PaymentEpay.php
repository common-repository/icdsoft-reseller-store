<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentEpay
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentEpay extends PaymentProcessor {
	/**
	 * @var
	 */
	protected $merchantId;
	/**
	 * @var
	 */
	protected $secret;
	/**
	 * @var string
	 */
	protected $invoice_prefix;
	/**
	 * @var int
	 */
	protected $invoice_prefix_len = 3;
	/**
	 * max len epay.bg accept is 64 chars
	 *
	 * @var int
	 */
	protected $invoice_len = 14;

	/**
	 * PaymentEpay constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://demo.epay.bg/' : 'https://www.epay.bg/' );

		$this->merchantId = $config['options']['id'];
		$this->secret     = $config['options']['secret'];

		if ( empty( $config['options']['invoice_prefix'] ) ) {
			$config['options']['invoice_prefix'] = '';
		}
		if ( ! empty( $config['options']['invoice_len'] ) ) {
			$this->invoice_len = (int) $config['options']['invoice_len'];
		}
		if ( ! empty( $config['options']['invoice_prefix_len'] ) ) {
			$this->invoice_prefix_len = (int) $config['options']['invoice_prefix_len'];
		}
		$this->invoice_prefix = str_pad( $config['options']['invoice_prefix'], $this->invoice_prefix_len, '0' );

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
		$secret      = $this->secret;
		$min         = $this->merchantId;
		$invoice     = $this->buildInvoiceId( $this->request_id );
		$sum         = round( $amount, 2 );
		$exp_date    = date( "d.m.Y", strtotime( "+1 week" ) );
		$description = icd_hosting_tr( 'hosting_order_due', array( 'order_id' => $order_id ) );
		$data        = "MIN={$min}\nINVOICE={$invoice}\nAMOUNT={$sum}\nCURRENCY={$currency}\nEXP_TIME={$exp_date}\nDESCR={$description}\nENCODING=utf-8";
		$ENCODED     = base64_encode( $data );
		$CHECKSUM    = $this->hmac( 'sha1', $ENCODED, $secret );

		return array(
			'url'    => $this->url,
			'type'   => 'online',
			'params' => array(
				'page'       => 'paylogin', // credit_paydirect
				'encoded'    => $ENCODED,
				'checksum'   => $CHECKSUM,
				'url_ok'     => $this->returnUrl( array( 'order_id' => $order_id ) ),
				'url_cancel' => $this->cancelUrl(),
			)

		);
	}

	/**
	 * Generate HMAC
	 *
	 * @param $algo
	 * @param $data
	 * @param $passwd
	 *
	 * @return mixed
	 */
	protected function hmac( $algo, $data, $passwd ) {
		/* md5 and sha1 only */
		$algo = strtolower( $algo );
		$p    = array( 'md5' => 'H32', 'sha1' => 'H40' );
		if ( strlen( $passwd ) > 64 ) {
			$passwd = pack( $p[ $algo ], $algo( $passwd ) );
		}
		if ( strlen( $passwd ) < 64 ) {
			$passwd = str_pad( $passwd, 64, chr( 0 ) );
		}

		$ipad = substr( $passwd, 0, 64 ) ^ str_repeat( chr( 0x36 ), 64 );
		$opad = substr( $passwd, 0, 64 ) ^ str_repeat( chr( 0x5C ), 64 );

		return ( $algo( $opad . pack( $p[ $algo ], $algo( $ipad . $data ) ) ) );
	}

	/**
	 * Generate invoice ID
	 *
	 * @param $request_id
	 *
	 * @return string
	 */
	protected function buildInvoiceId( $request_id ) {
		$len = $this->invoice_len - strlen( $this->invoice_prefix );

		return $this->invoice_prefix . str_pad( $this->request_id, $len, '0', STR_PAD_LEFT );
	}

	/**
	 * Parse invoice ID
	 *
	 * @param $invoice
	 *
	 * @return int
	 */
	protected function parseInvoiceId( $invoice ) {
		return intval( substr( $invoice, strlen( $this->invoice_prefix ) ) );
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function validPayment( $input ) {
		if ( empty( $input['encoded'] ) || empty( $input['checksum'] ) ) {
			return;
		} else {
			$payment_type  = 'error';
			$ENCODED       = $input['encoded'];
			$CHECKSUM      = $input['checksum'];
			$CHECKSUM_CALC = $this->hmac( 'sha1', $ENCODED, $this->secret );
			if ( $CHECKSUM_CALC == $CHECKSUM ) {
				$data      = base64_decode( $ENCODED );
				$lines_arr = explode( "\n", $data );
				$info_data = '';
				$input     = array();

				foreach ( $lines_arr as $line ) {
					if ( preg_match( "/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs ) ) {
						parse_str( str_replace( ':', '&', $line ), $input );
						$input   = array_change_key_case( $input );
						$invoice = $input['invoice'];
						$status  = $input['status'];
						$payDate = $input['pay_time'];
						$stan    = $input['stan'];
						$bcode   = $input['bcode'];
					}
				}

				$date = substr( $payDate, 0, 4 ) . '-' . substr( $payDate, 4, 2 ) . '-' . substr( $payDate, 6, 2 ) . ' ' . substr( $payDate, 8, 2 ) . ':' . substr( $payDate, 10, 2 ) . ':' . substr( $payDate, 12, 2 ) . ' EET';

				if ( $status == "PAID" ) {
					$info_data    = "INVOICE=$invoice:STATUS=OK\n";
					$payment_type = 'incoming';
				} else {
					$info_data = "INVOICE={$invoice}:STATUS=ERR\n";
					$payment_type = 'error';
				}

				$this->postback_response = $info_data;

			} else {
				return;
			}
		}

		if ( empty( $payment_type ) ) {
			return;
		}

		return array_merge(
			$this->postback_data,
			array(
				'payment_processor_id' => $this->config['processor_id'],
				'order_id'             => null,
				'payment_request_id'   => $this->parseInvoiceId( $invoice ),
				'payment_type'         => $payment_type,
				'transaction_id'       => $invoice,
				'payment_date'         => $date,
				'merchant_id'          => $this->merchantId,
				'total'                => $this->paymentTotal( $input ),
				'ipn'                  => $lines_arr,
			)
		);
	}

	/**
	 * Epay does not provide payment total
	 *
	 * @param $ipn
	 */
	protected function paymentTotal( $ipn ) {
		return;
	}

}