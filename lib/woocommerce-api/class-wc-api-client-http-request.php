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

	/** @var stdClass request data */
	protected $request;

	/** @var bool true to decode JSON as array */
	protected $json_decode_as_array;

	/** @var bool true to include cURL log, HTTP request/response object in result */
	protected $debug;

	/** @var stdClass response data */
	protected $response;

	/** @var string raw HTTP response headers */
	protected $curl_headers;


	/**
	 * Setup HTTP request
	 *
	 * @since 2.0
	 * @param array $args request args
	 */
	public function __construct( $args ) {

		$this->request = new stdClass();

		$this->request->headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'User-Agent: WooCommerce API Client-PHP/' . WC_API_Client::VERSION,
		);

		// GET, POST, PUT, DELETE, etc.
		$this->request->method = $args['method'];

		// trailing slashes tend to cause OAuth authentication issues, so strip them
		$this->request->url = rtrim( $args['url'], '/' );

		$this->request->params = array();
		$this->request->data = $args['data'];

		// JSON output format
		$this->json_decode_as_array = ( 'array' === $args['options']['json_decode'] );

		// debug mode?
		$this->debug = (bool) $args['options']['debug'];

		// optional cURL opts
		$timeout = (int) $args['options']['timeout'];
		$ssl_verify = (bool) $args['options']['ssl_verify'];

		$this->ch = curl_init();

		// default cURL opts
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify );
		curl_setopt( $this->ch, CURLOPT_SSL_VERIFYHOST, $ssl_verify );
		curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $this->ch, CURLOPT_TIMEOUT, (int) $timeout );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );

		// set request headers
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $this->request->headers );

		// save response headers
		curl_setopt( $this->ch, CURLOPT_HEADERFUNCTION, array( $this, 'curl_stream_headers' ) );

		// set request method and data
		switch ( $this->request->method ) {

			case 'GET':
				$this->request->body = null;
				$this->request->params = (array) $this->request->data;
			break;

			case 'PUT':
				$this->request->body = json_encode( $this->request->data );
				curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $this->request->body );
			break;

			case 'POST':
				$this->request->body = json_encode( $this->request->data );
				curl_setopt( $this->ch, CURLOPT_POST, true );
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $this->request->body );
			break;

			case 'DELETE':
				$this->request->body = null;
				$this->request->params = (array) $this->request->data;
				curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				break;
		}

		// auth
		$this->request->url = $this->get_url_with_auth( $args['consumer_key'], $args['consumer_secret'] );

		// set request url
		curl_setopt( $this->ch, CURLOPT_URL, $this->request->url );
	}


	/**
	 * Set authentication parameters for the request
	 *
	 * @since 2.0
	 * @param string $consumer_key API consumer key
	 * @param string $consumer_secret API consumer secret
	 * @return string request URL with authentication parameters added
	 */
	protected function get_url_with_auth( $consumer_key, $consumer_secret ) {

		$auth = new WC_API_Client_Authentication( $this->request->url, $consumer_key, $consumer_secret );

		if ( $auth->is_ssl() ) {

			// query string authentication over SSL works with all servers, whereas HTTP basic auth fails in certain cases
			// see https://github.com/kloon/WooCommerce-REST-API-Client-Library/issues/29
			$this->request->params = array_merge( $this->request->params, array(
				'consumer_key'    => $auth->get_consumer_key(),
				'consumer_secret' => $auth->get_consumer_secret(),
			) );

		} else {

			$this->request->params = array_merge( $this->request->params, $auth->get_oauth_params( $this->request->params, $this->request->method ) );
		}

		// build url
		return $this->request->url . ( ! empty( $this->request->params ) ? '?' . http_build_query( $this->request->params ) : '' );
	}


	/**
	 * Send the request
	 *
	 * @since 2.0
	 * @return object|array result
	 * @throws WC_API_Client_HTTP_Exception invalid decoded JSON or any non-HTTP 200/201 response
	 */
	public function dispatch() {

		$this->response = new stdClass();

		// blank headers
		$this->curl_headers = '';

		$start_time = microtime( true );

		// send request + save raw response body
		$this->response->body = curl_exec( $this->ch );

		// request duration
		$this->request->duration = round( microtime( true ) - $start_time, 5 );

		// response code
		$this->response->code = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );

		// response headers
		$this->response->headers = $this->get_response_headers();

		curl_close( $this->ch );

		$parsed_response = $this->get_parsed_response( $this->response->body );

		// check for invalid JSON
		if ( null === $parsed_response ) {

			throw new WC_API_Client_HTTP_Exception( sprintf( 'Invalid JSON returned for %s.', $this->request->url ), $this->response->code, $this->request, $this->response );
		}

		// any non-200/201/202 response code indicates an error
		if ( ! in_array( $this->response->code, array( '200', '201', '202' ) ) ) {

			// error message/code is nested sometimes
			if ( $this->json_decode_as_array ) {

				list( $error_message, $error_code ) = is_array( $parsed_response['errors'] ) ? array(
					$parsed_response['errors'][0]['message'],
					$parsed_response['errors'][0]['code']
				) : array(
					$parsed_response['errors']['message'],
					$parsed_response['errors']['code']
				);

			} else {

				list( $error_message, $error_code ) = is_array( $parsed_response->errors ) ? array(
					$parsed_response->errors[0]->message,
					$parsed_response->errors[0]->code
				) : array(
					$parsed_response->errors->message,
					$parsed_response->errors->code
				);
			}

			throw new WC_API_Client_HTTP_Exception( sprintf( 'Error: %s [%s]', $error_message, $error_code ), $this->response->code, $this->request, $this->response);
		}

		return $this->build_result( $parsed_response );
	}


	/**
	 * JSON decode the response body after stripping any invalid leading or
	 * trailing characters.
	 *
	 * Plugins (looking at you WP Super Cache) or themes
	 * can add output to the returned JSON which breaks decoding.
	 *
	 * @since 2.0
	 * @param string $raw_body raw response body
	 * @return object|array JSON decoded response body
	 */
	protected function get_parsed_response( $raw_body ) {

		$json_start = strpos( $raw_body, '{' );
		$json_end = strrpos( $raw_body, '}' ) + 1; // inclusive

		$json = substr( $raw_body, $json_start, ( $json_end - $json_start ) );

		return json_decode( $json, $this->json_decode_as_array );
	}


	/**
	 * Build the result object/array
	 *
	 * @since 2.0.0
	 * @param object|array JSON decoded result
	 * @return object|array in format:
	 * {
	 *  <result data>
	 *  'http' =>
	 *   'request' => stdClass(
	 *     'url' => request URL
	 *     'method' => request method
	 *     'body' => JSON encoded request body entity
	 *     'headers' => array of request headers
	 *     'duration' => request duration, in seconds
	 *     'params' => optional raw params
	 *     'data' => optional raw request data
	 *     'duration' =>
	 *    )
	 *   'response' => stdClass(
	 *     'body' => raw response body
	 *     'code' => HTTP response code
	 *     'headers' => HTTP response headers in assoc array
	 *   )
	 * }
	 */
	protected function build_result( $parsed_response ) {

		// add cURL log, HTTP request/response object
		if ( $this->debug ) {

			if ( $this->json_decode_as_array ) {

				$parsed_response['http'] = array(
					'request'  => json_decode( json_encode( $this->request ), true ),
					'response' => json_decode( json_encode( $this->response ), true ),
				);

			} else {

				$parsed_response->http = new stdClass();
				$parsed_response->http->request = $this->request;
				$parsed_response->http->response = $this->response;
			}
		}

		return $parsed_response;
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

		return $headers;
	}

}
