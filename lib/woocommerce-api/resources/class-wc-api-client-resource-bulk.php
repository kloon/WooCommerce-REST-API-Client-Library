<?php
/**
 * WC API Client Bulk resource class
 *
 * @since 3.0
 */
class WC_API_Client_Resource_Bulk extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 3.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( 'products', 'products', $client );
	}

	
	/**
	 * Bulk update/create products
	 *
	 * POST /products/bulk
	 *
	 * @since 3.0
	 * @param array $data valid products data
	 * @return array|object your newly-created product
	 */
	public function send( $data ) {

    $this->set_request_args( array(
        'method' => 'PUT',
        'body'   => $data,
        'path'   => 'bulk',
    ) );

    return $this->do_request();
}


}