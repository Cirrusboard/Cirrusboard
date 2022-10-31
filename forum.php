<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$forum = fetch("SELECT * FROM forums WHERE id = ?", [$id]);

$threads = query("SELECT t.*, u.id u_id, u.name u_name FROM threads t
		JOIN users u ON u.id = t.user
		WHERE t.forum = ? ORDER BY t.id DESC",
	[$id]);

$twig = twigloader();

echo $twig->render('forum.twig', [
	'forum' => $forum,
	'threads' => $threads
]);
