<?php
/**
 * WC API Client Reports resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Reports extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'reports', '', $client );
	}


	/**
	 * Get list of reports
	 *
	 * GET /reports
	 *
	 * @since 2.0
	 * @return array|object list of reports!
	 */
	public function get() {

		$this->set_request_args( array(
			'method' => 'GET',
		) );

		return $this->do_request();
	}


	/**
	 * Get Sales report
	 *
	 * GET /reports/sales
	 *
	 * @since 2.0
	 * @param array $args acceptable reports endpoint args, like `filter[period]`
	 * @return array|object sales report!
	 */
	public function get_sales( $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => 'sales',
			'params' => $args,
		) );

		return $this->do_request();
	}


	/**
	 * Get Top Sellers report
	 *
	 * GET /reports/sales/top_sellers
	 *
	 * @since 2.0
	 * @param array $args acceptable reports endpoint args, like `filter[period]`
	 * @return array|object sales report!
	 */
	public function get_top_sellers( $args = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => array( 'sales', 'top_sellers' ),
			'params' => $args,
		) );

		return $this->do_request();
	}


	/** Convenience methods - these do not map directly to an endpoint ********/


	// none yet


}
