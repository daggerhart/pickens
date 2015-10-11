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

	static function d( $v, $return = false, $show_caller = true ){
		$dd = debug_backtrace();
		array_shift( $dd );
		$caller = array_shift( $dd );
		unset($caller['object'], $caller['args'], $caller['type']);

		$output =  $show_caller ? "<pre>Debug Caller:".print_r($caller,1)."</pre>" : '';
		$output.= "<pre>".print_r($v,1)."</pre>";

		if ( $return ){
			return $output;
		}

		echo $output;
	}
}