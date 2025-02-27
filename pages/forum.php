<?php
$id = (int)($_GET['id'] ?? null);
$uid = (int)($_GET['user'] ?? null);
$time = (int)($_GET['time'] ?? null);
$page = (int)($_GET['page'] ?? 1);

if ($id)
	$viewmode = 'forum';
elseif ($uid)
	$viewmode = 'user';
elseif ($time)
	$viewmode = 'time';
else
	error('404');

$userfields = userfields().userfields('ul');
$offset = (($page - 1) * $tpp);

if ($viewmode == 'forum') {
	$isread = $threadsread = '';

	if ($log) {
		$forum = fetch("SELECT f.*, r.time rtime FROM forums f LEFT JOIN forumsread r ON (r.fid = f.id AND r.uid = ?)
			WHERE f.id = ? AND ? >= minread", [$userdata['id'], $id, $userdata['rank']]);

		if (!$forum) error('404');

		if (!$forum['rtime']) $forum['rtime'] = 0;

		$isread = ", (NOT (r.time < t.lastdate OR isnull(r.time)) OR t.lastdate < '".$forum['rtime']."') isread";
		$threadsread = "LEFT JOIN threadsread r ON (r.tid = t.id AND r.uid = ".$userdata['id'].")";
	} else
		$forum = fetch("SELECT * FROM forums WHERE id = ? AND ? >= minread", [$id, $userdata['rank']]);

	if (!$forum) error('404');

	$threads = query("SELECT $userfields t.* $isread FROM threads t
			LEFT JOIN users u ON u.id = t.user
			LEFT JOIN users ul ON ul.id = t.lastuser
			$threadsread
			WHERE t.forum = ?
			ORDER BY t.sticky DESC, t.lastdate DESC LIMIT ?,?",
		[$id, $offset, $tpp]);

	$url = "forum?id=$id";
} elseif ($viewmode == 'user') {
	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404');

	$forum['threads'] = result("SELECT COUNT(*) FROM threads t
			LEFT JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread",
		[$uid, $userdata['rank']]);

	$threads = query("SELECT $userfields t.* FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.user = ? AND ? >= f.minread
			ORDER BY t.id DESC LIMIT ?,?",
		[$uid, $userdata['rank'], $offset, $tpp]);

	$breadcrumb = ['profile?id='.$uid => $user['name']];

	$url = "forum?user=$uid";
} elseif ($viewmode == 'time') {
	$mintime = ($time > 0 && $time <= 2592000 ? time() - $time : 604800);

	$forum['threads'] = result("SELECT COUNT(*) FROM threads t
			LEFT JOIN forums f ON f.id = t.forum
			WHERE t.lastdate > ? AND ? >= f.minread",
		[$mintime, $userdata['rank']]);

	$threads = query("SELECT $userfields t.* FROM threads t
			JOIN users u ON u.id = t.user
			JOIN users ul ON ul.id = t.lastuser
			JOIN forums f ON f.id = t.forum
			WHERE t.lastdate > ? AND ? >= f.minread
			ORDER BY t.id DESC LIMIT ?,?",
		[$mintime, $userdata['rank'], $offset, $tpp]);

	$url = "forum?time=$time";
}

if ($forum['threads'] > $tpp)
	$pagelist = pagination($forum['threads'], $tpp, $url.'&page=%s', $page);

twigloader()->display('forum.twig', [
	'id' => $id,
	'forum' => $forum ?? null,
	'threads' => $threads,
	'breadcrumb' => $breadcrumb ?? null,
	'viewmode' => $viewmode,
	'uid' => $uid,
	'time' => $time,
	'pagelist' => $pagelist ?? null
]);
