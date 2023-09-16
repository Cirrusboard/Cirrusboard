<?php

$ranks = [
	-1 => 'Banned',
	//0  => 'Guest',
	1  => 'Member',
	2  => 'Moderator',
	3  => 'Administrator',
	4  => 'Root',
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
		error('403', 'This page requires login. <p><a href="login">Login</a></p>');
		die();
	}
}
