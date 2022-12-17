<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;
$uid = $_GET['user'] ?? null;
$time = $_GET['time'] ?? null;
$page = $_GET['page'] ?? 1;

if ($id)
	$viewmode = 'forum';
elseif ($uid)
	$viewmode = 'user';
elseif ($time)
	$viewmode = 'time';
else
	error('400', "I'm confused as to what you want...");

$userfields = "u.id u_id, u.name u_name, u.powerlevel u_powerlevel, ul.id ul_id, ul.name ul_name, ul.powerlevel ul_powerlevel";
$offset = (($page - 1) * $tpp);

if ($viewmode == 'forum') {
	$forum = fetch("SELECT * FROM forums WHERE id = ?", [$id]);

	if (!$forum) error('404', "This forum doesn't exist.");

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			WHERE t.forum = ?
			ORDER BY t.sticky DESC, t.id DESC LIMIT ?,?",
		[$id, $offset, $tpp]);

	$url = "forum.php?id=$id";
} elseif ($viewmode == 'user') {
	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404', "User does not exist.");

	$forum['threads'] = result("SELECT COUNT(*) FROM threads t
			LEFT JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread",
		[$uid, $userdata['powerlevel']]);

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread
			ORDER BY t.id DESC LIMIT ?,?",
		[$uid, $userdata['powerlevel'], $offset, $tpp]);

	$breadcrumb = ['profile.php?id='.$uid => $user['name']];

	$url = "forum.php?user=$uid";
} elseif ($viewmode == 'time') {
	$mintime = ($time > 0 && $time <= 2592000 ? time() - $time : 604800);

	$forum['threads'] = result("SELECT COUNT(*) FROM threads t
			LEFT JOIN forums f ON f.id = t.forum
			WHERE t.lastdate > ? AND ? >= f.minread",
		[$mintime, $userdata['powerlevel']]);

	$threads = query("SELECT t.*, $userfields FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.lastdate > ? AND ? >= f.minread
			ORDER BY t.id DESC LIMIT ?,?",
		[$mintime, $userdata['powerlevel'], $offset, $tpp]);

	$url = "forum.php?time=$time";
}

if ($forum['threads'] > $tpp)
	$pagelist = pagination($forum['threads'], $tpp, $url.'&page=%s', $page);

echo twigloader()->render('forum.twig', [
	'id' => $id,
	'forum' => $forum ?? null,
	'threads' => $threads,
	'viewmode' => $viewmode,
	'time' => $time,
	'pagelist' => $pagelist ?? null
]);
