<?php
require('lib/common.php');

$categs = query("SELECT id, title FROM categories ORDER BY ord, id");
while ($c = $categs->fetch())
	$categories[$c['id']] = $c['title'];

$forums = query("SELECT f.* FROM forums f
		JOIN categories c ON c.id = f.cat
		WHERE ? >= f.minread
		ORDER BY c.ord, c.id, f.ord, f.id",
	[$userdata['powerlevel']]);

$twig = twigloader();

echo $twig->render('index.twig', [
	'categories' => $categories,
	'forums' => $forums,
	'just_registered' => isset($_GET['rd'])
]);
