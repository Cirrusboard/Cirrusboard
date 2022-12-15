<?php
$host = '127.0.0.1';
$db   = 'cool_forum';
$user = '';
$pass = '';

$tplCache = 'templates/cache';
$tplNoCache = false; // **DO NOT SET AS TRUE IN PROD - DEV ONLY**

// Customise your forum
$config['title'] = "Cool Forum";
$config['description'] = "A very cool forum.";
$config['logo'] = 'assets/logo_placeholder.png';

$config['defaulttheme'] = "voxelmanip_retro";
$config['defaulttimezone'] = "Europe/Stockholm"; // Default timezone if people do not select their own.

// List of smilies, if you want them.
$smilies = [
	//'-_-' => 'assets/smilies/annoyed.png',
];

// Sample post that is shown on profile pages.
$samplepost = <<<HTML
[b]This[/b] is a [i]sample message.[/i] It shows how [u]your posts[/u] will look on the board.
[quote=Anonymous][spoiler]Hello![/spoiler][/quote]
[code]if (true) {\r
	print "The world isn't broken.";\r
} else {\r
	print "Something is very wrong.";\r
}[/code]
[url=]Test Link. Ooh![/url]
HTML;
