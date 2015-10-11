<?php

namespace Pickens;

class ContentItem {

	public $meta = array(),
		$title,
		$description,
		$slug,
		$content_raw,
		$content,
		$type,
		$type_slug,
		$alias,
		$filename,
		$filepath,
		$filedir,
		$file_slug;

	function __construct( $type ){
		$this->type = $type;
		$this->type_slug = $type['slug'];
	}

	function loadFileContents( $filepath ){
		$filename = basename( $filepath );

		// file_slug = (filename without extension)
		$file_slug = rtrim( $filename, '.md' );
		$file_contents = file_get_contents( $filepath );

		if ( $file_contents ) {
			// get the file contents and extract the meta data from it
			$data = explode( "-----\n", $file_contents );
			$meta = $this->parseMetaData( $data[0] );
			array_shift( $data );

			$meta['content_raw'] = implode( "-----\n", $data );
			$meta['slug'] = isset( $meta['slug'] ) ? $meta['slug'] : $file_slug;
			$meta['filedir'] = dirname( $filepath );
			$meta['filename'] = $filename;
			$meta['filepath'] = $filepath;
			$meta['file_slug'] = $file_slug;

			$this->updateMetaData( $meta );
			$this->updateItemAlias();
		}
	}

	/**
	 * Extract the baseline meta data for this content
	 *
	 * @param $array
	 *
	 * @return array
	 */
	function parseMetaData( $array ){
		$meta = parse_ini_string( $array );
		return $meta;
	}

	/**
	 * Expand the passed in array as the
	 *
	 * @param $meta
	 */
	function updateMetaData( $meta ){
		$this->meta = array_replace( $this->meta, $meta );

		foreach( $this->meta as $key => $value ){
			$this->{$key} = $value;
		}
	}


	/**
	 * Full route to content can be determined in a few ways.
	 *
	 * 1. Content can describe an entire route by using the "path" meta data
	 * 2. Collection provided uri_pattern
	 * 3. Collection slug followed by content slug
	 *
	 * @return string
	 */
	function updateItemAlias(){

		$replacements = array(
			'{item_slug}' => $this->slug,
			'{type_slug}' => $this->type_slug,
		);

		if ( isset( $this->alias ) ) {
			$alias = $this->alias;
		}
		else if ( isset( $this->type['alias_pattern'] ) ) {
			$alias = strtr( $this->type['alias_pattern'], $replacements );
		}
		else {
			$alias = strtr( '{type_slug}/{item_slug}', $replacements );
		}

		$this->alias = ltrim( $alias, '/' );
	}
}