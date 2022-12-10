<?php
require('lib/common.php');

// TODO: only current user can be edited, make admins able to edit others
$user = fetch("SELECT * FROM users WHERE id = ?", [$userdata['id']]);

if (isset($_POST['action'])) {
	// Temp variables for dynamic query construction.
	$fieldquery = '';
	$placeholders = [];

	$fields = [
		'location'	=> $_POST['location'] ?: null,
		'bio'		=> $_POST['bio'] ?: null,
		'email'		=> $_POST['email'] ?: null,
		'showemail'	=> isset($_POST['showemail']) ? 1 : 0,
		//'theme'		=> $_POST['theme'] != $defaulttheme ? $_POST['theme'] : null,
		'timezone'	=> $_POST['timezone'] != $config['defaulttimezone'] ? $_POST['timezone'] : null,
		'ppp'		=> $_POST['ppp'],
		'tpp'		=> $_POST['tpp'],
	];

	if ($userdata['powerlevel'] > 2)
		$fields['title'] = $_POST['title'];

	// Construct a query containing all fields.
	foreach ($fields as $fieldk => $fieldv) {
		if ($fieldquery) $fieldquery .= ',';
		$fieldquery .= $fieldk.'=?';
		$placeholders[] = $fieldv;
	}

	// 100% safe from SQL injection because no arbitrary user input is ever put directly
	// into the query, rather it is passed as a prepared statement placeholder.
	$placeholders[] = $user['id'];
	query("UPDATE users SET $fieldquery WHERE id = ?", $placeholders);

	redirect('profile.php?id=');
}


$timezones = [];
foreach (timezone_identifiers_list() as $tz)
	$timezones[$tz] = $tz;

$user['timezone'] = $user['timezone'] ?: $config['defaulttimezone'];

$birthday = ['y' => 2004, 'm' => 11, 'd' => 28];

echo twigloader()->render('editprofile.twig', [
	'user' => $user,
	'timezones' => $timezones,
	'birthday' => $birthday
]);
