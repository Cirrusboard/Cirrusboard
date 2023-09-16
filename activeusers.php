<?php
require('lib/common.php');

$time = (int)($_GET['time'] ?? 604800);

$users = query("SELECT u.id, u.name, u.rank, u.posts, u.joined, COUNT(*) num
		FROM users u LEFT JOIN posts p ON p.user = u.id
		WHERE p.date > ? GROUP BY u.id ORDER BY num DESC",
	[(time() - $time)]);

twigloader()->display('activeusers.twig', [
	'time' => $time,
	'users' => $users
]);
