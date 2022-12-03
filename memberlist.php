<?php
require('lib/common.php');

$users = $sql->query("SELECT id, name, powerlevel, avatar, posts, joined FROM users");

echo twigloader()->render('memberlist.twig', [
	'users' => $users
]);
