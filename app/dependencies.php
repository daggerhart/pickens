<?php

use Pickens\Files;
use Pickens\TextMetaData;

// -----------------------------------------------------------------------------
// Dependencies
// -----------------------------------------------------------------------------
$c = $app->getContainer();

// local filesystem
$c['files'] = function( $c ) {
	$settings = $c->get('settings');
	return new Files( $settings['filesystems']['local'], APP_ROOT );
};

// text metadata extractor
$c['metadata'] = function( $c ) {
	return new TextMetaData( 'yaml', "\n--meta\n" );
};

// markdown parser
$c['markdown'] = function( $c ){
	return new Parsedown;
};
