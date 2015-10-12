<?php

// http://stackoverflow.com/a/32098723/559923

$_SERVER['SCRIPT_NAME'] = 'index.php';
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

define( 'APP_ROOT', __DIR__ );
define( 'PUBLIC_ROOT', APP_ROOT . '/public' );

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
	return false;    // serve the requested resource as-is.
}

include PUBLIC_ROOT . '/index.php';

//return false;
