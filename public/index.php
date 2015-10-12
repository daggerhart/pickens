<?php


require APP_ROOT . '/vendor/autoload.php';


function d(){
	foreach( func_get_args() as $i => $a ){
		var_dump( $a );
	}
}

$app = new \Slim\Slim();

// pickens internal data source
$config = new \Pickens\Config( parse_ini_file( APP_ROOT . '/pickens.ini', true ) );

$app->container->config = $config;
$app->container->filesystem = new \Pickens\FileSystem( $config->local['root'], $config->local['ignore'] );
$app->container->metadata = new \Pickens\TextMetaData( 'ini', "-----\n\n" );
$app->container->parser = new \Parsedown;

// homepage
$app->get( '/', function() {
	include_once PUBLIC_ROOT . "/partials/wrapper.html";
});


/**
 *
 */
$app->get( '/api/get/file/:path+', function( $path ) use ( $app ) {
	$relativePath = '/' . ltrim( implode( '/', $path ), '/' );
	$file = $app->container->filesystem->getFile( $relativePath );

	if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
		$data = $app->container->metadata->getDataFromFile( $file['absPath'] );

		$data['content']['parsed'] = $app->container->parser->text( $data['content']['noMeta'] );

	    $file['data'] = $data;
	}

	$jsonData = json_encode( $file );

	header("Content-Type: application/json");
	echo $jsonData;
	exit;
});

/**
 *
 */
$app->post( '/api/update/file', function() use ( $app ) {
	$file = json_decode( $app->request()->getBody() );
	$app->container->filesystem->updateFileContents( $file->relativePath, $file->data->content->edited );

	$file = $app->container->filesystem->getFile( $file->relativePath );

	if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
		$data = $app->container->metadata->getDataFromFile( $file['absPath'] );
		$data['content']['parsed'] = $app->container->parser->text( $data['content']['noMeta'] );

		$file['data'] = $data;
	}

	$jsonData = json_encode( $file );

	header("Content-Type: application/json");
	echo $jsonData;

	exit;
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
$app->get( '/api/get/dir/:path', function( $path ) use ( $app ) {
	d($path);
	exit;
});

/**
 *
 */
$app->get( '/api/get/files', function() use ( $app ) {
	$data = $app->container->filesystem->getFiles( $app->container->config->local['folders'] );

	$files = [];

	/// goofing off with metadata extraction
	foreach ( $data as $path => $file ){
		if ( $file['isFile'] && $file['mimeType'] == 'text/x-markdown' ) {
			$file['data'] = $app->container->metadata->getDataFromFile( $file['absPath'] );
		}

		$files[ $path ] = $file;
	}

	$jsonData = json_encode( array_values( $files ) );

	header("Content-Type: application/json");
	echo $jsonData;
	exit;
});

$app->run();
