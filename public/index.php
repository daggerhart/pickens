<?php


require APP_ROOT . '/vendor/autoload.php';

$debug = true;

function d( $v ) {
	global $debug;
	if ( $debug ) {
		dump( $v );
	}
}

use Symfony\Component\Yaml\Yaml;

$app = new \Slim\Slim();

// pickens internal data source
//$config = new \Pickens\Config( parse_ini_file( APP_ROOT . '/pickens.ini', true ) );
$yml = Yaml::Parse( APP_ROOT . '/app/config/pickens.yml' );
$config = new \Pickens\Config( $yml );

$app->container->config = $config;
$app->container->files = new \Pickens\Files( $config->filesystems['local'] );
$app->container->metadata = new \Pickens\TextMetaData( 'ini', "-----\n\n" );
$app->container->parser = new \Parsedown;

//d($app->container);
// homepage
$app->get( '/', function() {
	include_once PUBLIC_ROOT . "/partials/wrapper.html";
});


/**
 *
 */
$app->get( '/api/get/file/:path+', function( $path ) use ( $app ) {
	$file = $app->container->files->getFile( implode( '/', $path ) );

	if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
		$data = $app->container->metadata->getDataFromFile( $file['absPath'] );

		$data['content']['parsed'] = $app->container->parser->text( $data['content']['noMeta'] );

	    $file['data'] = $data;
	}

	sendJson( $file );
});

/**
 *
 */
$app->post( '/api/update/file', function() use ( $app ) {
	$file = json_decode( $app->request()->getBody() );
	$app->container->files->updateFileContents( $file->relativePath, $file->data->content->edited );

	$file = $app->container->files->getFile( $file->relativePath );

	if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
		$data = $app->container->metadata->getDataFromFile( $file['absPath'] );
		$data['content']['parsed'] = $app->container->parser->text( $data['content']['noMeta'] );

		$file['data'] = $data;
	}

	sendJson( $file );
});

/**
 *
 */
$app->post( '/api/util/preview', function() use ( $app ) {
	$file = json_decode( $app->request()->getBody() );

	if ( $file->mimeType == 'text/x-markdown' && isset( $file->data->content->edited ) ) {

		$data = $app->container->metadata->getData( $file->data->content->edited );
		echo $app->container->parser->text( $data['content']['noMeta'] );
	}
	exit;
});

/**
 *
 */
$app->get( '/api/get/dir/:path+', function( $path ) use ( $app ) {
	$data = $app->container->files->getFiles( implode( '/', $path ) );
	sendJson( array_values( $data ) );
});

/**
 *
 */
$app->get( '/api/get/files', function() use ( $app ) {
	$data = $app->container->files->getRootFolders();

	//d($folders);
	//d($data);
	$files = [];

	/// goofing off with metadata extraction
	foreach ( $data as $path => $file ){
		if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
			$file['data'] = $app->container->metadata->getDataFromFile( $file['absPath'] );
		}

		$files[ $path ] = $file;
	}

	sendJson( array_values( $files ) );
});

$app->run();

/**
 * @param $array
 */
function sendJson( $array ){
	$jsonData = json_encode( $array );

	header("Content-Type: application/json");
	echo $jsonData;
	exit;
}