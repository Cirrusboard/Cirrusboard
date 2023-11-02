<?php

function relTime($time) {
	if ($time === null) return 'never';

	$relativeTime = new RelativeTime([
		'truncate' => 1,
	]);

	return $relativeTime->timeAgo($time);
}

function dateformat($time) {
	return date('Y-m-d H:i', $time);
}

function timelinks($file, $seltime) {
	$relativeTime = new RelativeTime([
		'suffix' => false,
		'truncate' => 1,
	]);

	$links = [];
	foreach ([3600, 86400, 604800, 2592000] as $time) {
		$timelbl = $relativeTime->convert(1, $time+1);

		if ($time == $seltime)
			$links[] = $timelbl;
		else
			$links[] = sprintf('<a href="%s?time=%s">%s</a>', $file, $time, $timelbl);
	}

	return join(' | ', $links);
}
