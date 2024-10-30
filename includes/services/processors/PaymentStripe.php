<?php

namespace ICD\Hosting\Services\Processors;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\Services\AppException;

/**
 * Class PaymentStripe
 *
 * @package ICD\Hosting\Services\Processors
 */
class PaymentStripe extends PaymentProcessor {

	public function requirePaymentToken() {
		return true;
	}

	/**
	 * @param $details
	 *
	 * @return array
	 * @throws ProcessorException
	 */
	public function createPaymentToken($details) {
		try {
			\Stripe\Stripe::setApiKey( $this->config['options']['secret_key'] );

			$payment_intent = \Stripe\PaymentIntent::create( [
				'amount'        => intval( $details['amount'] * 100 ),
				'currency'      => strtolower( $details['currency'] ),
				'description'   => $this->orderLabel( $details['order_id'] ),
				'receipt_email' => $details['email'],
				'metadata'      => [
					'order_id'           => $details['order_id'],
					'payment_request_id' => array_get( $details, 'payment_request_id' ),
				],
			] );

			return [
				'payment_processor_id' => $this->config['processor_id'],
				'order_id'             => $details['order_id'],
				'payment_request_id'   => array_get( $details, 'payment_request_id' ),
				'amount'               => $details['amount'],
				'currency'             => $details['currency'],
				'token_id'             => $payment_intent->client_secret
			];
		} catch ( Exception $e ) {
			$this->handleException( $e );
		}
	}

	public function detectPayment( $details ) {
		try {
			if ( ! empty( $details['paymentIntent']['id'] ) and
			     ! empty( $details['paymentIntent']['status'] ) and $details['paymentIntent']['status'] == 'succeeded' ) {

				\Stripe\Stripe::setApiKey( $this->config['options']['secret_key'] );
				$intent = \Stripe\PaymentIntent::retrieve( $details['paymentIntent']['id'] );
				if ( $intent and $intent->status == 'succeeded' ) {
					$charge = $intent->charges->first();
					if ( $charge and $charge->status == 'succeeded' ) {
						return [
							'payment_processor_id' => $this->config['processor_id'],
							'payment_request_id'   => $charge->metadata->payment_request_id,
							'payment_type'         => 'incoming',
							'payment_method'       => $this->paymentMethod(),
							'transaction_id'       => $charge->id,
							'payment_date'         => date( 'Y-m-d H:i:s T', $charge->created ),
							'merchant_id'          => $this->config['options']['id'],
							'total'                => $charge->amount / 100,
						];
					}
				}
			}

			if ( ! empty( $details['data']['object']['id'] ) and
			     ! empty( $details['data']['object']['object'] ) and $details['data']['object']['object'] == 'charge' ) {

				\Stripe\Stripe::setApiKey( $this->config['options']['secret_key'] );
				$charge = \Stripe\Charge::retrieve( $details['data']['object']['id'] );
				if ( $charge and $charge->status == 'succeeded' ) {
					return [
						'payment_processor_id' => $this->config['processor_id'],
						'payment_request_id'   => $charge->metadata->payment_request_id,
						'payment_type'         => $this->paymentType($charge),
						'payment_method'       => $this->paymentMethod(),
						'transaction_id'       => $charge->id,
						'payment_date'         => date( 'Y-m-d H:i:s T', $charge->created ),
						'merchant_id'          => $this->config['options']['id'],
						'total'                => $this->totalAmount($charge),
					];
				}
			}
		} catch ( \Exception $e ) {
		}
	}

	public function paymentType($charge) {
		if ($charge->refunded)
			return 'refund';

		return 'incomming';
	}

	public function totalAmount($charge) {
		if ($charge->refunded)
			return $charge->refunds->first()->amount / 100;

		return $charge->amount / 100;
	}

	/**
	 * Make payment using stripe library
	 *
	 * @param $order_id
	 * @param $amount
	 * @param $currency
	 * @param array $order
	 * @param array $input
	 *
	 * @return array
	 * @throws AppException
	 * @throws ProcessorException
	 */
	public function makePayment( $order_id, $amount, $currency, $order = array(), $input = array() ) {
		try {
			$secret_key = $this->config['options']['secret_key'];
			$params     = array(
				'amount'        => bcmul( (string) $amount, '100' ),
				'currency'      => $currency,
				'source'        => ! empty( $input['stripeToken'] ) ? $input['stripeToken'] : '',
				'description'   => icd_hosting_tr( 'hosting_order_due', array( 'order_id' => $order_id ) ),
				'receipt_email' => $order['email'],
			);

			ICD_Hosting::instance()->load_stripe_lib();

			\Stripe\Stripe::setApiKey( $secret_key );

			$charge = \Stripe\Charge::create( $params );

			$last_response = $charge->getLastResponse()->json; // <-- The name is misleading, this is actually an array
			unset( $last_response['source'] );

			$result = array(
				'payment_processor_id' => $this->config['processor_id'],
				'order_id'             => $order_id,
				'payment_request_id'   => null,
				'payment_method'       => $this->paymentMethod(),
				'transaction_id'       => $charge->id,
				'payment_date'         => gmdate( DATE_ATOM, $charge->created ),
				// <-- Stripe uses Unix timestamps, but 'payment-add' action expects string representation
				'merchant_id'          => $this->config['options']['id'],
				'ipn'                  => $last_response
				// <-- This is not a real IPN but the response from the charge action
			);

			return $result;
		} catch (\Exception $e) {
				$this->handleException($e);
		}
	}

	/**
	 * Build error key
	 *
	 * @param $e
	 *
	 * @return string
	 */
	private function buildErrKey( $e ) {
		if ( ! $e instanceof \Stripe\Error\Base ) {
			return 'error.generic';
		}

		$body = $e->getJsonBody();
		$err  = $body['error'];

		return "stripe.{$err['type']}.{$err['code']}";
	}

	/**
	 * Handle errors
	 *
	 * @param $e
	 *
	 * @throws ProcessorException
	 */
	private function handleException( $e ) {
		$msg = 'stripe.error.processing_payment';
		if ($e instanceof \Stripe\Error\Card) {
			$body = $e->getJsonBody();
			$msg = "stripe.{$body['error']['type']}.{$body['error']['code']}";
		}

		if (class_exists('AppException')) {
			throw new AppException($msg);
		} else {
			throw new \Exception($msg);
		}
	}
}
