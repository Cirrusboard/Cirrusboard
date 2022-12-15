<?php
if (!file_exists('conf/config.php')) {
	die('Please read the installing instructions in the README file.');
}

// load profiler first
require_once('lib/profiler.php');
$profiler = new Profiler();

require_once('conf/config.php');
require_once('vendor/autoload.php');
foreach (glob("lib/*.php") as $file) {
	require_once($file);
}

if (php_sapi_name() != "cli") {
	// Shorter variables for common $_SERVER values
	$ipaddr = $_SERVER['REMOTE_ADDR'];
	$useragent = $_SERVER['HTTP_USER_AGENT'] ?? null;
	$uri = $_SERVER['REQUEST_URI'] ?? null;
} else {
	// CLI fallback variables
	$ipaddr = '127.0.0.1';
	$useragent = 'PHP/CLI';
	$uri = '/';
}

// Whether the user is 'log'ged in
$log = false;

// Cookie authentication
if (isset($_COOKIE['token'])) {
	$id = result("SELECT id FROM users WHERE token = ?", [$_COOKIE['token']]);

	if ($id) // Valid cookie
		$log = true;
}


if ($log) {
	// Get data for the current user
	$userdata = fetch("SELECT * FROM users WHERE id = ?", [$id]);

	if (!isset($rss)) {
		query("UPDATE users SET lastview = ?, ip = ? WHERE id = ?", [time(), $ipaddr, $userdata['id']]);

		if (!$userdata['theme'] || !isValidTheme($userdata['theme']))
			$userdata['theme'] = $config['defaulttheme'];
	}
} else {
	// Fallback userdata for guests (NYI)
	$userdata = [
		'id' => -1,
		'powerlevel' => 0,
		'theme' => $config['defaulttheme']
	];

	if (!isset($rss))
		query("REPLACE INTO guests (lastview, ip) VALUES (?,?)", [time(), $ipaddr]);
}

$ppp = $userdata['ppp'] ?? 20;
$tpp = $userdata['tpp'] ?? 50;

if (!$log || !$userdata['timezone'])
	$userdata['timezone'] = $config['defaulttimezone'];

date_default_timezone_set($userdata['timezone']);
