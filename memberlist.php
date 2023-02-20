<?php
require('lib/common.php');

$users = $sql->query("SELECT id, name, rank, avatar, posts, joined FROM users");

echo twigloader()->render('memberlist.twig', [
	'users' => $users
]);
