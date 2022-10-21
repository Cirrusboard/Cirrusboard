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

function threadpost($post) {
	$twig = twigloader('components');

	return $twig->render('threadpost.twig', [
		'post' => $post
	]);
}

class ForumExtension extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		global $profiler;
		return [
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			}),

			new \Twig\TwigFunction('userlink', 'userlink', ['is_safe' => ['html']]),
			new \Twig\TwigFunction('threadpost', 'threadpost', ['is_safe' => ['html']]),
		];
	}

	public function getFilters() {
		return [
			new \Twig\TwigFilter('postfilter', 'postfilter', ['is_safe' => ['html']]),
		];
	}
}

function redirect($url) {
	header(sprintf('Location: %s', $url));
	die();
}

