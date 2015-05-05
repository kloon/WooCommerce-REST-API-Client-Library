<?php
/**
 * WC API Client Coupons resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Coupons extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'coupons', 'coupon', $client );
	}


	/**
	 * Get coupons
	 *
	 * GET /coupons
	 * GET /coupons/#{id}
	 *
	 * @since 2.0
	 * @param null|int $id coupon ID or null to get all coupons
	 * @param array $args acceptable coupons endpoint args, like `filter`
	 * @return array|object coupons!
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
	 * Get coupon by code
	 *
	 * GET /coupons/code/{code}
	 *
	 * @since 2.0
	 * @param string $code coupon code
	 * @param array $args acceptable coupon code lookup endpoint args, currently only `fields`
	 * @return array|object coupon!
	 */
	public function get_by_code( $code, $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( 'code', urlencode( $code ) ),
			'params' => $args,
		) );

		return $this->do_request();
	}



	/**
	 * Create a coupon
	 *
	 * POST /coupons
	 *
	 * @since 2.0
	 * @param array $data valid coupon data
	 * @return array|object your newly-created coupon
	 */
	public function create( $data ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Update a coupon
	 *
	 * PUT /coupon/#{id}
	 *
	 * @since 2.0
	 * @param int $id coupon ID
	 * @param array $data coupon data to update
	 * @return array|object your newly-updated coupon
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
	 * Delete a coupon
	 *
	 * DELETE /coupons/#{id}
	 *
	 * @since 2.0
	 * @param int $id coupon ID
	 * @param bool $force true to permanently delete the coupon, false to trash it
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
	 * Get a count of coupons
	 *
	 * GET /coupons/count
	 *
	 * @since 2.0
	 * @param array $args acceptable coupon endpoint args, like `filter[]`
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


	/** Convenience methods - these do not map directly to an endpoint ********/


	// none yet


}
