<?php

namespace Pickens;

class TextMetaData {
	// ini | json
	public $format;
	public $delimiter;

	function __construct( $format, $delimiter ){
		$this->format = $format;
		$this->delimiter = $delimiter;
	}

	/**
	 * @param $filepath
	 *
	 * @return bool|string
	 */
	function getFileContents( $filepath ){
		if ( file_exists( $filepath ) ){
			return file_get_contents( $filepath );
		}

		return false;
	}

	/**
	 * @param $filepath
	 *
	 * @return array|bool
	 */
	function getDataFromFile( $filepath ){
		$contents = $this->getFileContents( $filepath );

		if ( $contents ) {
			return $this->getData( $contents );
		}

		return false;
	}

	/**
	 * @param $file_contents
	 *
	 * @return array|bool
	 */
	function getData( $file_contents ){
		if ( $file_contents ){
			$data = explode( $this->delimiter, $file_contents );

			$meta = $this->parseMetaData( $this->format, $data[0] );

			// if no metadata array was found, then the whole thing is content
			if ( is_array( $meta ) ){
				array_shift( $data );
			}
			// otherwise, remove the meta data from the content
			else {
				$meta = [];
			}

			return array(
				'meta' => $meta,
				'content' => array(
					'full' => $file_contents,
					'noMeta' => implode( $this->delimiter, $data ),
				),
			);
		}

		return false;
	}

	/**
	 * @param $format
	 * @param $data
	 *
	 * @return array|bool|mixed
	 */
	function parseMetaData( $format, $data ){
		if ( $format === 'ini' ){
			return @parse_ini_string( $data );
		}
		else if ( $format === 'json' ){
			return json_decode( $data );
		}
		else {
			return false;
		}
	}
}