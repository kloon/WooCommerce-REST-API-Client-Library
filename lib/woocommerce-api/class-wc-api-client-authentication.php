<?php
/**
 * WC API Client Authentication
 *
 * Handles SSL/OAuth authentication for the API
 *
 * @since 2.0
 */
class WC_API_Client_Authentication {


	/** @var string endpoint URL */
	protected $url;

	/** @var string consumer key */
	protected $consumer_key;

	/** @var string consumer secret */
	protected $consumer_secret;

	/** OAuth signature method algorithm */
	const HASH_ALGORITHM = 'SHA256';


	/**
	 * Setup class
	 *
	 * @since 2.0
	 * @param string $url endpoint URL, e.g. http://www.woothemes,com/wc-api/v2/orders/123
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 */
	public function __construct( $url, $consumer_key, $consumer_secret ) {

		$this->url             = $url;
		$this->consumer_key    = $consumer_key;
		$this->consumer_secret = $consumer_secret;
	}


	/**
	 * Generate the parameters required for OAuth 1.0a authentication
	 *
	 * @since 2.0
	 * @param $params
	 * @param $method
	 * @return array
	 */
	public function get_oauth_params( $params, $method ) {

		$params = array_merge( $params, array(
			'oauth_consumer_key'     => $this->consumer_key,
			'oauth_timestamp'        => time(),
			'oauth_nonce'            => sha1( microtime() ),
			'oauth_signature_method' => 'HMAC-' . self::HASH_ALGORITHM,
		) );

		// the params above must be included in the signature generation
		$params['oauth_signature'] = $this->generate_oauth_signature( $params, $method );

		return $params;
	}



	/**
	 * Generate OAuth signature, see server-side method here:
	 *
	 * @link https://github.com/woothemes/woocommerce/blob/master/includes/api/class-wc-api-authentication.php#L196-L252
	 *
	 * @since 2.0
	 *
	 * @param array $params query parameters (including oauth_*)
	 * @param string $http_method, e.g. GET
	 * @return string signature
	 */
	public function generate_oauth_signature( $params, $http_method ) {

		$base_request_uri = rawurlencode( $this->url );
		
		if ( isset( $params['filter'] ) ) {
			$filters = $params['filter'];
			unset( $params['filter'] );
			foreach ( $filters as $filter => $filter_value ) {
				$params['filter[' . $filter . ']'] = $filter_value;
			}
		}
		
		// normalize parameter key/values and sort them
		$params = $this->normalize_parameters( $params );
		uksort( $params, 'strcmp' );

		// form query string
		$query_params = array();
		foreach ( $params as $param_key => $param_value ) {
			$query_params[] = $param_key . '%3D' . $param_value; // join with equals sign
		}

		$query_string = implode( '%26', $query_params ); // join with ampersand

		// form string to sign (first key)
		$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;

		return base64_encode( hash_hmac( self::HASH_ALGORITHM, $string_to_sign, $this->consumer_secret, true ) );
	}


	/**
	 * Normalize each parameter by assuming each parameter may have already been
	 * encoded, so attempt to decode, and then re-encode according to RFC 3986
	 *
	 * Note both the key and value is normalized so a filter param like:
	 *
	 * 'filter[period]' => 'week'
	 *
	 * is encoded to:
	 *
	 * 'filter%5Bperiod%5D' => 'week'
	 *
	 * This conforms to the OAuth 1.0a spec which indicates the entire query string
	 * should be URL encoded
	 *
	 * Modeled after the core method here:
	 *
	 * @link https://github.com/woothemes/woocommerce/blob/master/includes/api/class-wc-api-authentication.php#L254-L288
	 *
	 * @since 2.0
	 * @see rawurlencode()
	 * @param array $parameters un-normalized pararmeters
	 * @return array normalized parameters
	 */
	private function normalize_parameters( $parameters ) {

		$normalized_parameters = array();

		foreach ( $parameters as $key => $value ) {

			// percent symbols (%) must be double-encoded
			$key   = str_replace( '%', '%25', rawurlencode( rawurldecode( $key ) ) );
			$value = str_replace( '%', '%25', rawurlencode( rawurldecode( $value ) ) );

			$normalized_parameters[ $key ] = $value;
		}

		return $normalized_parameters;
	}


	/**
	 * Returns true if accessing the API over SSL, primarily used to determine
	 * which authentication mechanism should be used (HTTP Basic Auth or OAuth)
	 *
	 * @since 2.0
	 * @return bool
	 */
	public function is_ssl() {

		return substr( $this->url, 0, 5 ) === 'https';
	}


	/**
	 * Return the consumer key
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_consumer_key() {
		return $this->consumer_key;
	}


	/**
	 * Return the consumer secret
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_consumer_secret() {
		return $this->consumer_secret;
	}


}
