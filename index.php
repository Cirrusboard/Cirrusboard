<?php
require('lib/common.php');

$categs = query("SELECT id, title FROM categories ORDER BY ord, id");
while ($c = $categs->fetch())
	$categories[$c['id']] = $c['title'];


$forums = query("SELECT $userfields f.* FROM forums f
		LEFT JOIN users u ON u.id = f.lastuser
		JOIN categories c ON c.id = f.cat
		WHERE ? >= f.minread
		ORDER BY c.ord, c.id, f.ord, f.id",
	[$userdata['powerlevel']]);

// Online users stats
$args = [(time() - 15*60)];

$onlineUsers = query("SELECT id,name,powerlevel,lastview FROM users WHERE lastview > ? ORDER BY name", $args);
$onlineUsersCount = result("SELECT COUNT(*) FROM users WHERE lastview > ?", $args);

$guestsOnline = result("SELECT COUNT(*) guests FROM guests WHERE lastview > ?", $args);


$stats = fetch("SELECT (SELECT COUNT(*) FROM users) u, (SELECT COUNT(*) FROM threads) t, (SELECT COUNT(*) FROM posts) p");

$newestUser = fetch("SELECT id, name, powerlevel FROM users ORDER BY id DESC LIMIT 1");

echo twigloader()->render('index.twig', [
	'categories' => $categories,
	'forums' => $forums,
	'just_registered' => isset($_GET['rd']),
	'online_users' => $onlineUsers,
	'online_users_count' => $onlineUsersCount,
	'guests_online' => $guestsOnline,
	'stats' => $stats,
	'newestuser' => $newestUser
]);
