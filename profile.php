<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$profile = fetch("SELECT * FROM users WHERE id = ?", [$id]);

if (!$profile) error('404', 'This user does not exist.');

$days = (time() - $profile['joined']) / 86400;

if ($profile['email'] && $profile['showemail'] && $log)
	$email = esc($profile['email']);

if ($profile['birthday']) {
	$birthdate = new DateTime($profile['birthday']);
	$currdate = new DateTime(date("Y-m-d"));
	$birthday = date("F j, Y", strtotime($profile['birthday']))
		.' ('.intval($currdate->diff($birthdate)->format("%Y")).' years old)';
}

$profilefields = [
	"General information" => [
		'Name'			=> $profile['name'],
		'Power'			=> rankIdToName($profile['rank']),
		'Title'			=> $profile['title'],
		'Total posts'	=> sprintf('%s (%1.02f per day)', $profile['posts'], $profile['posts'] / $days),
		'Total threads'	=> sprintf('%s (%1.02f per day)', $profile['threads'], $profile['threads'] / $days),
		'Registered on'	=> sprintf('%s (%s)', dateformat($profile['joined']), relTime($profile['joined'])),
		'Last post'		=> ($profile['lastpost'] ? sprintf('%s (%s)', dateformat($profile['lastpost']), relTime($profile['lastpost'])) : "None"),
		'Last view'		=> sprintf('%s (%s)', dateformat($profile['lastview']), relTime($profile['lastview']))
	],
	"User information" => [
		'Bio'		=> ($profile['bio'] ? postfilter($profile['bio']) : ''),
		'Location'	=> $profile['location'] ?: '',
		'Email'		=> $email ?? '',
		'Birthday'	=> $birthday ?? ''
	]
];

$post = ['date' => time(), 'text' => $samplepost ?? 'um hi', 'headerbar' => 'Sample post'];

foreach ($profile as $field => $val)
	$post['u_'.$field] = $val;

// Profile actions

$actions = [
	"forum.php?user=$id" => 'View threads',
	"thread.php?user=$id" => 'Show posts'];

if ($userdata['rank'] > 2 && $userdata['rank'] > $profile['rank'])
	$actions["editprofile.php?id=$id"] = 'Edit user';

echo twigloader()->render('profile.twig', [
	'uid' => $id,
	'profile' => $profile,
	'profilefields' => $profilefields,
	'sample_post' => $post,
	'actions' => $actions
]);
