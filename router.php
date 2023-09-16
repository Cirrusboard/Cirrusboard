<?php
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = explode('/', $uri);

if ($path[1] == 'rss') $rss = true;

require('lib/common.php');

function fallback() {
	global $uri;

	if (str_contains($uri, '.php')) {
		header("Location: ".str_replace('.php', '', $uri), true, 301);
		die();
	}

	error('404');
}

if ($path[1]) {
	if (file_exists('pages/'.$path[1].'.php'))
		require('pages/'.$path[1].'.php');
	elseif ($path[1] == 'credits')
		twigloader()->display('credits.twig');
	else
		fallback();
} else
	require('pages/index.php');
