<?php
/**
 * WC API Client Exception class
 *
 * Provides a simple wrapper around Exception to help with error handling
 *
 * @since 2.0
 */
class WC_API_Client_Exception extends Exception {

	/** @var array response array */
	protected $http_response;

	/**
	 * Setup the exception
	 *
	 * @param string $message error message
	 * @param int $code HTTP response code
	 * @param array|null $http_response response array
	 */
	public function __construct( $message, $code = 0, $http_response = null ) {

		parent::__construct( $message, $code );

		$this->http_response = $http_response;
	}

	/**
	 * Return the HTTP response array from the request
	 *
	 * @since 2.0
	 * @return array
	 */
	public function get_http_response() {

		return $this->http_response;
	}


}
