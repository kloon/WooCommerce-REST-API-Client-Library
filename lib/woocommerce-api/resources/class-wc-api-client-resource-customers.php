<?php
/**
 * WC API Client Customers resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Customers extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'customers', 'customer', $client );
	}


	/**
	 * Get customers
	 *
	 * GET /customers
	 * GET /customers/#{id}
	 *
	 * @since 2.0
	 * @param null|int $id customer ID or null to get all customers
	 * @param array $args acceptable customers endpoint args, like `filter[]`
	 * @return array|object customers!
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
	 * Get customer by email
	 *
	 * GET /customers/email/{email}
	 *
	 * @since 2.0
	 * @param string $email customer's email
	 * @param array $args acceptable customer email lookup endpoint args, currently only `fields`
	 * @return array|object customer!
	 */
	public function get_by_email( $email, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( 'email', urlencode( $email ) ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Create a customer
	 *
	 * POST /customers
	 *
	 * @since 2.0
	 * @param array $data valid customer data
	 * @return array|object your newly-created customer
	 */
	public function create( $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update a customer
	 *
	 * PUT /customer/#{id}
	 *
	 * @since 2.0
	 * @param int $id customer ID
	 * @param array $data customer data to update
	 * @return array|object your newly-updated customer
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
	 * Delete a customer
	 *
	 * DELETE /customer/#{id}
	 *
	 * @since 2.0
	 * @param int $id customer ID
	 * @return array|object response
	 */
	public function delete( $id ) {

		$this->set_request_args( array(
			'method' => 'DELETE',
			'path'   => $id,
		) );

		return $this->do_request();
	}


	/**
	 * Get a count of customers
	 *
	 * GET /customers/count
	 *
	 * @since 2.0
	 * @param array $args acceptable customer endpoint args, like `filter[]`
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
	 * Get customer orders
	 *
	 * GET /customers/#{customer_id}/orders
	 *
	 * @since 2.0
	 * @param int $id customer ID
	 * @param array $args acceptable customer orders endpoint args, currently only `fields`
	 * @return array|object customer orders!
	 */
	public function get_orders( $id, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $id, 'orders' ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get customer downloads
	 *
	 * GET /customers/#{customer_id}/downloads
	 *
	 * @since 2.0
	 * @param int $id customer ID
	 * @param array $args acceptable customer downloads endpoint args, currently only `fields`
	 * @return array|object customer downloads!
	 */
	public function get_downloads( $id, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $id, 'downloads' ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/


	// none yet


}
