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
$config['logo'] = 'voxelmanip_forums.png';

$config['defaulttimezone'] = "Europe/Stockholm"; // Default timezone if people do not select their own.

// List of smilies, if you want them.
$smilies = [
	//'-_-' => 'assets/smilies/annoyed.png',
];
