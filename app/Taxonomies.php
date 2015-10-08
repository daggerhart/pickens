<?php

namespace Pickens;

class Taxonomies {
	protected $config;
	protected $values;

	/**
	 * @param $taxonomies - array
	 */
	function __construct( $taxonomies ) {
		$this->config = $taxonomies;
		$this->values = $this->loadTaxonomies( $taxonomies );

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

	function loadTaxonomies( $taxonomies ) {
		$values = [];
		return $values;
	}
}
