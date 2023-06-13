<?php
require('lib/common.php');

needsLogin();

$pid = $_GET['pid'] ?? null;
$action = $_POST['action'] ?? null;

// Post deletion, stuffed into editpost because why not.
if (isset($_GET['delete']) || isset($_GET['undelete'])) {
	if ($userdata['rank'] <= 1)
		error('403', "You do not have the permission to do this.");

	query("UPDATE posts SET deleted = ? WHERE id = ?", [(isset($_GET['delete']) ? 1 : 0), $pid]);

	redirect("thread.php?pid=$pid#$pid");
}


$thread = fetch("SELECT p.user p_user, t.*, f.title f_title
			FROM posts p
			LEFT JOIN threads t ON t.id = p.thread
			LEFT JOIN forums f ON f.id = t.forum
			WHERE p.id = ? AND ? >= f.minread",
		[$pid, $userdata['rank']]);

if (!$thread)
	error('404', "Invalid post ID.");
if ($thread['closed'] && $userdata['rank'] <= 1)
	error('403', "You can't edit a post in closed threads.");
if ($userdata['rank'] < 3 && $userdata['id'] != $thread['p_user'])
	error('403', "You do not have permission to edit this post.");

$editpost = fetch("SELECT u.id, p.user, pt.text FROM posts p
			LEFT JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
			LEFT JOIN users u ON p.user = u.id
			WHERE p.id = ?",
		[$pid]);

$error = [];

$message = $_POST['message'] ?? $editpost['text'];

if ($action == 'Submit') {
	if ($message == $editpost['text'])
		$error[] = "No changes detected.";
	if (strlen($message) < 15)
		$error[] = "You can't blank out your post!";

	if ($error == []) {
		$newrev = result("SELECT revision FROM posts WHERE id = ?", [$pid]) + 1;

		query("UPDATE posts SET revision = ? WHERE id = ?", [$newrev, $pid]);

		query("INSERT INTO poststext (id, text, revision, date) VALUES (?,?,?,?)",
			[$pid, $_POST['message'], $newrev, time()]);

		redirect("thread.php?pid=$pid#$pid");
	}
} elseif ($action == 'Preview') {
	$euser = fetch("SELECT * FROM users WHERE id = ?", [$editpost['id']]);
	$post['date'] = time();
	$post['text'] = $message;
	foreach ($euser as $field => $val)
		$post['u_'.$field] = $val;
	$post['headerbar'] = 'Post preview';
}

$breadcrumb = [
	'forum.php?id='.$thread['forum'] => $thread['f_title'],
	'thread.php?id='.$thread['id'] => $thread['title']
];

echo twigloader()->render('editpost.twig', [
	'thread' => $thread,
	'post' => $post ?? null,
	'action' => $action,
	'pid' => $pid,
	'message' => $message,
	'breadcrumb' => $breadcrumb,
	'error' => $error
]);
