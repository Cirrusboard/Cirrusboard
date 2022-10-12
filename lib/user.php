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
