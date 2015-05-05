<?php
/**
 * WC API Client HTTP Exception class
 *
 * Provides HTTP request/response specific exception handling
 *
 * @since 2.0
 */
class WC_API_Client_HTTP_Exception extends WC_API_Client_Exception {

	/** @var stdClass request array */
	protected $request;

	/** @var stdClass response array */
	protected $response;

	/**
	 * Setup the exception
	 *
	 * @since 2.0
	 * @param string $message error message
	 * @param int $code HTTP response code
	 * @param stdClass $request request class
	 * @param stdClass $response response class
	 */
	public function __construct( $message, $code = 0, $request, $response ) {

		parent::__construct( $message, $code );

		$this->request  = $request;
		$this->response = $response;
	}

	/**
	 * Return the HTTP request class from the request, in the format:
	 *
	 * {
	 *   headers: array of string request headers
	 *   method: request method, e.g. GET
	 *   url: request URL
	 *   params: request parameter array
	 *   data: request data array
	 *   body: JSON encoded request body entity
	 *   duration: request duration, in seconds
	 * }
	 *
	 * @since 2.0
	 * @return array
	 */
	public function get_request() {

		return $this->request;
	}

	/**
	 * Return the HTTP response class from the request, in the format:
	 *
	 * {
	 *   body: raw response body
	 *   code: HTTP response code
	 *   headers: array of HTTP response headers
	 * }
	 *
	 * @since 2.0
	 * @return array
	 */
	public function get_response() {

		return $this->response;
	}


}
