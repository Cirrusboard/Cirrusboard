<?php
require('lib/common.php');

$forums = query("SELECT f.* FROM forums f ORDER BY f.sort,f.id");

$twig = twigloader();

echo $twig->render('index.twig', [
	'forums' => $forums,
	'just_registered' => isset($_GET['rd'])
]);
