<?php

function userlink($user, $pre = '') {
	return sprintf(
		'<a href="profile.php?id=%d">%s</a>',
	$user[$pre.'id'], userlabel($user, $pre));
}

function userlabel($user, $pre = '') {
	return sprintf(
		'<span style="color:#%s;">%s</span>',
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
