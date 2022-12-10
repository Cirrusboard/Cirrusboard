<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$profile = fetch("SELECT * FROM users WHERE id = ?", [$id]);

if (!$profile) error('404', 'This user does not exist.');

$days = (time() - $profile['joined']) / 86400;

if ($profile['email'] && $profile['showemail'] && $log)
	$email = esc($profile['email']);

$profilefields = [
	"General information" => [
		'Name'			=> $profile['name'],
		'Power'			=> powIdToName($profile['powerlevel']),
		'Title'			=> $profile['title'],
		'Total posts'	=> sprintf('%s (%1.02f per day)', $profile['posts'], $profile['posts'] / $days),
		'Total threads'	=> sprintf('%s (%1.02f per day)', $profile['threads'], $profile['threads'] / $days),
		'Registered on'	=> sprintf('%s (%s)', dateformat($profile['joined']), relTime($days * 86400)),
		'Last post'		=> ($profile['lastpost'] ? sprintf('%s (%s)', dateformat($profile['lastpost']), relTime(time()-$profile['lastpost'])) : "None"),
		'Last view'		=> sprintf('%s (%s)', dateformat($profile['lastview']), relTime(time() - $profile['lastview']))
	],
	"User information" => [
		'Bio'		=> ($profile['bio'] ? postfilter($profile['bio']) : ''),
		'Location'	=> $profile['location'] ?: '',
		'Email'		=> $email ?? ''
	]
];

$post = ['date' => time(), 'text' => $samplepost, 'headerbar' => 'Sample post'];

foreach ($profile as $field => $val)
	$post['u_'.$field] = $val;

echo twigloader()->render('profile.twig', [
	'uid' => $id,
	'profile' => $profile,
	'profilefields' => $profilefields,
	'sample_post' => $post
]);
