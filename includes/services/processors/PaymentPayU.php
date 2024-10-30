<?php

namespace ICD\Hosting\Services\Processors;

/**
 * Class PaymentPayU
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentPayU extends PaymentProcessor {
	/**
	 * @var
	 */
	protected $merchantId;
	/**
	 * @var
	 */
	protected $accountId;
	/**
	 * @var
	 */
	protected $ApiKey;

	const TRANSACTION_OK = 4;
	const TRANSACTION_PENDING = 7;
	const TRANSACTION_ERROR = 104;
	const TRANSACTION_REJECTED = 6;

	/**
	 * PaymentPayU constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config, $config['test'] ? 'https://stg.gateway.payulatam.com/ppp-web-gateway' : 'https://gateway.payulatam.com/ppp-web-gateway' );

		if ( $config['test'] ) {
			$this->merchantId = '500238';
			$this->ApiKey     = '6u39nqhq8ftd0hlvnjfs66eh8c';
			$this->accountId  = '509171';
		} else {
			$this->merchantId = $config['options']['id'];
			$this->accountId  = $config['options']['accountId'];
			$this->ApiKey     = $config['options']['secret'];
		}
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
		//“ApiKey~merchantId~referenceCode~amount~currency”.
		$invoice_id  = str_pad( $this->request_id, 11, '0', STR_PAD_LEFT );
		$total       = number_format( ( $amount - $discount ), 2, '.', '' );
		$fingerprint = md5( $this->ApiKey . "~" . $this->merchantId . "~" . $invoice_id . "~" . $total . "~" . $currency );

		return array(
			'url'    => $this->url,
			'type'   => 'online',
			'params' => array(
				'merchantId'      => $this->merchantId,
				'accountId'       => $this->accountId,
				'referenceCode'   => $invoice_id,
				'amount'          => $total,
				'tax'             => 0,
				'taxReturnBase'   => 0,
				'currency'        => $currency,
				'description'     => icd_hosting_tr( 'hosting_order_due', array( 'order_id' => $order_id ) ),
				'signature'       => $fingerprint,
				'test'            => $this->config['test'] ? 1 : 0,
				'buyerEmail'      => $order['email'],
				'responseUrl'     => $this->returnUrl( array( 'order_id' => $order_id ) ),
				'confirmationUrl' => $this->postbackUrl(),
				'payerFullName'   => $order['firstname'] . ' ' . $order['lastname'],
				'billingAddress'  => $order['address'],
				'telephone'       => '(' . $order['phone_country_code'] . ')' . $order['phone'],
				'billingCity'     => $order['city'],
				'zipCode'         => $order['zip'],
				'billingCountry'  => $order['country'],
			),

		);
	}

	/**
	 * Verifies that the payment is valid
	 *
	 * @param $input
	 *
	 * @return array|bool
	 */
	public function validPayment( $input ) {
		if ( ! isset( $input['value'], $input['sign'], $input['reference_sale'], $input['currency'], $input['state_pol'] ) ) {
			return;
		}

		//"ApiKey~merchant_id~reference_sale~new_value~currency~state_pol"
		$new_value = number_format( $input['value'], 2, '.', '' );
		$reverse   = substr( strrev( $new_value ), 0, 1 );
		if ( $reverse == '0' ) {
			$new_value = number_format( $input['value'], 1, '.', '' );
		}

		$signature         = $this->ApiKey . '~' . $this->merchantId . '~' . $input['reference_sale'] . '~' . $new_value . '~' . $input['currency'] . '~' . $input['state_pol'];
		$signature         = md5( $signature );
		$transaction_state = $input['state_pol'];

		if ( strtoupper( $input['sign'] ) == strtoupper( $signature ) && ! empty( $input['reference_pol'] ) && ! empty( $input['value'] ) ) {

			return array_merge(
				$this->postback_data,
				array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'             => null,
					'payment_request_id'   => intval( $input['reference_sale'] ),
					'payment_type'         => $this->paymentType( $input ),
					'transaction_id'       => $input['reference_pol'],
					'payment_date'         => date( 'Y-m-d H:i:s T' ),
					'merchant_id'          => $this->merchantId,
					'total'                => $this->paymentTotal( $input ),
					'ipn'                  => $input,
					'payment_request_id'   => intval( $input['reference_sale'] ),
				)
			);
		}
	}

	/**
	 * Get payment type based on IPN payment status
	 *
	 * @param $ipn
	 *
	 * @return string
	 */
	protected function paymentType( $ipn ) {
		if ( $ipn['state_pol'] == self::TRANSACTION_OK ) {
			return 'incoming';
		}

		return 'error';
	}

	/**
	 * Total payment amount
	 *
	 * @param $ipn
	 *
	 * @return mixed
	 */
	protected function paymentTotal( $ipn ) {
		return $ipn['value'];
	}

}