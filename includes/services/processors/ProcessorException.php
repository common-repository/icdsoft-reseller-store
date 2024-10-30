<?php

namespace ICD\Hosting\Services\Processors;

use Exception;

/**
 * Class ProcessorException
 *
 * @package ICD\Hosting\Services\Processors
 */
class ProcessorException extends Exception {
	/**
	 * ProcessorException constructor.
	 *
	 * @param $message
	 * @param $processor
	 */
	public function __construct( $message, $processor ) {
		parent::__construct( $message );

		$this->processor = $processor;
	}

	/**
	 * Return failed processor
	 *
	 * @return mixed
	 */
	public function getProcessor() {
		return $this->processor;
	}
}
