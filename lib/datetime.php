<?php

function relTime($time) {
	if ($time === null) return 'never';

	$relativeTime = new \RelativeTime\RelativeTime([
		'language' => '\RelativeTime\Languages\English',
		'separator' => ', ',
		'suffix' => true,
		'truncate' => 1,
	]);

	return $relativeTime->timeAgo($time);
}

function dateformat($time) {
	return date('Y-m-d H:i', $time);
}
