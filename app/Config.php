<?php

namespace Pickens;

class Config {
	protected $values = array();

	function __construct( $values ){
		$this->values = $values;
	}

	function __get( $key ){
		return isset( $this->values[ $key ] ) ? $this->values[ $key ] : null;
	}

	function __isset( $key ){
		return isset( $this->values[ $key ] );
	}

	function values(){
		return $this->values;
	}
}

