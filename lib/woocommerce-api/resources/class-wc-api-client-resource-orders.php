<?php
/**
 * WC API Client Orders resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Orders extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'orders', 'order', $client );
	}


	/**
	 * Get orders
	 *
	 * GET /orders
	 * GET /orders/#{id}
	 *
	 * @since 2.0
	 * @param null|int $id order ID or null to get all orders
	 * @param array $args acceptable order endpoint args, like `status`
	 * @return array|object orders!
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
	 * Create an order
	 *
	 * POST /orders
	 *
	 * @since 2.0
	 * @param array $data valid order data
	 * @return array|object your newly-created order
	 */
	public function create( $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update an order
	 *
	 * PUT /orders/#{id}
	 *
	 * @since 2.0
	 * @param int $id order ID
	 * @param array $data order data to update
	 * @return array|object your newly-updated order
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
	 * Delete an order
	 *
	 * DELETE /orders/#{id}
	 *
	 * @since 2.0
	 * @param int $id order ID
	 * @param bool $force true to permanently delete the order, false to trash it
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
	 * Get a count of orders
	 *
	 * GET /orders/count
	 *
	 * @since 2.0
	 * @param array $args acceptable order endpoint args, like `status`
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
	 * Get a list of valid order statuses
	 *
	 * GET /orders/statuses
	 *
	 * @since 2.0
	 * @return array|object order statuses
	 */
	public function get_statuses() {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => 'statuses',
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/


	/**
	 * Update the status for an order
	 *
	 * PUT /orders/#{id} with status
	 *
	 * @param int $id order ID
	 * @param string $status valid order status
	 * @return array|object newly-updated order
	 */
	public function update_status( $id, $status ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => $id,
			'body'   => array( 'status' => $status ),
		) );

		return $this->do_request();
	}


}
