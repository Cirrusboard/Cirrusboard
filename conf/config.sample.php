<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cirrusboard');
define('DB_USER', '');
define('DB_PASS', '');

define('TPL_CACHE', 'templates/cache/');
//define('TPL_CACHE', '/tmp/cirrusboard/');

// Customise your forum
$config['title'] = "Cool Forum";
$config['description'] = "A very cool forum.";
$config['logo'] = 'assets/logo_placeholder.png';
//$config['logotitle'] = ''; // Add a title attribute to the logo, which will be visible when hovered on it.
//$config['tagline'] = ''; // Add a tagline which will be shown in the header right below the logo.

$config['newsid'] = -1; // Designates the id for your announcements forum, the latest post will be shown at the top of the index page.

$config['defaulttheme'] = "voxelmanip_retro";
$config['defaulttimezone'] = "Europe/Stockholm"; // Default timezone if people do not select their own.

// Whether the forum list table should be merged, akin to the traditional Acmlmboard index page.
$config['mergedforumlist'] = false;

// Allow HTML in posts? A reasonable attempt is done to scrub JavaScript and other dangerous HTML elements, but tread with caution.
$config['html'] = false;

// This will enable Acmlmboard-style post layouts where users can customise their post layout with CSS. Requires HTML be enabled.
// When disabled, it will turn the signature into a regular forum one. Please be careful with disabling this on an existing forum
// as users with post layouts will be cut in half and create trailing HTML elements that break the layout.
$config['postlayouts'] = true;

// Uncomment to replace the footer with your own thing. We won't mind if you remove the credits from the footer!
// (As long as it remains in the /credits page and the LICENSE file is kept)
//$config['customfooter'] = <<<HTML
// (put some stuff inside here)
//HTML;

// Enable maintenance mode, which will show a message to members and logged out users.
$config['maintenance'] = false;
// Message for maintenance mode.
// $config['maintenance_msg'] = "<p>The Voxelmanip Forums is temporarily closed for maintenance. Please come back later.</p>";

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
