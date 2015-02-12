<?php
/**
 * WC API Client HTTP Request
 *
 * Handles HTTP communication to the WC API via cURL
 *
 * @since 2.0
 */
class WC_API_Client_HTTP_Request {


	/** @var resource cURL handle */
	protected $ch;

	/** @var string request method, e.g. GET */
	protected $method;

	/** @var string request URL */
	protected $url;

	/** @var array query string parameters */
	protected $params = array();

	/** @var array */
	protected $body;

	/** @var string raw HTTP response headers */
	protected $curl_headers;


	/**
	 * Setup HTTP request
	 *
	 * @since 2.0
	 * @param string $method HTTP request method, e.g. GET
	 * @param string $url endpoint URL, e.g. http://www.woothemes,com/wc-api/v2/orders/123
	 * @param array $data query parameters or request body, depending on HTTP method
	 * @param WC_API_Client_Authentication $auth class instance
	 */
	public function __construct( $method, $url, $data, $auth ) {

		$this->method = $method;
		$this->ch = curl_init();

		// default cURL opts
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt( $this->ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );

		// set request headers
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json',
			'User-Agent: WooCommerce API Client-PHP/' . WC_API_Client::VERSION,
		) );

		// save response headers
		curl_setopt( $this->ch, CURLOPT_HEADERFUNCTION, array( $this, 'curl_stream_headers' ) );

		// set request method and data
		switch ( $method ) {

			case 'GET':
				$this->params = (array) $data;
			break;

			case 'PUT':
				curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
			break;

			case 'POST':
				curl_setopt( $this->ch, CURLOPT_POST, true );
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
			break;

			case 'DELETE':
				$this->params = (array) $data;
				curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				break;
		}

		// set authentication
		$this->setup_auth( $auth );

		// build url
		$this->url = $url . ( ! empty( $this->params ) ? '?' . http_build_query( $this->params ) : '' );

		// set request url
		curl_setopt( $this->ch, CURLOPT_URL, $this->url );
	}


	/**
	 * Set authentication parameters for the request
	 *
	 * @since 2.0
	 * @param $auth WC_API_Client_Authentication class instance
	 */
	protected function setup_auth( $auth ) {

		if ( $auth->is_ssl() ) {

			// query string authentication over SSL works with all servers, whereas HTTP basic auth fails in certain cases
			// see https://github.com/kloon/WooCommerce-REST-API-Client-Library/issues/29
			$this->params = array_merge( $this->params, array(
				'consumer_key'    => $auth->get_consumer_key(),
				'consumer_secret' => $auth->get_consumer_secret(),
			) );

		} else {

			$this->params = array_merge( $this->params, $auth->get_oauth_params( $this->params, $this->method ) );
		}
	}


	/**
	 * Send the request
	 *
	 * @since 2.0
	 * @return array in format:
	 * {
	 *   'body' => raw response body
	 *   'code' => HTTP response code
	 *   'headers' => HTTP response headers in assoc array
	 *   'duration' => request duration, in seconds
	 * }
	 * @throws WC_API_Client_Exception any non HTTP 200/201 response
	 */
	public function dispatch() {

		// blank headers
		$this->curl_headers = '';

		$start_time = microtime( true );

		// send request
		$response_body = curl_exec( $this->ch );

		$duration = round( microtime( true ) - $start_time, 5 );

		// save response code
		$response_code = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );

		curl_close( $this->ch );

		// any non-200/201 response code indicates an error
		if ( ! in_array( $response_code, array( '200', '201' ) ) ) {

			$error = json_decode( $response_body );

			// check for invalid JSON
			if ( null === $error ) {

				throw new WC_API_Client_Exception( sprintf( 'Invalid JSON returned for %s.', $this->url ), $response_code, $error );
			}

			// error message/code is nested sometimes
			list( $error_message, $error_code ) = is_array( $error->errors ) ? array( $error->errors[0]->message, $error->errors[0]->code ) : array( $error->errors->message, $error->errors->code );

			throw new WC_API_Client_Exception( sprintf( 'Error: %s [%s]', $error_message, $error_code ), $response_code, $response_body );
		}

		return array(
			'url'      => $this->url,
			'body'     => $response_body,
			'code'     => $response_code,
			'headers'  => $this->get_response_headers(),
			'duration' => $duration,
		);
	}


	/**
	 * Save the cURL response headers for later processing
	 *
	 * @since 2.0
	 * @see WP_Http_Curl::stream_headers()
	 * @param object $_ the cURL resource handle (unused)
	 * @param string $headers the current response headers
	 * @return int the size of the processed headers
	 */
	public function curl_stream_headers( $_, $headers ) {

		$this->curl_headers .= $headers;
		return strlen( $headers );
	}


	/**
	 * Parse the raw response headers into an assoc array in format:
	 * {
	 *   'Header-Key' => header value
	 *   'Duplicate-Key' => array(
	 *     0 => value 1
	 *     1 => value 2
	 *   )
	 * }
	 *
	 * @since 2.0
	 * @see WP_HTTP::processHeaders
	 * @return array
	 */
	protected function get_response_headers() {

		// get the raw headers
		$raw_headers = preg_replace('/\n[ \t]/', ' ', str_replace( "\r\n", "\n", $this->curl_headers ) );

		// spit them
		$raw_headers = array_filter( explode( "\n", $raw_headers ), 'strlen' );

		$headers = array();

		// parse into assoc array
		foreach ( $raw_headers as $header ) {

			// skip response codes (appears as HTTP/1.1 200 OK or HTTP/1.1 100 Continue)
			if ( 'HTTP/' === substr( $header, 0, 5 ) ) {
				continue;
			}

			list( $key, $value ) = explode( ':', $header, 2 );

			if ( isset( $headers[ $key ] ) ) {

				// ensure duplicate headers aren't overwritten
				$headers[ $key ] = array( $headers[ $key ] );
				$headers[ $key ][] = $value;

			} else {
				$headers[ $key ] = $value;
			}

		}

		return $this->response_headers = $headers;
	}

}
