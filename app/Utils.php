<?php

namespace Pickens;

class Utils {

	/**
	 * Allow: "_" and lower alphanumeric
	 *
	 * @param $string
	 *
	 * @return string
	 */
	static function makeSlug( $string ){
		$string = strtolower( str_replace( array( '-', ' ' ), '_', $string ) );
		return preg_replace("/[^A-Za-z0-9 ]/", '', $string);
	}

	/**
	 * Convert a comma separated list into an array
	 *
	 * @param $csl array - comma separated list
	 *
	 * @return array
	 */
	static function expandCSL( $csl ){
		$list = explode( ',', $csl );
		return array_map( 'trim', $list );
	}
}