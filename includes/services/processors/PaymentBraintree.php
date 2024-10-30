<?php
namespace ICD\Hosting\Services\Processors;

use ICD\Hosting\ICD_Hosting;
use ICD\Hosting\Services\AppException;

class PaymentBraintree extends PaymentProcessor {

	protected  $merchantId;
	protected  $accountId;
	protected  $privateKey;
	protected  $publicKey;
	protected  $environment;

	public function __construct($config) {
		parent::__construct($config);

		$this->merchantId = $config['options']['id'];
		$this->accountId = $config['options']['merchant_account_id'];
		$this->privateKey = $config['options']['private_key'];
		$this->publicKey = $config['options']['public_key'];
		$this->environment = $config['test'] ? 'sandbox' : 'production';

		ICD_Hosting::instance()->load_braintree_lib();
	}

	public function makePayment($order_id, $amount, $currency, $order = array(), $input = array()) {
		try {
			$gateway = new \Braintree\Gateway([
				'environment' => $this->environment,
				'merchantId' => $this->merchantId,
				'publicKey' => $this->publicKey,
				'privateKey' => $this->privateKey
			]);
			$merchantAccountIterator = $gateway->merchantAccount()->all();

			$found = false;

			foreach ($merchantAccountIterator as $merchantAccount) {
				if ($merchantAccount->default) {
					$defaultAccount = $merchantAccount;
				}

				if ($merchantAccount->id == $this->accountId and $merchantAccount->currencyIsoCode == $currency) {
					$found = true;
				}
			}

			if (!$found and $defaultAccount->currencyIsoCode != $currency) {
				throw new \Exception('payment_error.payment_failed');
			}

			$nonce = $input['braintree_payment_method_nonce_' . $input['payment_method']];

			$result = $gateway->transaction()->sale([
				'amount' => $amount,
				'orderId' => str_pad($this->request_id, 11, '0', STR_PAD_LEFT),
				'merchantAccountId' => $this->accountId,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'submitForSettlement' => true
				],
				'customer' => [
					'firstName' => $order['firstname'],
					'lastName' => $order['lastname'],
					'company' => $order['company'],
					'phone' => '(' . str_ireplace( '+', '', $order['phone_country_code'] ) . ')' . $order['phone'],
					'email' => $order['email']
				],
			]);

			if ($result->success) {
				$transaction = $result->transaction;
				return array(
					'payment_processor_id' => $this->config['processor_id'],
					'order_id'             => $order_id,
					'payment_method'       => $this->paymentMethod(),
					'transaction_id'       => $transaction->id,
					'payment_date'         => $transaction->createdAt->format('Y-m-d H:i:s T'),
					'merchant_id'          => $this->merchantId,
					'ipn'                  => $transaction
				);
			}
			else {
				throw new \Exception('payment_error.payment_declined');
			}
		}
		catch (\Exception $e) {
			$this->handleException($e);
		}
	}

	public function detectPayment($input)
	{
		if (
			isset($input["bt_signature"]) &&
			isset($input["bt_payload"])
		) {
			$merchant_id = $this->config['options']['id'];
			$private_key = $this->config['options']['private_key'];
			$public_key = $this->config['options']['public_key'];
			$environment = $this->config['test'] ? 'sandbox' : 'production';
			$result = [];

			$gateway = new \Braintree\Gateway([
				'environment' => $environment,
				'merchantId' => $merchant_id,
				'publicKey' => $public_key,
				'privateKey' => $private_key
			]);

			try {
				$webhook_notification = $gateway->webhookNotification()->parse(
					$input["bt_signature"], $input["bt_payload"]
				);
			} catch ( \Exception $e ) {
				return [];
			}

			$webhook_notification = $webhook_notification->jsonSerialize();
			$transaction = isset($webhook_notification['transaction']) ? $webhook_notification['transaction'] : [];

			if (!empty($transaction)) {
				try {
					$transaction_find = $gateway->transaction()->find($transaction->id)->jsonSerialize();
				} catch (\Braintree\Exception\NotFound $exception) {
					return [];
				}

				$transaction = $transaction_find;
				$this->postback_response = 'OK';

				$result =  array(
					'payment_processor_id' => $this->config['processor_id'],
					'payment_request_id' => $transaction['orderId'],
					'payment_type' => $this->paymentType($transaction),
					'payment_method' => $this->paymentMethod(),
					'transaction_id' => $transaction['id'],
					'payment_date' => $webhook_notification['timestamp']->format('Y-m-d H:i:s T'),
					'merchant_id' => $this->config['options']['id'], //$this->merchantId,
					'total' => $this->paymentTotal($transaction),
					'ipn' => $input,
				);
			}

			return $result;
		}
	}

	public function paymentType($transaction) {
		if ($transaction['type'] == 'sale' and $transaction['status'] == 'submitted_for_settlement') {
			return 'incoming';
		}

		if ($transaction['type'] == 'credit' and $transaction['status'] == 'settled') {
			return 'refund';
		}

		if ($transaction['status'] == 'settlement_declined') {
			return 'chargeback';
		}

		return 'error';
	}

	protected function paymentTotal($transaction) {
		return $transaction['amount'];
	}

	public function handleException($e) {
		$msg = $e->getMessage();
		$msg = strpos($msg, 'payment_error.') !== false ? $msg : 'payment_error.payment_failed';

		if (class_exists('AppException')) {
			throw new AppException($msg);
		} else {
			throw new \Exception($msg);
		}
	}
}
