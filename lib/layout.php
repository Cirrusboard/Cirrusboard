<?php

/**
 * Twig loader, initializes Twig with standard configurations and extensions.
 *
 * @return \Twig\Environment Twig object.
 */
function twigloader($subfolder = '') {
	global $tplCache, $tplNoCache, $config, $log, $userdata, $uri;

	$doCache = ($tplNoCache ? false : $tplCache);

	$loader = new \Twig\Loader\FilesystemLoader('templates/'.$subfolder);

	$twig = new \Twig\Environment($loader, [
		'cache' => $doCache,
	]);

	$twig->addExtension(new ForumExtension());

	$twig->addGlobal('config', $config);

	$twig->addGlobal('log', $log);
	$twig->addGlobal('userdata', $userdata);

	$twig->addGlobal('uri', $uri);
	$twig->addGlobal('domain', (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']);
	$twig->addGlobal('pagename', substr($_SERVER['PHP_SELF'], 0, -4));

	return $twig;
}

function error($title, $message) {
	echo twigloader()->render('_error.twig', ['err_title' => $title, 'err_message' => $message]);
	die();
}

function threadpost($post) {
	global $userdata;

	if (isset($post['minread']) and $post['minread'] > $userdata['rank']) {
		return '<table class="c1 threadpost"><tr><td class="n1 center">(post in restricted forum)</td></tr></table>';
	} else {
		return twigloader('components')->render('threadpost.twig', [
			'post' => $post
		]);
	}
}

function postform($action, $name, $postTitle, $postMessage, $editableTitle) {
	return twigloader('components')->render('postform.twig', [
		'action' => $action,
		'name' => $name,
		'post_title' => $postTitle,
		'post_message' => $postMessage,
		'editable_title' => $editableTitle
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

function esc($text) {
	return htmlspecialchars($text);
}

function fieldinput($name, $value, $size, $max, $placeholder = '', $type = '') {
	return sprintf('<input type="%s" name="%s" size="%s" maxlength="%s" value="%s"%s>',
		($type ?: 'text'), $name, $size, $max, esc($value),
		($placeholder ? ' placeholder="'.$placeholder.'"' : ''));
}

function fieldtextarea($name, $value, $rows, $cols) {
	return sprintf('<textarea name="%s" rows=%s cols=%s>%s</textarea>',
		$name, $rows, $cols, esc($value));
}

function fieldcheckbox($name, $checked, $label) {
	return sprintf('<label><input type="checkbox" name="%s" value="1" %s> %s</label>', $name, ($checked ? ' checked' : ''), $label);
}

function fieldselect($name, $selected, $choices) {
	$text = '';
	foreach ($choices as $k => $v)
		$text .= sprintf('<option value="%s"%s>%s</option>', $k, ($k == $selected ? ' selected' : ''), $v);

	return sprintf('<select name="%s" id="%s">%s</select>', $name, $name, $text);
}

function threadStatus($type) {
	if (!$type) return '';

	$text = match ($type) {
		'n'  => 'NEW',
		'o'  => 'OFF',
		'on' => 'OFF'
	};
	$statusimg = match ($type) {
		'n'  => 'new.png',
		'o'  => 'off.png',
		'on' => 'offnew.png'
	};

	return "<img src=\"assets/status/$statusimg\" alt=\"$text\">";
}
