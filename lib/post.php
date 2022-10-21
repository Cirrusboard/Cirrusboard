<?php

function postfilter($text) {
	if (empty($text)) return;

	$text = trim($text);

	$text = str_replace("\n", '<br>', $text);

	// TODO: BBCode (or markdown?)

	return $text;
}
