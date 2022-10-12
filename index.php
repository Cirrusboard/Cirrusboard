<?php
require('lib/common.php');

$twig = twigloader();

echo $twig->render('index.twig', [
	'just_registered' => isset($_GET['rd'])
]);
