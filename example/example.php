<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );
require_once "../class-wc-api-client.php";

$consumer_key = 'ck_fcedaba8f0fcb0fb4ae4f1211a75da72'; // Add your own Consumer Key here
$consumer_secret = 'cs_9914968ae9adafd3741c818bf6d704c7'; // Add your own Consumer Secret here
$store_url = 'http://localhost/demo/'; // Add the home URL to the store you want to connect to here

// Initialize the class
$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );

// Get Index
//print_r( $wc_api->get_index() );

// Get all orders
//print_r( $wc_api->get_orders( array( 'status' => 'completed' ) ) );

// Get a single order by id
//print_r( $wc_api->get_order( 166 ) );

// Get orders count
//print_r( $wc_api->get_orders_count() );

// Get order notes for a specific order
//print_r( $wc_api->get_order_notes( 166 ) );

// Update order status
//print_r( $wc_api->update_order( 166, $data = array( 'status' => 'failed' ) ) );

// Get all coupons
//print_r( $wc_api->get_coupons() );

// Get coupon by id
//print_r( $wc_api->get_coupon( 173 ) );

// Get coupon by code
//print_r( $wc_api->get_coupon_by_code( 'test coupon' ) );

// Get coupons count
//print_r( $wc_api->get_coupons_count() );

// Get customers
//print_r( $wc_api->get_customers() );

// Get customer by id
//print_r( $wc_api->get_customer( 2 ) );

// Get customer count
//print_r( $wc_api->get_customers_count() );

// Get customer orders
//print_r( $wc_api->get_customer_orders( 2 ) );

// Get all products
//print_r( $wc_api->get_products() );

// Get a single product by id
//print_r( $wc_api->get_product( 167 ) );

// Get products count
//print_r( $wc_api->get_products_count() );

// Get product reviews
//print_r( $wc_api->get_product_reviews( 167 ) );

// Get reports
//print_r( $wc_api->get_reports() );

// Get sales report
//print_r( $wc_api->get_sales_report() );

// Get top sellers report
// print_r( $wc_api->get_top_sellers_report() );


