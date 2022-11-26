<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

// Thing to ease permalinks, thread.php?pid=%d to point to a particular post
if (isset($_GET['pid'])) {
	// TODO: When I implement pagination this needs to be expanded
	$id = result("SELECT thread FROM posts WHERE id = ?", [$_GET['pid']]);
}

$thread = fetch("SELECT t.*, f.title forum_title, f.id forum_id FROM threads t
		JOIN forums f ON f.id = t.forum
		WHERE t.id = ?",
	[$id]);

if (!$thread) error('404', "This forum doesn't exist.");

$userpostfields = postfields_user();
$posts = query("SELECT u.id u_id, u.name u_name, u.powerlevel u_powerlevel, $userpostfields p.*, pt.text FROM posts p
		JOIN poststext pt ON p.id = pt.id AND pt.revision = 1
		JOIN users u ON p.user = u.id
		WHERE p.thread = ?
		ORDER BY p.id",
	[$id]);

$breadcrumb = [
	'forum.php?id='.$thread['forum_id'] => $thread['forum_title']];

echo twigloader()->render('thread.twig', [
	'id' => $id,
	'thread' => $thread,
	'posts' => $posts,
	'breadcrumb' => $breadcrumb
]);
