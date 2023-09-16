<?php
require('lib/common.php');

$users = $sql->query("SELECT id, name, rank, avatar, posts, joined FROM users");

twigloader()->display('memberlist.twig', [
	'users' => $users
]);
