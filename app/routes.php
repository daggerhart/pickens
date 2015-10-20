<?php

// -----------------------------------------------------------------------------
// Routes
// -----------------------------------------------------------------------------
/**
 * Homepage
 */
$app->get('/', function( $request, $response, $args ) {
	$this->files;
	return $response->write( file_get_contents( PUBLIC_ROOT . "/partials/wrapper.html" ) );
});
/**
 * Root Folders
 */
$app->get('/api/root/get', function( $request, $response, $args ) {
	$files = $this->files->getDirFiles( '' );
	return $response
		->withHeader('Content-type', 'application/json')
		->write( json_encode( array_values( $files ) ) );
});
/**
 * Files
 */
// Get
$app->get('/api/file/get/{path:.*}', function( $request, $response, $args ) {
	$file = $this->files->getFile( $args['path'] );

	if ( $file['isFile'] && $file['Mimetype'] == 'text/x-markdown' ) {
		$data = $this->metadata->getDataFromFile( $file['RealPath'] );
		$data['content']['parsed'] = $this->markdown->text( $data['content']['noMeta'] );
		$file['data'] = $data;
	}

	return $response
		->withHeader('Content-type', 'application/json')
		->write( json_encode( $file ) );
});
// Update
$app->post('/api/file/update', function( $request, $response, $args ) {

	$newfile = json_decode( $request->getBody() );
	$this->files->updateFileContents( $newfile->RelativePathname, $newfile->data->content->edited );
	$file = $this->files->getFile( $newfile->RelativePathname );

	if ( $file['isFile'] && $file['Mimetype'] == 'text/x-markdown' ) {
		$data = $this->metadata->getDataFromFile( $file['RealPath'] );
		$data['content']['parsed'] = $this->markdown->text( $data['content']['noMeta'] );
		$file['data'] = $data;
	}

	return $response
		->withHeader('Content-type', 'application/json')
		->write( json_encode( $file ) );
});
// Preview
$app->post('/api/file/preview', function( $request, $response, $args ) {
	$file = json_decode( $request->getBody() );

	if ( $file->Mimetype == 'text/x-markdown' && isset( $file->data->content->edited ) ) {
		$data = $this->metadata->getData( $file->data->content->edited );
		return $response
			->withHeader('Content-type', 'application/json')
			->write( json_encode( [
				'html' => $this->markdown->text( $data['content']['noMeta'] )
			]));
	}
});

/**
 * Directory and its Files
 */
$app->get('/api/dir/get/{path:.*}', function( $request, $response, $args ) {
	$dir = $this->files->getFile( $args['path'] );
	$files = $this->files->getDirFiles( $args['path'] );
	return $response
		->withHeader('Content-type', 'application/json')
		->write( json_encode( [
			'dir' => $dir,
			'files' => $files
		]));
});
