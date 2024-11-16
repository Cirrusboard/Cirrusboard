<?php
if (!file_exists('conf/config.php'))
	die('Please read the installing instructions in the README file.');

// load profiler first
require_once('lib/profiler.php');
$profiler = new Profiler();

require_once('conf/config.php');

define('DEBUG', (isset($debug) && $debug));

if (DEBUG) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

// Check maintenance config so early that it shows up even if Composer or the database isn't present.
if (isset($config['maintenance']) && $config['maintenance'] && php_sapi_name() != "cli") {
	require('lib/misc/maintenance.php');
	die();
}

if (file_exists('vendor.phar')) // Load vendor PHAR if it exists
	require_once('vendor.phar');
else
	require_once('vendor/autoload.php');

foreach (glob("lib/*.php") as $file)
	require_once($file);

$userfields = userfields();

if (php_sapi_name() != "cli") {
	// Prevent running any javascript even if it's somehow injected
	header("Content-Security-Policy: script-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'self'; font-src 'self';");

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

$userId = authenticateCookie();
$log = $userId != -1;

if ($log) {
	// Get data for the current user
	$userdata = fetch("SELECT * FROM users WHERE id = ?", [$userId]);

	if (!isset($rss)) {
		query("UPDATE users SET lastview = ?, ip = ? WHERE id = ?", [time(), $ipaddr, $userdata['id']]);

		if (!$userdata['theme'] || !isValidTheme($userdata['theme']))
			$userdata['theme'] = $config['defaulttheme'];
	}
} else {
	// Fallback userdata for guests
	$userdata = [
		'id' => -1,
		'rank' => 0,
		'theme' => $config['defaulttheme']
	];

	if (!isset($rss))
		query("REPLACE INTO guests (lastview, ip) VALUES (?,?)", [time(), $ipaddr]);
}

define('IS_BANNED', $userdata['rank'] < 0);
define('IS_MEMBER', $userdata['rank'] > 0);
define('IS_MOD',	$userdata['rank'] > 1);
define('IS_ADMIN',	$userdata['rank'] > 2);
define('IS_ROOT',	$userdata['rank'] > 3);

if (isset($_GET['theme']))
	$userdata['theme'] = $_GET['theme'];

$ppp = $userdata['ppp'] ?? 20;
$tpp = $userdata['tpp'] ?? 50;

if (!$log || !$userdata['timezone'])
	$userdata['timezone'] = $config['defaulttimezone'];

date_default_timezone_set($userdata['timezone']);
