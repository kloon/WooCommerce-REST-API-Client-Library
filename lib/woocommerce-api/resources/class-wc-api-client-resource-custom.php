<?php
/**
 * WC API Client Custom resource class
 *
 * @since 2.0
 */
class WC_API_Client_Resource_Custom extends WC_API_Client_Resource {


	/**
	 * Setup the resource
	 *
	 * @since 2.0
	 * @param WC_API_Client $client class instance
	 */
	public function __construct( $client ) {

		parent::__construct( '', '', $client );
	}


	/**
	 * Setup the custom resource, pass your custom endpoint (e.g. `orders`) and
	 * object namespace (e.g. `order`) here prior to making any API calls
	 *
	 * @since 2.0
	 * @param string $endpoint
	 * @param string $object_namespace
	 */
	public function setup( $endpoint, $object_namespace = '' ) {

		$this->endpoint = $endpoint;
		$this->object_namespace = $object_namespace;
	}


	/**
	 * Perform a GET request to the custom endpoint
	 *
	 * Path can either be:
	 *
	 * string (your_string) - GET /endpoint/your_string
	 * array (1,2,3) - GET /endpoint/1/2/3
	 *
	 * Params are added after the endpoint
	 *
	 * @since 2.0
	 * @param string|array $path request path
	 * @param array $params request params
	 * @return array|object custom resource
	 */
	public function get( $path, $params = array() ) {

		$this->set_request_args( array(
			'method' => 'GET',
			'path'   => $path,
			'params' => $params,
		) );

		return $this->do_request();
	}


	/**
	 * Perform a POST request to the custom endpoint
	 *
	 * Path can either be:
	 *
	 * string (your_string) - GET /endpoint/your_string
	 * array (1,2,3) - GET /endpoint/1/2/3
	 *
	 * Params are added after the endpoint
	 *
	 * @since 2.0
	 * @param string|array $path request path
	 * @param array $data request body entity to be JSON encoded
	 * @param array $params request params
	 * @return array|object custom resource
	 */
	public function post( $path, $data, $params = array() ) {

		$this->set_request_args( array(
			'method' => 'POST',
			'path'   => $path,
			'params' => $params,
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Perform a PUT request to the custom endpoint
	 *
	 * Path can either be:
	 *
	 * string (your_string) - GET /endpoint/your_string
	 * array (1,2,3) - GET /endpoint/1/2/3
	 *
	 * Params are added after the endpoint
	 *
	 * @since 2.0
	 * @param string|array $path request path
	 * @param array $data request body entity to be JSON encoded
	 * @param array $params request params
	 * @return array|object custom resource
	 */
	public function put( $path, $data, $params = array() ) {

		$this->set_request_args( array(
			'method' => 'PUT',
			'path'   => $path,
			'params' => $params,
			'body'   => $data,
		) );

		return $this->do_request();
	}


	/**
	 * Perform a DELETE request to the custom endpoint
	 *
	 * Path can either be:
	 *
	 * string (your_string) - GET /endpoint/your_string
	 * array (1,2,3) - GET /endpoint/1/2/3
	 *
	 * Params are added after the endpoint
	 *
	 * @since 2.0
	 * @param string|array $path request path
	 * @param array $params request params
	 * @return array|object custom resource
	 */
	public function delete( $path, $params = array() ) {

		$this->set_request_args( array(
			'method' => 'DELETE',
			'path'   => $path,
			'params' => $params,
		) );

		return $this->do_request();
	}


}
