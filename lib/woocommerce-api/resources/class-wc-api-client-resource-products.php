<?php
/**
 * WC API Client Products resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Products extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'products', 'product', $client );
	}


	/**
	 * Get products
	 *
	 * GET /products
	 * GET /products/#{id}
	 *
	 * @since 2.0
	 * @param null|int $id product ID or null to get all products
	 * @param array $args acceptable products endpoint args, like `filter[]`
	 * @return array|object products!
	 */
	public function get( $id = null, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => $id,
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get product by SKU
	 *
	 * GET /products/sku/{sku}
	 *
	 * Note this will throw an exception if no products are found (404 not found)
	 *
	 * @since 2.0
	 * @param string $sku product SKU
	 * @param array $args acceptable product SKU lookup endpoint args, currently only `fields`
	 * @return array|object product!
	 */
	public function get_by_sku( $sku, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( 'sku', urlencode( $sku ) ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Create a product
	 *
	 * POST /products
	 *
	 * @since 2.0
	 * @param array $data valid product data
	 * @return array|object your newly-created product
	 */
	public function create( $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update a product
	 *
	 * PUT /product/#{id}
	 *
	 * @since 2.0
	 * @param int $id product ID
	 * @param array $data product data to update
	 * @return array|object your newly-updated product
	 */
	public function update( $id, $data ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => $id,
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Delete a product
	 *
	 * DELETE /products/#{id}
	 *
	 * @since 2.0
	 * @param int $id product ID
	 * @param bool $force true to permanently delete the product, false to trash it
	 * @return array|object response
	 */
	public function delete( $id, $force = false ) {

		$this->set_request_args( array(
			'method' => 'DELETE',
			'path'   => $id,
			'params' => array( 'force' => $force ),
		) );

		return $this->do_request();
	}


	/**
	 * Get a count of products
	 *
	 * GET /products/count
	 *
	 * @since 2.0
	 * @param array $args acceptable product endpoint args, like `type` or `filter[]`
	 * @return array|object the count
	 */
	public function get_count( $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => 'count',
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get product reviews
	 *
	 * GET /products/#{product_id}/reviews
	 *
	 * @since 2.0
	 * @param int $id product ID
	 * @param array $args acceptable product reviews endpoint args, currently only `fields`
	 * @return array|object product reviews!
	 */
	public function get_reviews( $id, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $id, 'reviews' ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get a list of product categories or a single product category
	 *
	 * GET /products/categories
	 * GET /products/categories/{#id}
	 *
	 * @since 2.0
	 * @param int $id category ID or null to get all product categories
	 * @param array $args acceptable product categories endpoint args, currently only `fields`
	 * @return array|object product categories!
	 */
	public function get_categories( $id = null, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( 'categories', $id ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/


	/**
	 * Update the stock quantity for a product
	 *
	 * PUT /products/#{id} with stock quantity
	 *
	 * @param int $id order ID
	 * @param int|float $quantity new stock quantity
	 * @return array|object newly-updated product
	 */
	public function update_stock( $id, $quantity ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => $id,
			'body'   => array( 'stock_quantity' => $quantity ),
		) );

		return $this->do_request();
	}


}
