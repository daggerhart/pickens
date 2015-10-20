<?php

// -----------------------------------------------------------------------------
// Debug
// -----------------------------------------------------------------------------
$debug = true;

function d( $v ) {
	global $debug;
	if ( $debug ) {
		dump( $v );
	}
}

if ( $debug ) {
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// -----------------------------------------------------------------------------
// Config / Settings
// -----------------------------------------------------------------------------
define( 'APP_ROOT', __DIR__ . '/..' );
define( 'PUBLIC_ROOT', __DIR__ );

require APP_ROOT . '/vendor/autoload.php';

use Pickens\Settings;

// -----------------------------------------------------------------------------
// App
// -----------------------------------------------------------------------------
$settings = new Settings( APP_ROOT . '/app/settings.yaml' );

// settings go into the app, and get merges with slim settings
// accessible from the container with $c->get('settings');
$app = new \Slim\App( array( 'settings' => $settings->values() ) );

// Set up dependencies
require APP_ROOT . '/app/dependencies.php';

// Register middleware
require APP_ROOT . '/app/middleware.php';

// Register routes
require APP_ROOT . '/app/routes.php';

$app->run();
