<?php

// Function that does lots of voodoo magic to make sure the post data is (reasonably) safe
function securityfilter($msg) {
	$tags = ':a(?:pplet|udio)|b(?:ase|gsound|ody|button)|canvas|embed|frame(?:set)?|form|h(?:ead|tml)|i(?:frame|layer|nput)|l(?:ayer|ink)|m(?:ath|eta|eth)|noscript|object|plaintext|s(?:cript|vg|ource)|title|textarea|video|x(?:ml|mp)';
	$msg = preg_replace("'<(/?)({$tags})'si", "&lt;$1$2", $msg);

	$msg = preg_replace('@(on)(\w+\s*)=@si', '$1_$2&#x3D;', $msg);

	$msg = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2jujscript!', $msg);

	$msg = preg_replace("'-moz-binding'si", ' -mo<b></b>z-binding', $msg);
	$msg = str_ireplace("expression", "ex<b></b>pression", $msg);
	$msg = preg_replace("'filter:'si", 'filter&#58;>', $msg);
	$msg = preg_replace("'transform:'si", 'transform&#58;>', $msg);

	$msg = str_replace("<!--", "&lt;!--", $msg);

	return $msg;
}

function postfilter($text) {
	global $smilies, $config;

	if (empty($text)) return;

	// Normalise the text (make it sane(r))
	$text = str_replace("\r", "", trim($text));

	if ($config['html'])
		$text = securityfilter($text);
	else
		$text = htmlspecialchars($text);

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
		'<td><button id="postformatting-%s" title="%s" type="button">%s</button></td>',
	$tag, $title, $name);
}

function posttoolbar() {
	// TODO: I'd really want some better icons for this... Font awesome?
	return '<table class="postformatting nom" id="postformatting"><tr>'
			.posttoolbutton('B', 'Bold', 'b')
			.posttoolbutton('<i>I</i>', 'Italics', 'i')
			.posttoolbutton('<u>U</u>', 'Underline', 'u')
			.posttoolbutton('<s>S</s>', 'Strikethrough', 's')
			.'<td>&nbsp;</td>'
			.posttoolbutton('://', 'URL', 'url')
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
