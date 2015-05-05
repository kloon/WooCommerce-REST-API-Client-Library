<?php
/**
 * WooCommerce API Client Class
 *
 * @version 2.0.0
 * @license GPL 3 or later http://www.gnu.org/licenses/gpl.html
 */

$dir = dirname( __FILE__ ) . '/woocommerce-api/';

// required functions
if ( ! function_exists( 'curl_init' ) ) {
	throw new Exception( 'WooCommerce REST API client requires the cURL PHP extension.' );
}

if ( ! function_exists( 'json_decode' ) ) {
	throw new Exception( 'WooCommerce REST API client needs the JSON extension.' );
}

// base class
require_once( $dir . 'class-wc-api-client.php' );

// plumbing
require_once( $dir . 'class-wc-api-client-authentication.php' );
require_once( $dir . 'class-wc-api-client-http-request.php' );

// exceptions
require_once( $dir . '/exceptions/class-wc-api-client-exception.php' );
require_once( $dir . '/exceptions/class-wc-api-client-http-exception.php' );

// resources
require_once( $dir . '/resources/class-wc-api-client-resource.php' );
require_once( $dir . '/resources/class-wc-api-client-orders.php' );
