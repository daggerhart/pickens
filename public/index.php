<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require '../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// pickens internal data source
$config = new \Pickens\Config( parse_ini_file( '../app/pickens.ini', true ) );
$pickens = new \Pickens\App( $config );


// http://www.slimframework.com/docs/features/templates.html
// Create container
$container = new \Slim\Container;

$container['pickens'] = $pickens;

// markdown parsedown
$container['parser'] = new \Parsedown;

// twig for templating
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

$app = new \Slim\App($container);

// collections and taxonomies can set their own alias_pattern
foreach( $pickens->getAliasMap() as $alias => $internal ){

	$app->get( '/' . $alias, function ( $request, $response, $args ) use ( $pickens, $internal ) {
		$item = $pickens->internal_routes[ $internal ];

		if ( $item ){
			$item->content = $this->parser->text( $item->content_raw );

			return $this->view->render( $response, 'layout.html', (array) $item );
		}

		return $response;
	} );
}

$app->run();
