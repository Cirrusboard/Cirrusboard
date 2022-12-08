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

function timeunits($sec) {
	if ($sec < 60)		return "$sec sec.";
	if ($sec < 3600)	return floor($sec / 60) . ' min.';
	if ($sec < 86400)	return floor($sec / 3600) . ' hour' . ($sec >= 7200 ? 's' : '');
	return floor($sec / 86400) . ' day' . ($sec >= 172800 ? 's' : '');
}

function timelink($timex, $file, $time) {
	if ($time == $timex)
		return timeunits($timex);
	else
		return sprintf('<a href="%s.php?time=%s">%s</a>', $file, $timex, timeunits($timex));
}

function timelinks($file, $time) {
	$lol = [3600, 86400, 604800, 2592000];
	$out = [];

	foreach ($lol as $lo)
		$out[] = timelink($lo, $file, $time);

	return implode(" | ", $out);
}
