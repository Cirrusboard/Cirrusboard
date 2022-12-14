<?php
$rss = true;
require('lib/common.php');

header('Content-Type: text/xml');

$posts = query("SELECT u.id u_id, u.name u_name, p.*, t.title, t.forum, f.id fid, f.title ftitle
		FROM posts p
		 JOIN threads t ON t.id = p.thread
		 JOIN users u ON u.id = p.user
		 JOIN forums f ON f.id = t.forum
		WHERE ? >= f.minread
		ORDER BY p.date DESC LIMIT 30",
	[$userdata['powerlevel']]);

$fullurl = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']
	.str_replace('/rss.php', '', $_SERVER['SCRIPT_NAME']);

echo twigloader()->render('rss.twig', [
	'posts' => $posts,
	'fullurl' => $fullurl
]);
