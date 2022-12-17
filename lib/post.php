<?php

function postfilter($text) {
	global $smilies;

	if (empty($text)) return;

	// Normalise the text (make it sane(r))
	$text = str_replace("\r", "", trim($text));

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

	// Userlinks and post links
	$text = preg_replace("'>>([0-9]+)'si", '>><a href=thread.php?pid=\\1#\\1>\\1</a>', $text);

	// Quotes (not from cave story LOL)
	$text = preg_replace("'\[quote\](.*?)\[/quote\][\n\r]*'si", '<div class="quote"><div class="quotetext">\\1</div></div>', $text);
	$text = preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\][\n\r]*'si", '<div class="quote"><div class="author"><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></div><div class="quotetext">', $text);
	$text = preg_replace("'\[quote=(.*?)\][\n\r]*'si", '<div class="quote"><div class="author">Posted by \\1</div><div class="quotetext">', $text);
	$text = preg_replace("'\[/quote\][\n\r]*'", '</div></div>', $text);

	// Code block
	$text = preg_replace_callback("'\[code\](.*?)\[/code\]'si", function ($match) {
		return '<div class="codeblock">'.htmlspecialchars($match[1]).'</div>';
	}, $text);
	$text = preg_replace("'\[pre\](.*?)\[/pre\]'si", '<code>\\1</code>', $text);

	// YooToob
	$text = preg_replace(
		"'\[youtube\]((https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/(watch\?v=)?)?([\-0-9_a-zA-Z]*?)\[\/youtube\]'si",
		'<iframe width="427" height="240" src="https://www.youtube.com/embed/\\6" frameborder="0" allowfullscreen></iframe>',
	$text);


	// Smil(ies|eys)
	foreach ($smilies as $code => $image)
		$text = str_replace(" $code ", sprintf(' <img class="smiley" src="%s" alt="%s" title="%s"> ', $image, $code, $code), $text);

	$text = str_replace("\n", '<br>', $text);

	return $text;
}

function posttoolbutton($name, $title, $tag) {
	return sprintf(
		'<td><button onclick="formatText(\'%s\');return false" title="%s">%s</button></td>',
	$tag, $title, $name);
}

function posttoolbar() {
	// TODO: I'd really want some better icons for this... Font awesome?
	return '<table class="postformatting"><tr>'
			.posttoolbutton('B', 'Bold', 'b')
			.posttoolbutton('I', 'Italics', 'i')
			.posttoolbutton('U', 'Underline', 'u')
			.posttoolbutton('S', 'Strikethrough', 's')
			.'<td>&nbsp;</td>'
			.posttoolbutton(':/', 'URL', 'url')
			.posttoolbutton('!', 'Spoiler', 'spoiler')
			.posttoolbutton('&quot;', 'Quote', 'quote')
			.posttoolbutton('>_', 'Code', 'code')
			.posttoolbutton(';', 'Preformatted text', 'pre')
			.'<td>&nbsp;</td>'
			.posttoolbutton('[]', 'Image', 'img')
			.posttoolbutton('YT', 'YouTube embed', 'youtube')
			.'<td>&nbsp;</td><td><a href="faq.php#smile">More...</a></td>'
			.'</tr></table>';
}
