<?php

$ranks = [
	-1 => __('Banned'),
	//0  => 'Guest',
	1  => __('Member'),
	2  => __('Moderator'),
	3  => __('Administrator'),
	4  => __('Root'),
];

$rankColours = [
	-1 => '9d9d9d',
	0  => 'ffffff',
	1  => '5eb2fb',
	2  => '4fe840',
	3  => 'e34d4d',
	4  => 'ffd21b'
];

function rankIdToName($id) {
	global $ranks;
	return $ranks[$id] ?? 'N/A';
}

function rankIdToColour($id) {
	global $rankColours;
	return $rankColours[$id] ?? '';
}

function needsLogin() {
	global $log;
	if (!$log) {
		error('403', __('This page requires login.').'<p><a href="login">'.__('Log in').'</a></p>');
		die();
	}
}
