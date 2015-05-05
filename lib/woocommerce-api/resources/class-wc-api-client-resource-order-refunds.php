<?php
/**
 * WC API Client Order Refunds resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Order_Refunds extends WC_API_Client_Resource {


	/**
	 * Setup the order refunds resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'orders', 'order_refund', $client );
	}


	/**
	 * Get order refunds
	 *
	 * GET /orders/#{order_id}/refunds
	 * GET /orders/#{order_id}/refunds/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param null|int $id order refund ID or null to get all order refunds
	 * @param array $args acceptable order refund endpoint args, like `fields`
	 * @return array|object order refunds!
	 */
	public function get( $order_id, $id = null, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $order_id, 'refunds', $id ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Create an order refund
	 *
	 * POST /orders/#{order_id}/refunds
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param array $data valid order refund data
	 * @return array|object your newly-created order refund
	 */
	public function create( $order_id, $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'path'   => array( $order_id, 'refunds' ),
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update an order refund
	 *
	 * PUT /orders/#{order_id}/refunds/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param int $id order refund ID
	 * @param array $data order refund data to update
	 * @return array|object your newly-updated order refund
	 */
	public function update( $order_id, $id, $data ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => array( $order_id, 'refunds', $id ),
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Delete an order refund
	 *
	 * DELETE /orders/#{order_id}/refunds/#{id}
	 *
	 * @since 2.0
	 * @param int $order_id order ID
	 * @param int $id order refund ID
	 * @return array|object response
	 */
	public function delete( $order_id, $id ) {

		$this->set_request_args( array(
			'method' => 'DELETE',
			'path'   => array( $order_id, 'refunds', $id ),
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/

	// none yet


}
