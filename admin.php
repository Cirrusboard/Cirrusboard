<?php
require('lib/common.php');

if (!IS_ADMIN) error('403', "You shouldn't be here, get out!");

$latestRegisteredUsers = query("SELECT id, name, rank, joined FROM users ORDER BY joined DESC LIMIT 7");
$latestSeenUsers = query("SELECT id, name, rank, lastview FROM users ORDER BY lastview DESC LIMIT 7");

$tbs = $sql->query('SHOW TABLE STATUS');
while ($t = $tbs->fetch())
	$tablestatus[$t['Name']] = $t;

echo twigloader()->render('admin.twig', [
	'latest_registered_users' => $latestRegisteredUsers,
	'latest_seen_users' => $latestSeenUsers,
	'tablestatus' => $tablestatus
]);
