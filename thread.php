<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$thread = fetch("SELECT t.*, f.name forum_title, f.id forum_id FROM threads t
		JOIN forums f ON f.id = t.forum
		WHERE t.id = ?",
	[$id]);

$userpostfields = postfields_user();
$posts = query("SELECT u.id u_id, u.name u_name, $userpostfields p.*, pt.text FROM posts p
		JOIN poststext pt ON p.id = pt.id AND pt.revision = 1
		JOIN users u ON p.author = u.id
		WHERE p.thread = ?
		ORDER BY p.id",
	[$id]);

$breadcrumb = [
	'forum.php?id='.$thread['forum_id'] => $thread['forum_title']];

$twig = twigloader();

echo $twig->render('thread.twig', [
	'thread' => $thread,
	'posts' => $posts,
	'breadcrumb' => $breadcrumb
]);
