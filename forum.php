<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;
$uid = $_GET['user'] ?? null;
$time = $_GET['time'] ?? null;

if ($id)
	$viewmode = 'forum';
elseif ($uid)
	$viewmode = 'user';
elseif ($time)
	$viewmode = 'time';
else
	error('400', "I'm confused as to what you want...");

$userfields = "u.id u_id, u.name u_name, u.powerlevel u_powerlevel, ul.id ul_id, ul.name ul_name, ul.powerlevel ul_powerlevel";

if ($viewmode == 'forum') {
	$forum = fetch("SELECT * FROM forums WHERE id = ?", [$id]);

	if (!$forum) error('404', "This forum doesn't exist.");

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			WHERE t.forum = ? ORDER BY t.id DESC",
		[$id]);
} elseif ($viewmode == 'user') {
	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404', "User does not exist.");

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread
			ORDER BY t.id DESC",
		[$uid, $userdata['powerlevel']]);

	$breadcrumb = ['profile.php?id='.$uid => $user['name']];
} elseif ($viewmode == 'time') {
	$mintime = ($time > 0 && $time <= 2592000 ? time() - $time : 86400);

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.lastdate > ? AND ? >= f.minread
			ORDER BY t.id DESC",
		[$mintime, $userdata['powerlevel']]);
}

echo twigloader()->render('forum.twig', [
	'id' => $id,
	'forum' => $forum ?? null,
	'threads' => $threads,
	'viewmode' => $viewmode
]);
