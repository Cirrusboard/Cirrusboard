<?php
$id = $_GET['id'] ?? null;

$profile = fetch("SELECT * FROM users WHERE id = ?", [$id]);

if (!$profile) error('404');

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
	__("General information") => [
		__('Name')			=> $profile['name'],
		__('Rank')			=> rankIdToName($profile['rank']),
		__('Title')			=> $profile['title'],
		__('Total posts')	=> sprintf('%s (%1.02f per day)', $profile['posts'], $profile['posts'] / $days),
		__('Total threads')	=> sprintf('%s (%1.02f per day)', $profile['threads'], $profile['threads'] / $days),
		__('Registered on')	=> sprintf('%s (%s)', dateformat($profile['joined']), relTime($profile['joined'])),
		__('Last post')		=> ($profile['lastpost'] ? sprintf('%s (%s)', dateformat($profile['lastpost']), relTime($profile['lastpost'])) : "None"),
		__('Last view')		=> sprintf('%s (%s)', dateformat($profile['lastview']), relTime($profile['lastview']))
	],
	__("User information") => [
		__('Bio')		=> ($profile['bio'] ? postfilter($profile['bio']) : ''),
		__('Location')	=> $profile['location'] ?: '',
		__('Email')		=> $email ?? '',
		__('Birthday')	=> $birthday ?? ''
	]
];

$post = ['date' => time(), 'text' => $samplepost ?? 'um hi', 'headerbar' => __('Sample post')];

foreach ($profile as $field => $val)
	$post['u_'.$field] = $val;

// Profile actions

$actions = [
	"forum?user=$id" => __('View threads'),
	"thread?user=$id" => __('Show posts')];

if (IS_ADMIN && $userdata['rank'] > $profile['rank'])
	$actions["editprofile?id=$id"] = __('Edit user');

twigloader()->display('profile.twig', [
	'uid' => $id,
	'profile' => $profile,
	'profilefields' => $profilefields,
	'sample_post' => $post,
	'actions' => $actions
]);
