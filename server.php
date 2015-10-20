<?php

// http://stackoverflow.com/a/32098723/559923
$_SERVER['SCRIPT_NAME'] = 'index.php';

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
	return false;    // serve the requested resource as-is.
}

include __DIR__ . '/public/index.php';
