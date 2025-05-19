<?php
needsLogin();

$pid = $_GET['pid'] ?? null;
$action = $_POST['action'] ?? null;

if (IS_BANNED) error('403', __('You are banned and cannot edit posts.'));

// Post deletion, stuffed into editpost because why not.
if (isset($_GET['delete']) || isset($_GET['undelete'])) {
	if (!IS_MOD)
		error('403');

	query("UPDATE posts SET deleted = ? WHERE id = ?", [(isset($_GET['delete']) ? 1 : 0), $pid]);

	redirect("thread?pid=$pid#$pid");
}

$thread = fetch("SELECT p.user p_user, t.*, f.title f_title
			FROM posts p
			LEFT JOIN threads t ON t.id = p.thread
			LEFT JOIN forums f ON f.id = t.forum
			WHERE p.id = ? AND ? >= f.minread",
		[$pid, $userdata['rank']]);

if (!$thread)
	error('404');
if ($thread['closed'] && !IS_MOD)
	error('403', __("You can't edit a post in closed threads."));
if ($userdata['id'] != $thread['p_user'] && !IS_ROOT)
	error('403');

$editpost = fetch("SELECT u.id, p.user, p.text, p.editdate, p.date FROM posts p
			LEFT JOIN users u ON p.user = u.id
			WHERE p.id = ?",
		[$pid]);

$error = [];

$message = $_POST['message'] ?? $editpost['text'];

if ($action == __('Submit')) {
	if ($message == $editpost['text'])
		$error[] = __("No changes detected.");
	if (strlen($message) < 15)
		$error[] = __("You can't blank out your post!");

	if ($error == []) {
		$rev = result("SELECT revision FROM posts WHERE id = ?", [$pid]);

		query("UPDATE posts SET revision = ?, editdate = ?, text = ? WHERE id = ?", [$rev + 1, time(), $message, $pid]);

		insertInto('poststext', [
			'id' => $pid,
			'text' => $editpost['text'],
			'revision' => $rev,
			'date' => $editpost['editdate'] ?: null,
		]);

		redirect("thread?pid=$pid#$pid");
	}
} elseif ($action == __('Preview')) {
	$euser = fetch("SELECT * FROM users WHERE id = ?", [$editpost['id']]);
	$post['date'] = time();
	$post['text'] = $message;
	foreach ($euser as $field => $val)
		$post['u_'.$field] = $val;
	$post['headerbar'] = __('Post preview');
}

$breadcrumb = [
	'forum?id='.$thread['forum'] => $thread['f_title'],
	'thread?id='.$thread['id'] => $thread['title']
];

twigloader()->display('editpost.twig', [
	'thread' => $thread,
	'post' => $post ?? null,
	'action' => $action,
	'pid' => $pid,
	'message' => $message,
	'breadcrumb' => $breadcrumb,
	'error' => $error
]);
