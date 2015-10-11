<?php

namespace Pickens;

class App {
	// original configuration
	protected $config;

	// pickens settings array
	protected $settings;

	public $internal_routes, $alias_map;

	protected $root;

	function __construct( $config ){
		$this->config = $config;
		$this->root = dirname( __DIR__ );

		$this->settings = $this->parseSettings( $config );
		$this->internal_routes = $this->buildInternalRoutes( $this->settings );
		$this->alias_map = $this->buildAliasMap( $this->internal_routes );
	}

	/**
	 * @param $config
	 *
	 * @return Config
	 */
	function parseSettings( $config ){
		$settings = [];

		foreach ( $config->values() as $config_type => $items ) {
			foreach ( $items as $config_item_type => $config_item ) {
				$settings[ $config_type ][ $config_item_type ] = array_replace(
					$config_item,
					[
						'slug' => isset( $config_item['slug'] ) ? $config_item['slug'] : Utils::makeSlug( $config_item['single'] ),
						'features' => isset( $config_item['features'] ) ? Utils::expandCSL( $config_item['features'] ) : [ ],
						'config_type' => $config_type,
						'config_item_type' => $config_item_type,
					]
				);
			}
		}

		return new Config( $settings );
	}

	/**
	 * Build an array of data for all possible internal routes
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	function buildInternalRoutes( $settings ){
		$internal_routes = [];

		// collections contain content items
		foreach ( $settings->collections as $type => $collection ){
			$filedir = "{$this->root}/content/{$collection['slug']}";

			// single items in collection
			foreach( glob( "{$filedir}/*.md" ) as $filepath ){
				$item = new ContentItem( $collection );
				$item->loadFileContents( $filepath );

				// default internal collection item route
				$internal_route = "collection/{$collection['slug']}/{$item->slug}";

				$internal_routes[ $internal_route ] = $item;
			}
		}

		return $internal_routes;
	}

	/**
	 * @param $routes
	 *
	 * @return array
	 */
	function buildAliasMap( $routes ){
		$external_routes_map = [];

		foreach ( $routes as $internal_route => $item ) {
			$external_routes_map[ $item->alias ] = $internal_route;
		}

		return $external_routes_map;
	}

	function getAliasMap(){
		return $this->alias_map;
	}

	function getSettings(){
		return $this->settings;
	}
}