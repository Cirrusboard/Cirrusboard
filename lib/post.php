<?php

function postfilter($text) {
	if (empty($text)) return;

	$text = trim($text);

	$text = str_replace("\n", '<br>', $text);

	// Basic formatting
	$text = preg_replace("'\[b\](.*?)\[/b\]'si", '<b>\\1</b>', $text);
	$text = preg_replace("'\[i\](.*?)\[/i\]'si", '<i>\\1</i>', $text);
	$text = preg_replace("'\[u\](.*?)\[/u\]'si", '<u>\\1</u>', $text);
	$text = preg_replace("'\[s\](.*?)\[/s\]'si", '<s>\\1</s>', $text);

	// Little bit more advanced stuff
	$text = preg_replace("'\[spoiler\](.*?)\[/spoiler\]'si", '<span class="spoiler" onclick=""><span class="container">\\1</span></span>', $text);
	$text = preg_replace("'\[url\](.*?)\[/url\]'si", '<a href=\\1>\\1</a>', $text);
	$text = preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si", '<a href=\\1>\\2</a>', $text);
	$text = preg_replace("'\[img\](.*?)\[/img\]'si", '<img src=\\1>', $text);
	$text = preg_replace("'\[color=([a-f0-9]{6})\](.*?)\[/color\]'si", '<span style="color: #\\1">\\2</span>', $text);

	// Quotes (not from cave story LOL)
	$text = preg_replace("'\[quote\](.*?)\[/quote\]'si", '<div class="quote"><div class="quotetext">\\1</div></div>', $text);
	$text = preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\]'si", '<div class="quote"><div class="author"><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></div><div class="quotetext">', $text);
	$text = preg_replace("'\[quote=(.*?)\]'si", '<div class="quote"><div class="author">Posted by ', $text);
	$text = str_replace('[/quote]', '</div></div>', $text);

	return $text;
}
