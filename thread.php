<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;
$uid = $_GET['user'] ?? null;
$time = $_GET['time'] ?? null;

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

$userpostfields = 'u.id u_id, u.name u_name, u.powerlevel u_powerlevel, '.postfields_user();

if ($viewmode == 'thread') {
	$thread = fetch("SELECT t.*, f.title forum_title, f.id forum_id FROM threads t
			JOIN forums f ON f.id = t.forum
			WHERE t.id = ? AND ? >= f.minread",
		[$id, $userdata['powerlevel']]);

	if (!$thread) error('404', "This forum doesn't exist.");

	$posts = query("SELECT $userpostfields p.*, pt.text, pt.date ptdate, pt.revision cur_revision
			FROM posts p
			JOIN poststext pt ON p.id = pt.id AND pt.revision = 1
			JOIN users u ON p.user = u.id
			WHERE p.thread = ?
			ORDER BY p.id",
		[$id]);

	$breadcrumb = ['forum.php?id='.$thread['forum_id'] => $thread['forum_title']];
} elseif ($viewmode == 'user') {
	$user = fetch("SELECT name FROM users WHERE id = ?", [$uid]);

	if (!$user) error('404', "This user doesn't exist.");

	$posts = query("SELECT $userpostfields p.*, pt.text, pt.date ptdate, pt.revision cur_revision, t.id tid, t.title ttitle
			FROM posts p
			LEFT JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
			LEFT JOIN users u ON p.user = u.id
			LEFT JOIN threads t ON p.thread = t.id
			LEFT JOIN forums f ON f.id = t.forum
			WHERE p.user = ? AND ? >= f.minread
			ORDER BY p.id",
		[$uid, $userdata['powerlevel']]);

	$breadcrumb = ['profile.php?id='.$uid => $user['name']];
} elseif ($viewmode == 'time') {
	$mintime = ($time > 0 && $time <= 2592000 ? time() - $time : 86400);

	$posts = query("SELECT $userpostfields p.*, pt.text, pt.date ptdate, pt.revision cur_revision, t.id tid, t.title ttitle
			FROM posts p
			LEFT JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
			LEFT JOIN users u ON p.user = u.id
			LEFT JOIN threads t ON p.thread = t.id
			LEFT JOIN forums f ON f.id = t.forum
			WHERE p.date > ? AND ? >= f.minread
			ORDER BY p.date DESC",
		[$time, $userdata['powerlevel']]);
}



echo twigloader()->render('thread.twig', [
	'id' => $id,
	'thread' => $thread ?? null,
	'posts' => $posts,
	'breadcrumb' => $breadcrumb ?? null,
	'viewmode' => $viewmode
]);
