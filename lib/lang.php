<?php

function initTranslations($langCode) {
	$langfile = 'lang/cirrusboard.'.$langCode.'.json';
	if (file_exists($langfile))
		define('LANG_DATA',
			json_decode(file_get_contents($langfile), true));
}

function translate($string, ...$placeholders) {
	if (defined('LANG_DATA') && isset(LANG_DATA[$string]) && LANG_DATA[$string] != '')
		$translatedString = LANG_DATA[$string];
	else
		$translatedString = $string;

	return sprintf($translatedString, ...$placeholders);
}

function __($string, ...$placeholders) {
	return translate($string, ...$placeholders);
}
