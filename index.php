<?php
require('lib/common.php');

$categs = query("SELECT id, title FROM categories ORDER BY ord, id");
while ($c = $categs->fetch())
	$categories[$c['id']] = $c['title'];

$forums = query("SELECT f.*, u.id u_id, u.name u_name, u.powerlevel u_powerlevel FROM forums f
		LEFT JOIN users u ON u.id = f.lastuser
		JOIN categories c ON c.id = f.cat
		WHERE ? >= f.minread
		ORDER BY c.ord, c.id, f.ord, f.id",
	[$userdata['powerlevel']]);

$stats = fetch("SELECT (SELECT COUNT(*) FROM users) u, (SELECT COUNT(*) FROM threads) t, (SELECT COUNT(*) FROM posts) p");

$newestUser = fetch("SELECT id, name, powerlevel FROM users ORDER BY id DESC LIMIT 1");

echo twigloader()->render('index.twig', [
	'categories' => $categories,
	'forums' => $forums,
	'just_registered' => isset($_GET['rd']),
	'stats' => $stats,
	'newestuser' => $newestUser
]);
