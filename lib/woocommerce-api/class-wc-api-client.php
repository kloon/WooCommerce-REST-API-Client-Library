<?php
/**
 * WC API Client
 *
 * @since 2.0
 */
class WC_API_Client {


	/** API client version */
	const VERSION = '2.0.1';

	/** @var string store URL, e.g. http://www.woothemes.com */
	public $store_url;

	/** @var string consumer key */
	public $consumer_key;

	/** @var string consumer secret */
	public $consumer_secret;

	/** @var string API URL, e.g. http://www.woothemes.com/wc-api/v2 */
	public $api_url;

	/** @var bool true if debug is enabled */
	public $debug = false;

	/** @var bool return the response data as an array, defaults to object */
	public $return_as_array = false;

	/** @var bool perform validation on the API URL */
	public $validate_url = false;

	/** @var int HTTP request timeout */
	public $timeout = 30;

	/** @var bool true to perform SSL peer verification */
	public $ssl_verify = true;

	/** Resources */

	/** @var WC_API_Client_Resource_Coupons instance */
	public $coupons;

	/** @var WC_API_Client_Resource_Custom instance */
	public $custom;

	/** @var WC_API_Client_Resource_Customers instance */
	public $customers;

	/** @var WC_API_Client_Resource_Index instance */
	public $index;

	/** @var WC_API_Client_Resource_Orders instance */
	public $orders;

	/** @var WC_API_Client_Resource_Order_Notes instance */
	public $order_notes;

	/** @var WC_API_Client_Resource_Order_Refunds instance */
	public $order_refunds;

	/** @var WC_API_Client_Resource_Products instance */
	public $products;

	/** @var WC_API_Client_Resource_Reports instance */
	public $reports;

	/** @var WC_API_Client_Resource_Webhooks instance */
	public $webhooks;


	/**
	 * Setup the client
	 *
	 * @since 2.0
	 * @param string $store_url store URL, e.g. http://www.woothemes.com
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 * @param array $options client options
	 */
	public function __construct( $store_url, $consumer_key, $consumer_secret, $options = array() ) {

		// required functions
		if ( ! extension_loaded( 'curl' ) ) {
			throw new Exception( 'WooCommerce REST API client requires the cURL PHP extension.' );
		}

		if ( ! extension_loaded( 'json' ) ) {
			throw new Exception( 'WooCommerce REST API client needs the JSON extension.' );
		}

		// set required info
		$this->store_url = $store_url;
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;

		// load each API resource
		$this->init_resources();

		// build API url from store URL
		$this->build_api_url();

		// set options
		$this->parse_options( $options );

		if ( $this->validate_url ) {
			$this->validate_api_url();
		}
	}


	/**
	 * Load each API resource
	 *
	 * @since 2.0
	 */
	public function init_resources() {

		$resources = array(
			'WC_API_Client_Resource_Coupons'       => 'coupons',
			'WC_API_Client_Resource_Custom'        => 'custom',
			'WC_API_Client_Resource_Customers'     => 'customers',
			'WC_API_Client_Resource_Index'         => 'index',
			'WC_API_Client_Resource_Orders'        => 'orders',
			'WC_API_Client_Resource_Order_Notes'   => 'order_notes',
			'WC_API_Client_Resource_Order_Refunds' => 'order_refunds',
			'WC_API_Client_Resource_Products'      => 'products',
			'WC_API_Client_Resource_Reports'       => 'reports',
			'WC_API_Client_Resource_Webhooks'      => 'webhooks',
		);

		foreach ( $resources as $resource_class => $resource_method ) {

			if ( class_exists( $resource_class ) ) {
				$this->$resource_method = new $resource_class( $this );
			}
		}
	}


	/**
	 * Build the correct API URL given the store URL provided
	 *
	 * @since 2.0
	 */
	public function build_api_url() {

		$url = parse_url( $this->store_url );

		// default to http if not provided
		$scheme = isset( $url['scheme'] ) ? $url['scheme'] : 'http';

		// set host
		$host = $url['host'];

		// add port to host if provided
		$host .= isset( $url['port'] ) ? ':' . $url['port'] : '';

		// set path and strip any trailing slashes
		$path = isset( $url['path'] ) ? rtrim( $url['path'], '/' ) : '';

		// add WC API path
		$path .= '/wc-api/v2/';

		// build URL
		$this->api_url = "{$scheme}://{$host}{$path}";
	}


	/**
	 * Parse client options, current available options are:
	 *
	 * `debug` - true to include cURL log, HTTP request object, and HTTP response object in result
	 * `return_as_array` - true to return the response as an associative array instead of object
	 * `validate_url` - true to validate the API URL is correct before making API calls
	 *
	 *
	 * to implement:
	 * `timeout` - HTTP request timeout
	 * ... additional HTTP options for handling proxy servers, etc.
	 *
	 * @since 2.0
	 * @param array $options
	 */
	public function parse_options( $options ) {

		$valid_options = array(
			'debug',
			'return_as_array',
			'validate_url',
			'timeout',
			'ssl_verify',
		);

		foreach ( (array) $options as $opt_key => $opt_value ) {

			// backwards compat
			if ( 'verbose_mode' === $opt_key ) {
				$opt_key = 'debug';
			}

			if ( ! in_array( $opt_key, $valid_options ) ) {
				continue;
			}

			$this->$opt_key = $opt_value;
		}
	}


	/**
	 * Validate the API URL by checking if the API index exists and is parseable,
	 * as well as forcing SSL for stores that allow it
	 *
	 * @since 2.0
	 * @throws WC_API_Client_Exception
	 */
	public function validate_api_url() {

		$index = @file_get_contents( $this->api_url );

		// check for HTTP 404 response (file_get_contents() returns false when encountering 404)
		// this usually means:
		// 1) the store URL is not correct (missing sub-directory path, etc)
		// 2) pretty permalinks are disabled
		if ( false === $index ) {
			throw new WC_API_Client_Exception( sprintf( 'Invalid URL, no WC API found at %s -- ensure your store URL is correct and pretty permalinks are enabled.', $this->api_url ), 404 );
		}

		// older versions of WC (2.0 and under) will simply return a "1"
		if ( '1' === $index ) {
			throw new WC_API_Client_Exception( sprintf( 'Please upgrade the WooCommerce version on %s to v2.2 or greater.', $this->api_url ) );
		}

		// strip invalid leading/trailing characters from JSON
		$json_start = strpos( $index, '{' );
		$json_end = strrpos( $index, '}' ) + 1; // inclusive

		$index = json_decode( substr( $index, $json_start, ( $json_end - $json_start ) ) );

		// check for invalid JSON, an error here usually means:
		// 1) there's some garbage in the JSON output, WP Super Cache is notorious for adding an HTML comment to non-cached pages
		if ( null === $index ) {
			throw new WC_API_Client_Exception( sprintf( 'WC API found, but JSON is corrupt -- ensure the index at %s is valid JSON.', $this->api_url ) );
		}

		// check if the site URL returned is SSL, but SSL is not enabled
		if ( 'https' === parse_url( $index->store->URL, PHP_URL_SCHEME ) && ! $index->store->meta->ssl_enabled ) {

			// override the user-entered URL with the SSL version
			$this->api_url = str_replace( 'http://', 'https://', $this->api_url );
		}
	}


	/**
	 * Make the API call to specified endpoint with the given data -- also
	 * handle decoding the JSON
	 *
	 * @since 2.0
	 * @param string $method HTTP method, e.g. GET
	 * @param string $path request path, e.g. orders/123
	 * @param array $request_data either query parameters or the request body
	 * @return object|array object by default
	 * @throws WC_API_Client_Exception HTTP or authentication errors
	 */
	public function make_api_call( $method, $path, $request_data ) {

		$args = array(
			'method'          => $method,
			'url'             => $this->api_url . $path,
			'data'            => $request_data,
			'consumer_key'    => $this->consumer_key,
			'consumer_secret' => $this->consumer_secret,
			'options'         => array(
				'timeout'     => $this->timeout,
				'ssl_verify'  => $this->ssl_verify,
				'json_decode' => $this->return_as_array ? 'array' : 'object',
				'debug'       => $this->debug,
			)
		);

		$request = new WC_API_Client_HTTP_Request( $args );

		return $request->dispatch();
	}


}
