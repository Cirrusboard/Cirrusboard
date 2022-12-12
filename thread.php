<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;
$uid = $_GET['user'] ?? null;
$time = $_GET['time'] ?? null;
$page = $_GET['page'] ?? 1;

if ($id)
	$viewmode = 'thread';
elseif ($uid)
	$viewmode = 'user';
elseif ($time)
	$viewmode = 'time';
elseif (isset($_GET['pid'])) { // Thing to ease permalinks, thread.php?pid=%d to point to a particular post
	// TODO: When I implement pagination this needs to be expanded
	$id = result("SELECT thread FROM posts WHERE id = ?", [$_GET['pid']]);
	$viewmode = 'thread';
} else
	error('400', "I'm confused as to what you want...");

// common fields to select
$selectfields = 'u.id u_id, u.name u_name, u.powerlevel u_powerlevel, '.postfields_user().
				'p.*, pt.text, pt.date, pt.revision cur_revision';

// common joins
$join = "FROM posts p
		JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
		JOIN users u ON p.user = u.id
		JOIN threads t ON p.thread = t.id
		JOIN forums f ON f.id = t.forum";

// search offset relative to the current page
$offset = (($page - 1) * $ppp);

if ($viewmode == 'thread') {

	query("UPDATE threads SET views = views + 1 WHERE id = ?", [$id]);

	$thread = fetch("SELECT t.*, f.title forum_title, f.id forum_id FROM threads t
			JOIN forums f ON f.id = t.forum
			WHERE t.id = ? AND ? >= f.minread",
		[$id, $userdata['powerlevel']]);

	if (!$thread) error('404', "This forum doesn't exist.");

	$posts = query("SELECT $selectfields
			$join
			WHERE p.thread = ?
			ORDER BY p.id LIMIT ?,?",
		[$id, $offset, $ppp]);

	$breadcrumb = ['forum.php?id='.$thread['forum_id'] => $thread['forum_title']];

	$url = "thread.php?id=$id";

} elseif ($viewmode == 'user') {

	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404', "This user doesn't exist.");

	$thread['posts'] = result("SELECT COUNT(*) FROM posts WHERE user = ?", [$uid]);

	$posts = query("SELECT $selectfields, t.id tid, t.title ttitle
			$join
			WHERE p.user = ? AND ? >= f.minread
			ORDER BY p.date DESC LIMIT ?,?",
		[$uid, $userdata['powerlevel'], $offset, $ppp]);

	$breadcrumb = ['profile.php?id='.$uid => $user['name']];

	$url = "thread.php?user=$uid";

} elseif ($viewmode == 'time') {

	$mintime = ($time > 0 && $time <= 2592000 ? time() - $time : 604800);

	$thread['posts'] = result("SELECT COUNT(*) FROM posts WHERE date > ?", [$mintime]);

	$posts = query("SELECT $selectfields, t.id tid, t.title ttitle
			$join
			WHERE p.date > ? AND ? >= f.minread
			ORDER BY p.date DESC LIMIT ?,?",
		[$mintime, $userdata['powerlevel'], $offset, $ppp]);

	$url = "thread.php?time=$time";
}

if ($thread['posts'] > $ppp)
	$pagelist = pagination($thread['posts'], $ppp, $url.'&page=%s', $page);

echo twigloader()->render('thread.twig', [
	'id' => $id,
	'thread' => $thread ?? null,
	'posts' => $posts,
	'breadcrumb' => $breadcrumb ?? null,
	'viewmode' => $viewmode,
	'time' => $time,
	'pagelist' => $pagelist ?? null
]);
