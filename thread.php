<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$thread = fetch("SELECT t.*, f.name forum_title FROM threads t
		JOIN forums f ON f.id = t.forum
		WHERE t.id = ?",
	[$id]);

$posts = query("SELECT p.*, pt.text FROM posts p
		JOIN poststext pt ON p.id = pt.id AND pt.revision = 1
		WHERE p.thread = ?",
	[$id]);

$twig = twigloader();

echo $twig->render('thread.twig', [
	'thread' => $thread,
	'posts' => $posts
]);
