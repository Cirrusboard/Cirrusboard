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

$userfields = userfields().userfields('ul');
$offset = (($page - 1) * $tpp);

if ($viewmode == 'forum') {
	$isread = $threadsread = '';

	if ($log) {
		$forum = fetch("SELECT f.*, r.time rtime FROM forums f LEFT JOIN forumsread r ON (r.fid = f.id AND r.uid = ?)
			WHERE f.id = ? AND ? >= minread", [$userdata['id'], $id, $userdata['powerlevel']]);
		if (!$forum['rtime']) $forum['rtime'] = 0;

		$isread = ", (NOT (r.time < t.lastdate OR isnull(r.time)) OR t.lastdate < '".$forum['rtime']."') isread";
		$threadsread = "LEFT JOIN threadsread r ON (r.tid = t.id AND r.uid = ".$userdata['id'].")";
	} else
		$forum = fetch("SELECT * FROM forums WHERE id = ? AND ? >= minread", [$id, $userdata['powerlevel']]);

	if (!$forum) error('404', "This forum doesn't exist.");

	$threads = query("SELECT $userfields t.* $isread FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			$threadsread
			WHERE t.forum = ?
			ORDER BY t.sticky DESC, t.lastdate DESC LIMIT ?,?",
		[$id, $offset, $tpp]);

	$url = "forum.php?id=$id";
} elseif ($viewmode == 'user') {
	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404', "User does not exist.");

	$forum['threads'] = result("SELECT COUNT(*) FROM threads t
			LEFT JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread",
		[$uid, $userdata['powerlevel']]);

	$threads = query("SELECT $userfields t.* FROM threads t
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

	$threads = query("SELECT $userfields t.* FROM threads t
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
