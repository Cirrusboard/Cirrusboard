<?php

/**
 * Twig loader, initializes Twig with standard configurations and extensions.
 *
 * @return \Twig\Environment Twig object.
 */
function twigloader($subfolder = '') {
	global $tplCache, $tplNoCache, $config, $log, $userdata;

	$doCache = ($tplNoCache ? false : $tplCache);

	$loader = new \Twig\Loader\FilesystemLoader('templates/'.$subfolder);

	$twig = new \Twig\Environment($loader, [
		'cache' => $doCache,
	]);

	$twig->addExtension(new ForumExtension());

	$twig->addGlobal('config', $config);

	$twig->addGlobal('log', $log);
	$twig->addGlobal('userdata', $userdata);

	return $twig;
}

function error($title, $message) {
	echo twigloader()->render('_error.twig', ['err_title' => $title, 'err_message' => $message]);
	die();
}

function threadpost($post) {
	return twigloader('components')->render('threadpost.twig', [
		'post' => $post
	]);
}

function pagination($levels, $lpp, $url, $current) {
	return twigloader('components')->render('pagination.twig', [
		'levels' => $levels, 'lpp' => $lpp, 'url' => $url, 'current' => $current
	]);
}

function redirect($url) {
	header(sprintf('Location: %s', $url));
	die();
}
