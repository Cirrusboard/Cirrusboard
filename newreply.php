<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;
$action = $_POST['action'] ?? null;
$message = trim($_POST['message'] ?? '');

$thread = fetch("SELECT t.*, f.title f_title
	FROM threads t LEFT JOIN forums f ON f.id = t.forum
	WHERE t.id = ?", [$id]);

$error = [];

if ($action == "Submit") {
	$lastpost = fetch("SELECT id, user, date FROM posts WHERE thread = ? ORDER BY id DESC LIMIT 1", [$thread['id']]);
	if ($lastpost['user'] == $userdata['id'] && $lastpost['date'] >= (time() - 60*60*12))
		$error[] = "You can't double post until it's been at least 12 hours!";

	if (strlen($message) == 0)
		$error[] = "Your post is empty. Enter a message and try again.";

	if ($error == []) {
		query("UPDATE users SET posts = posts + 1, lastpost = ? WHERE id = ?", [time(), $userdata['id']]);
		query("INSERT INTO posts (user, thread, date, ip) VALUES (?,?,?,?)",
			[$userdata['id'], $id, time(), $ipaddr]);

		$postid = insertid();
		query("INSERT INTO poststext (id, text) VALUES (?,?)",
			[$postid, $message]);

		query("UPDATE threads SET posts = posts + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[time(), $userdata['id'], $postid, $id]);

		query("UPDATE forums SET posts = posts + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[time(), $userdata['id'], $postid, $thread['forum']]);

		redirect("thread.php?pid=$postid#$postid");
	}
} elseif ($action == 'Preview') {
	$post['date'] = $post['ulastpost'] = time();
	$post['text'] = $message;
	foreach ($userdata as $field => $val)
		$post['u_'.$field] = $val;
	$post['headerbar'] = 'Post preview';
}

$breadcrumb = [
	'forum.php?id='.$thread['forum'] => $thread['f_title'],
	'thread.php?id='.$thread['id'] => $thread['title']
];

echo twigloader()->render('newreply.twig', [
	'breadcrumb' => $breadcrumb,
	'thread' => $thread,
	'message' => $message,
	'error' => $error,
	'post' => $post ?? null,
	'action' => $action
]);

