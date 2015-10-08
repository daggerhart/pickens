<?php

namespace Pickens;

class Collections {
	protected $config;
	protected $values;

	/**
	 * @param $collections - array
	 */
	function __construct( $collections ){
		$this->config = $collections;
		$this->values = $this->loadCollections( $collections );

		print_r($this->values);
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
	 * @param $collections
	 *
	 * @return array
	 */
	function loadCollections( $collections ){
		$values = [];
		foreach ( $collections as $collection ){
			$slug = isset( $collection['slug'] ) ? $collection['slug'] : Utils::makeSlug( $collection['plural'] );

			$values[ $slug ] = array_replace(
				$collection,
				array(
					'slug' => $slug,
					'features' => isset( $collection['features'] ) ? Utils::expandCSL( $collection['features'] ) : [],
				)
			);
		}

		return $values;
	}
}