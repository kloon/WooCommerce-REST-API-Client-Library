<?php
/**
 * WooCommerce API Client Class
 *
 * @author Gerhard Potgieter
 * @since 2013.12.05
 * @copyright Gerhard Potgieter
 * @version 1.0
 * @license GPL 3 or later http://www.gnu.org/licenses/gpl.html
 */

class WC_API_Client {

	/**
	 * API base endpoint
	 */
	const API_ENDPOINT = 'wc-api/v1/';

	/**
	 * The HASH alorithm to use for oAuth signature, SHA256 or SHA1
	 */
	const HASH_ALGORITHM = 'SHA256';

	/**
	 * The API URL
	 * @var string
	 */
	private $_api_url;

	/**
	 * The WooCommerce Consumer Key
	 * @var string
	 */
	private $_consumer_key;

	/**
	 * The WooCommerce Consumer Secret
	 * @var string
	 */
	private $_consumer_secret;

	/**
	 * If the URL is secure, used to decide if oAuth or Basic Auth must be used
	 * @var boolean
	 */
	private $_is_ssl;

	/**
	 * Return the API data as an Object, set to false to keep it in JSON string format
	 * @var boolean
	 */
	private $_return_as_object = true;

	/**
	 * Default contructor
	 * @param string  $consumer_key    The consumer key
	 * @param string  $consumer_secret The consumer secret
	 * @param string  $store_url       The URL to the WooCommerce store
	 * @param boolean $is_ssl          If the URL is secure or not, optional
	 */
	public function __construct( $consumer_key, $consumer_secret, $store_url, $is_ssl = false ) {
		if ( isset( $consumer_key ) && isset( $consumer_secret ) && isset( $store_url ) ) {
			$this->_api_url = $store_url . self::API_ENDPOINT;
			$this->set_consumer_key( $consumer_key );
			$this->set_consumer_secret( $consumer_secret );
			$this->set_is_ssl( $is_ssl );
		} else if ( ! isset( $consumer_key ) && ! isset( $consumer_secret ) ) {
			throw new Exception( 'Error: __construct() - Consumer Key / Consumer Secret missing.' );
		} else {
			throw new Exception( 'Error: __construct() - Store URL missing.' );
		}
	}

	/**
	 * Get API Index
	 * @return mixed|json string
	 */
	public function get_index() {
		return $this->_make_api_call( '' );
	}

	/**
	 * Get all orders
	 * @param  integer [optional] $order_id
	 * @return mixed|jason string
	 */
	public function get_orders( $order_id = null ) {
		if ( isset( $order_id ) )
			$endpont = 'orders/' . $order_id;
		else $endpont = 'orders';
		return $this->_make_api_call( $endpont );
	}

	/**
	 * Get a single order
	 * @param  integer $order_id
	 * @return mixed|json string
	 */
	public function get_order( $order_id ) {
		return $this->_make_api_call( 'orders/' . $order_id );
	}

	/**
	 * Get the total order count
	 * @return mixed|json string
	 */
	public function get_orders_count() {
		return $this->_make_api_call( 'orders/count' );
	}

	/**
	 * Get orders notes for an order
	 * @param  integer $order_id
	 * @return mixed|json string
	 */
	public function get_order_notes( $order_id ) {
		return $this->_make_api_call( 'orders/' . $order_id . '/notes' );
	}

	/**
	 * Get all coupons
	 * @param  integer [optional] $coupon_id
	 * @return mixed|json string
	 */
	public function get_coupons( $coupon_id = null ) {
		if ( isset( $coupon_id ) )
			$endpont = 'coupons/' . $coupon_id;
		else $endpont = 'coupons';
		return $this->_make_api_call( $endpont );
	}

	/**
	 * Get a single coupon
	 * @param  integer $coupon_id
	 * @return mixed|json string
	 */
	public function get_coupon( $coupon_id ) {
		return $this->_make_api_call( 'coupons/' . $coupon_id );
	}

	/**
	 * Get the total coupon count
	 * @return mixed|json string
	 */
	public function get_coupons_count() {
		return $this->_make_api_call( 'coupons/count' );
	}

	/**
	 * Get a coupon by the coupon code
	 * @param  string $coupon_code
	 * @return mixed|json string
	 */
	public function get_coupon_by_code( $coupon_code ) {
		return $this->_make_api_call( 'coupons/code/' . $coupon_code );
	}

	/**
	 * Get all customers
	 * @param  integer [optional] $customer_id
	 * @return mixed|json string
	 */
	public function get_customers( $customer_id = null ) {
		if ( isset( $order_id ) )
			$endpont = 'customers/' . $customer_id;
		else $endpont = 'customers';
		return $this->_make_api_call( $endpont );
	}

	/**
	 * Get a single customer
	 * @param  integer $customer_id
	 * @return mixed|json string
	 */
	public function get_customer( $customer_id ) {
		return $this->_make_api_call( 'customers/' . $customer_id );
	}

	/**
	 * Get the total customer count
	 * @return mixed|json string
	 */
	public function get_customers_count() {
		return $this->_make_api_call( 'customers/count' );
	}

	/**
	 * Get the customer's orders
	 * @param  integer $customer_id
	 * @return mixed|json string
	 */
	public function get_customer_orders( $customer_id ) {
		return $this->_make_api_call( 'customers/' . $customer_id . '/orders' );
	}

	/**
	 * Get all the products
	 * @param  integer [optional] $product_id
	 * @return mixed|json string
	 */
	public function get_products( $product_id = null ) {
		if ( isset( $product_id ) )
			$endpont = 'products/' . $product_id;
		else $endpont = 'products';
		return $this->_make_api_call( $endpont );
	}

	/**
	 * Get a single product
	 * @param  integer $product_id
	 * @return mixed|json string
	 */
	public function get_product( $product_id ) {
		return $this->_make_api_call( 'products/' . $product_id );
	}

	/**
	 * Get the total product count
	 * @return mixed|json string
	 */
	public function get_product_count() {
		return $this->_make_api_call( 'products/count' );
	}

	/**
	 * Get reviews for a product
	 * @param  integer $product_id
	 * @return mixed|json string
	 */
	public function get_product_reviews( $product_id ) {
		return $this->_make_api_call( 'products/' . $product_id . '/reviews' );
	}

	/**
	 * Get reports
	 * @return mixed|json string
	 */
	public function get_reports() {
		return $this->_make_api_call( 'reports' );
	}

	/**
	 * Get the sales report
	 * @return mixed|json string
	 */
	public function get_sales_report() {
		return $this->_make_api_call( 'reports/sales' );
	}

	/**
	 * Set the consumer key
	 * @param string $consumer_key
	 */
	public function set_consumer_key( $consumer_key ) {
		$this->_consumer_key = $consumer_key;
	}

	/**
	 * Set the consumer secret
	 * @param string $consumer_secret
	 */
	public function set_consumer_secret( $consumer_secret ) {
		$this->_consumer_secret = $consumer_secret;
	}

	/**
	 * Set SSL variable
	 * @param boolean $is_ssl
	 */
	public function set_is_ssl( $is_ssl ) {
		if ( $is_ssl == '' ) {
			if ( strtolower( substr( $this->_api_url, 0, 5 ) ) == 'https' ) {
				$this->_is_ssl = true;
			} else $this->_is_ssl = false;
		} else $this->_is_ssl = $is_ssl;
	}

	/**
	 * Set the return data as object
	 * @param boolean $is_object
	 */
	public function set_return_as_object( $is_object = true ) {
		$this->_return_as_object = $is_object;
	}

	/**
	 * Make the call to the API
	 * @param  string $endpoint
	 * @param  array  $params
	 * @param  string $method
	 * @return mixed|json string
	 */
	private function _make_api_call( $endpoint, $params = array(), $method = 'GET' ) {
		$ch = curl_init();

		// Check if we must use Basic Auth or 1 legged oAuth, if SSL we use basic, if not we use OAuth 1.0a one-legged
		if ( $this->_is_ssl ) {
			curl_setopt( $ch, CURLOPT_USERPWD, $this->_consumer_key . ":" . $this->_consumer_secret );
		} else {
			$params['oauth_consumer_key'] = $this->_consumer_key;
			$params['oauth_timestamp'] = time();
			$params['oauth_nonce'] = sha1( time() );
			$params['oauth_signature_method'] = 'HMAC-' . self::HASH_ALGORITHM;
			$params['oauth_signature'] = $this->generate_oauth_signature( $params, $method, $endpoint );
		}

		if ( isset( $params ) && is_array( $params ) ) {
			$paramString = '?' . http_build_query( $params );
		} else {
			$paramString = null;
		}

		// Set up the enpoint URL
		curl_setopt( $ch, CURLOPT_URL, $this->_api_url . $endpoint . $paramString );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );

		 $return = curl_exec( $ch );

		 if ( $this->_return_as_object ) {
		 	$return = json_decode( $return );
		 }
		 return $return;
	}

	/**
	 * Generate oAuth signature
	 * @param  array  $params
	 * @param  string $http_method
	 * @param  string $endpoint
	 * @return string
	 */
	public function generate_oauth_signature( $params, $http_method, $endpoint ) {
		$base_request_uri = rawurlencode( $this->_api_url . $endpoint );

		// normalize parameter key/values and sort them
		array_walk( $params, array( $this, 'normalize_parameters' ) );
		uksort( $params, 'strcmp' );

		// form query string
		$query_params = array();
		foreach ( $params as $param_key => $param_value ) {
			$query_params[] = $param_key . '%3D' . $param_value; // join with equals sign
		}

		$query_string = implode( '%26', $query_params ); // join with ampersand

		// form string to sign (first key)
		$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;

		return base64_encode( hash_hmac( self::HASH_ALGORITHM, $string_to_sign, $this->_consumer_secret, true ) );
	}

	/**
	 * Normalize the paramaters
	 * @param  string $key
	 * @param  string $value
	 * @return void
	 */
	private function normalize_parameters( &$key, &$value ) {
		$key = rawurlencode( rawurldecode( $key ) );
		$value = rawurlencode( rawurldecode( $value ) );
	}
}
?>