<?php
/**
 * WC API Client Order Notes resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Order_Notes extends WC_API_Client_Resource {


	/**
	 * Setup the order notes resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'orders', 'order_note', $client );
	}


	/**
	 * Get order notes
	 *
	 * GET /orders/#{order_id}/notes
	 * GET /orders/#{order_id}/notes/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param null|int $id order note ID or null to get all order notes
	 * @param array $args acceptable order endpoint args, like `fields`
	 * @return array|object orders notes!
	 */
	public function get( $order_id, $id = null, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $order_id, 'notes', $id ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Create an order note
	 *
	 * POST /orders/#{order_id}/notes
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param array $data valid order note data
	 * @return array|object your newly-created order note
	 */
	public function create( $order_id, $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'path'   => array( $order_id, 'notes' ),
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update an order note
	 *
	 * PUT /orders/#{order_id}/notes/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param int $id order note ID
	 * @param array $data order note data to update
	 * @return array|object your newly-updated order note
	 */
	public function update( $order_id, $id, $data ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => array( $order_id, 'notes', $id ),
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Delete an order note
	 *
	 * DELETE /orders/#{order_id}/notes/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param int $id order note ID
	 * @return array|object response
	 */
	public function delete( $order_id, $id ) {

		$this->set_request_args( array(
			'method' => 'DELETE',
			'path'   => array( $order_id, 'notes', $id ),
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/

	// none yet


}
