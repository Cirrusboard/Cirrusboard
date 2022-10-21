<?php

function userlink($user, $pre = '') {
	return sprintf(
		'<a href="profile.php?id=%d">%s</a>',
	$user[$pre.'id'], userlabel($user, $pre));
}

function userlabel($user, $pre = '') {
	return sprintf(
		'<span style="color:#4f77ff;">%s</span>',
	htmlspecialchars($user[$pre.'name'] ?? 'null'));
}

function postfields_user() {
	$fields = ['joined', 'posts'];
	$str = '';
	foreach ($fields as $field)
		$str .= "u.$field u_$field,";

	return $str;
}
