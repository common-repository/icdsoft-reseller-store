<?php

namespace ICD\Hosting\Services;

/**
 * Class OrderStatusEnum
 *
 * @package ICD\Hosting\Services
 */
class OrderStatusEnum {
	const NOT_PAID = 'not_paid';
	const PARTIALLY_PAID = 'partially_paid';
	const FULLY_PAID = 'fully_paid';
	const ORDER_NEW = 'new';
}