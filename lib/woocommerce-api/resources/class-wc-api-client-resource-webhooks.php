<?php
/**
 * WC API Client Webhooks resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Webhooks extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'webhooks', 'webhook', $client );
	}


	/**
	 * Get webhooks
	 *
	 * GET /webhooks
	 * GET /webhooks/#{id}
	 *
	 * @since 2.0
	 * @param null|int $id webhook ID or null to get all webhooks
	 * @param array $args acceptable webhooks endpoint args, like `status`
	 * @return array|object webhooks!
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
	 * Create a webhook
	 *
	 * POST /webhooks
	 *
	 * @since 2.0
	 * @param array $data valid webhook data
	 * @return array|object your newly-created webhook
	 */
	public function create( $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update a webhook
	 *
	 * PUT /webhook/#{id}
	 *
	 * @since 2.0
	 * @param int $id webhook ID
	 * @param array $data webhook data to update
	 * @return array|object your newly-updated webhook
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
	 * Delete a webhook
	 *
	 * DELETE /webhook/#{id}
	 *
	 * @since 2.0
	 * @param int $id webhook ID
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
	 * Get a count of webhooks
	 *
	 * GET /webhooks/count
	 *
	 * @since 2.0
	 * @param array $args acceptable webhook endpoint args, like `status`
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
	 * Get webhook deliveries
	 *
	 * GET /webhooks/#{webhook_id}/deliveries
	 *
	 * @since 2.0
	 * @param int $id webhook ID
	 * @param array $args acceptable webhook delivery endpoint args, currently only `fields`
	 * @return array|object webhook deliveries!
	 */
	public function get_deliveries( $id, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $id, 'deliveries' ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get a webhook delivery
	 *
	 * GET /webhooks/#{webhook_id}/deliveries/#{id}
	 *
	 * @since 2.0
	 * @param int $webhook_id webhook ID
	 * @param int $id webhook delivery ID
	 * @param array $args acceptable webhook delivery endpoint args, currently only `fields`
	 * @return array|object webhook delivery!
	 */
	public function get_delivery( $webhook_id, $id, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( $webhook_id, 'deliveries', $id ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/


	// none yet


}
