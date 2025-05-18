<?php
needsLogin();

$id = $_GET['id'] ?? null;
$action = $_POST['action'] ?? null;
$message = trim($_POST['message'] ?? '');

$thread = fetch("SELECT t.*, f.title f_title, f.minreply f_minreply
	FROM threads t LEFT JOIN forums f ON f.id = t.forum
	WHERE t.id = ?", [$id]);

if (!$thread)
	error('404');
if ($thread['f_minreply'] > $userdata['rank'])
	error('403');
if ($thread['closed'] && !IS_MOD)
	error('403', __("You can't post in closed threads."));

$error = [];

if ($action == __("Submit")) {
	$lastpost = fetch("SELECT id, user, date FROM posts WHERE thread = ? ORDER BY id DESC LIMIT 1", [$thread['id']]);
	if ($lastpost['user'] == $userdata['id'] && $lastpost['date'] >= (time() - 60*60*12))
		$error[] = __("You can't double post until it's been at least 12 hours!");

	if (strlen($message) == 0)
		$error[] = __("Your post is empty. Enter a message and try again.");

	if ($error == []) {
		insertInto('posts', [
			'user' => $userdata['id'],
			'thread' => $id,
			'date' => time(),
			'ip' => $ipaddr
		]);

		$pid = insertId();
		insertInto('poststext', ['id' => $pid, 'text' => $message]);

		query("UPDATE threads SET posts = posts + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[time(), $userdata['id'], $pid, $id]);

		query("UPDATE forums SET posts = posts + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[time(), $userdata['id'], $pid, $thread['forum']]);

		query("UPDATE users SET posts = posts + 1, lastpost = ? WHERE id = ?", [time(), $userdata['id']]);

		redirect("thread?pid=$pid#$pid");
	}
} elseif ($action == __('Preview')) {
	$post['date'] = time();
	$post['text'] = $message;
	foreach ($userdata as $field => $val)
		$post['u_'.$field] = $val;
	$post['headerbar'] = __('Post preview');
}

// Append quoted message to the newreply box, to reply to other messages.
$pid = $_GET['pid'] ?? 0;
if ($pid) {
	$qpost = fetch("SELECT u.name name, p.user, pt.text, f.id fid, p.thread, f.minread
				FROM posts p
				LEFT JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
				LEFT JOIN users u ON p.user = u.id
				LEFT JOIN threads t ON t.id = p.thread
				LEFT JOIN forums f ON f.id = t.forum
				WHERE p.id = ?",
			[$pid]);

	//does the user have reading access to the quoted post?
	if ($userdata['rank'] < $qpost['minread'])
		$qpost['name'] = $qpost['text'] = '[redacted]';

	$message = sprintf(
		'[quote="%s" id="%s"]%s[/quote]'.PHP_EOL.PHP_EOL,
	$qpost['name'], $pid, $qpost['text']);
}

$breadcrumb = [
	'forum?id='.$thread['forum'] => $thread['f_title'],
	'thread?id='.$thread['id'] => $thread['title']
];

$fieldlist = userfields('u');
$newestposts = query("SELECT $fieldlist u.posts u_posts, p.*, pt.text
			FROM posts p
			LEFT JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
			LEFT JOIN users u ON p.user = u.id
			WHERE p.thread = ? AND p.deleted = 0
			ORDER BY p.id DESC LIMIT 7", [$id]);

twigloader()->display('newreply.twig', [
	'id' => $id,
	'breadcrumb' => $breadcrumb,
	'thread' => $thread,
	'message' => $message,
	'error' => $error,
	'post' => $post ?? null,
	'action' => $action,
	'newestposts' => $newestposts
]);
