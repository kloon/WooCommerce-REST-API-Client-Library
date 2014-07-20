WooCommerce REST API Client Library
===================================

## About

A PHP wrapper for the WooCommerce REST API. Easily interact with the WooCommerce REST API using this wrapper class.
Feedback and bug reports are appreciated.

## Requirements

PHP 5.2.x
cURL
WooCommerce 2.1 at least on the store

## Getting started
Generate API credentials ( Consumer Key & Consumer Secret ) on your profile page for the store you want to interact with.

> A good place to start is to look at the example script included.

### Initialize the class
```php
<?php
    require_once 'class-wc-api-client.php';

    $consumer_key = 'ck_fcedaba8f0fcb0fb4ae4f1211a75da72'; // Add your own Consumer Key here
	$consumer_secret = 'cs_9914968ae9adafd3741c818bf6d704c7'; // Add your own Consumer Secret here
	$store_url = 'http://localhost/'; // Add the home URL to the store you want to connect to here

	// Initialize the class
	$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );
?>
```

### Get the data using methods
```php
<?php
	// Get all orders
	$orders = $wc_api->get_orders();
	print_r( $orders );
?>
```

**All methods return the data `json_decode()` by default so you can access the data.**

## Available methods

### Index method
- `get_index()`

### Order methods
- `get_orders()`
- `get_orders( $params = array( 'status' => 'completed' ) )`
- `get_order( $order_id )`
- `get_orders_count()`
- `get_order_notes( $order_id )`
- `update_order( $order_id, $data = array( 'status' => 'processing' ) )`
- `update_order( $order_id, $data = array( 'status' => 'processing', 'note' => 'This is a note') )`

### Coupon methods
- `get_coupons()`
- `get_coupon( $coupon_id )`
- `get_coupon_by_code( $coupon_code )`
- `get_coupons_count()`


### Customer methods
- `get_customers()`
- `get_customers( $params = array( 'filter[created_at_min]' => '2013-12-01' ) )`
- `get_customer( $customer_id )`
- `get_customers_count()`
- `get_customer_orders( $customer_id )`

### Product methods
- `get_products()`
- `get_products( $params = array( 'filter[created_at_min]' => '2013-12-01' ) )`
- `get_product( $product_id )`
- `get_products_count()`
- `get_product_reviews( $product_id )`

### Report methods
- `get_reports()`
- `get_sales_report( $params = array( 'filter[start_date]' => '2013-12-01', 'filter[end_date]' => '2013-12-09' ) )`
- `get_top_sellers_report( $params = array( 'filter[limit]' = '10' ) )`

### Custom endpoints
If you extended the WooCommerce API with your own endpoints you can use the following function to get access to that data
- `make_custom_endpoint_call( $endpoint, $params = array(), $method = 'GET' )`

## Changelog

**version 0.3.1 - 2014-05-02**

- Fix parameter normalization issue with WC 2.1.7+

**version 0.3 - 2014-02-20**

- Add HTTP error messages on failed cURL calls

**version 0.2 - 2014-01-22**

- Add support for filters/params to endpoint functions
- Add new top sellers report endpoint function
- Add function to call custom endpoints

**version 0.1 - 2013-12-10**

- Initial release

## Credit

Copyright (c) 2013-2014 - [Gerhard Potgieter](http://gerhardpotgieter.com/)
Released under the [GPL3 license](http://www.gnu.org/licenses/gpl-3.0.html)
