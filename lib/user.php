<?php

function authenticateCookie() {

	if (isset($_COOKIE['token'])) {
		$id = result("SELECT id FROM users WHERE token = ?", [$_COOKIE['token']]);

		if ($id) // Valid cookie, user is logged in.
			return $id;
	}

	return -1;
}

function userlink($user, $pre = '') {
	return sprintf(
		'<a href="profile?id=%d">%s</a>',
	$user[$pre.'id'], userlabel($user, $pre));
}

function userlabel($user, $pre = '') {
	return sprintf(
		'<span style="color:#%s">%s</span>',
	rankIdToColour($user[$pre.'rank']), htmlspecialchars($user[$pre.'name'] ?? 'null'));
}

function userfields($prefix = 'u') {
	$fields = ['id', 'name', 'rank'];

	$out = '';
	foreach ($fields as $field)
		$out .= sprintf('%s.%s %s_%s,', $prefix, $field, $prefix, $field);

	return $out;
}

function postfields_user() {
	$fields = ['joined', 'posts', 'title', 'avatar', 'header', 'signature', 'signsep'];
	$str = '';
	foreach ($fields as $field)
		$str .= "u.$field u_$field,";

	return $str;
}
