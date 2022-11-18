<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$forum = fetch("SELECT * FROM forums WHERE id = ?", [$id]);

if (!$forum) error('404', "This forum doesn't exist.");

$threads = query("SELECT t.*, u.id u_id, u.name u_name, u.powerlevel u_powerlevel,
		ul.id ul_id, ul.name ul_name, ul.powerlevel ul_powerlevel FROM threads t
		JOIN users u ON u.id = t.user
		JOIN users ul ON ul.id = t.lastuser
		WHERE t.forum = ? ORDER BY t.id DESC",
	[$id]);

echo twigloader()->render('forum.twig', [
	'id' => $id,
	'forum' => $forum,
	'threads' => $threads
]);
