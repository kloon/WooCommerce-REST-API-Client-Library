<?php
/**
 * WC API Client Exception class
 *
 * Provides a simple wrapper around Exception to help with error handling
 *
 * @since 2.0
 */
class WC_API_Client_Exception extends Exception {


	/**
	 * Setup the exception
	 *
	 * @param string $message error message
	 * @param int $code HTTP response code
	 * @param null $raw_error raw error text
	 */
	public function __construct( $message, $code = 0, $raw_error = null ) {

		parent::__construct( $message, $code );

		$this->raw_error = $raw_error;
	}

	/**
	 * Return the raw error text
	 *
	 * @since 2.0
	 * @return null
	 */
	public function get_raw_error() {

		return $this->raw_error;
	}


}
