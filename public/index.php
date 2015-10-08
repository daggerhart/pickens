<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require '../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$config = parse_ini_file( '../app/pickens.ini', true );

// http://www.slimframework.com/docs/features/templates.html
// Create container
$container = new \Slim\Container;

// Register component on container
$container['view'] = function( $c ) {
	$view = new \Slim\Views\Twig('../templates/', [
		//'cache' => '../cache'
	]);
	$view->addExtension(new \Slim\Views\TwigExtension(
		$c['router'],
		$c['request']->getUri()
	));

	return $view;
};
// markdown parsedown
$container['parser'] = new \Parsedown;

$app = new \Slim\App($container);

$app->add( new \Pickens\Collections( $config['collections'] ) );
$app->add( new \Pickens\Taxonomies( $config['taxonomies'] ) );
//$app->add( new \Pickens\Features );
//$app->add( new \Pickens\MetaData );

/**
 * Pages
 */
$app->get('/', function ($request, $response, $args ) use ( $config ){
	$filepath = '../content/pages/index.md';

	if ( file_exists( $filepath ) ){
		$data = get_route_data( $this, $filepath );
		$data['content'] = parse_content( $this, $data['content'] );
		return template_content( $this, $response, $data, $config );
	}

	return $response;
});
$app->get( '/{slug}', function ($request, $response, $args ) use ( $config ){
	$filepath = '../content/pages/'.$args['slug'].'.md';

	if ( file_exists( $filepath ) ){
		$data = get_route_data( $this, $filepath );
		$data['content'] = parse_content( $this, $data['content'] );
		return template_content( $this, $response, $data, $config );
	}

	return $response;

})->setName('page');

/**
 * Posts
 */
// Render Twig template in route
$app->get('/post/{slug}', function ($request, $response, $args)  use ( $config ){
	$filepath = '../content/posts/'.$args['slug'].'.md';

	if ( file_exists( $filepath ) ){
		$data = get_route_data( $this, $filepath );
		$data['content'] = parse_content( $this, $data['content'] );
		return template_content( $this, $response, $data, $config );
	}

	return $response;

})->setName('post');

// Run app
$app->run();

/**
 * Extract metadata and content from a file
 *
 * @param $app
 * @param $filepath
 *
 * @return array|bool
 */
function get_route_data( $app, $filepath ) {
	$file_contents = file_get_contents( $filepath );

	if ( $file_contents ) {

		// get the file contents and extract the meta data from it
		$data = explode( "-----\n", $file_contents );
		$meta = get_meta_data( $data[0] );
		array_shift( $data );
		$content = implode( '', $data );

		return array(
			'meta' => $meta,
			'title' => $meta['title'],
			'description' => $meta['description'],
			'category' => $meta['category'],
			'tags' => $meta['tags'],
			'content' => $content
		);
	}

	return false;
}

/**
 * @param $array
 *
 * @return array
 */
function get_meta_data( $array ){
	$meta = parse_ini_string( $array );
	$meta['category'] = isset( $meta['category'] ) ? $meta['category'] : '';
	$meta['tags'] = isset( $meta['tags'] ) ? explode( ',', $meta['tags'] ) : [];

	return $meta;
}

/**
 * Convert markdown data into html content
 *
 * @param $app
 * @param $content
 *
 * @return mixed
 */
function parse_content( $app, $content ){
	return $app->parser->text( $content );
}

/**
 * Template the content
 *
 * @param $app
 * @param $response
 * @param $data
 *
 * @return mixed
 */
function template_content( $app, $response, $data, $config ){
	return $app->view->render( $response, 'layout.html', [
		'meta' => $data['meta'],
		'title' => $data['meta']['title'],
		'description' => $data['meta']['description'],
		'category' => $data['meta']['category'],
		'tags' => $data['meta']['tags'],
		'content' => $data['content'],
		'data' => print_r( $data, 1 ) . print_r( $config , 1 )
	]);
}