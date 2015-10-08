<?php

namespace Pickens;

class Features {
	protected $config;
	protected $values;

	/**
	 * @param $config - array
	 */
	function __construct( $config ) {
		$this->config = $config;
		$this->values = $this->loadFeatures( $config );

		print_r( $this->values );
	}

	/**
	 * @param $request
	 * @param $response
	 * @param $next
	 *
	 * @return mixed
	 */
	function __invoke( $request, $response, $next ){
		return $next( $request, $response );
	}

	/**
	 * @param $config
	 *
	 * @return array
	 */
	function loadFeatures( $config ) {
		$values = [];
		return $values;
	}
}
