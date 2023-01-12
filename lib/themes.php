<?php

function getThemeInfo($id) {
	$path = 'themes/'.$id.'/meta.json';

	if (!file_exists($path))
		return ['name' => $id.' (missing meta!)', 'author' => 'N/A'];

	$info = json_decode(file_get_contents($path), true);

	return $info;
}

function themeList() {
	$themes = glob('themes/*', GLOB_ONLYDIR);
	sort($themes);

	foreach ($themes as $theme) {
		$id = explode("/", $theme)[1];
		$info = getThemeInfo($id);
		$list[$id] = sprintf('%s (%s)', $info['name'], $info['author']);
	}

	return $list;
}

function isValidTheme($id) {
	return file_exists('themes/'.$id.'/style.css');
}
