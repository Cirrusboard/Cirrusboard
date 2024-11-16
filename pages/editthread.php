<?php
needsLogin();

if (IS_BANNED) error('403', 'You are banned and cannot edit threads.');

$id = $_GET['id'] ?? null;

$thread = fetch("SELECT t.*, f.title forum_title, f.id forum_id, f.minread, f.minreply FROM threads t
			JOIN forums f ON f.id = t.forum
			WHERE t.id = ? AND ? >= f.minread",
		[$id, $userdata['rank']]);

if (!$thread) error('404');

$threadcreator = result("SELECT user FROM threads WHERE id = ?", [$id]);

if ((!IS_MOD && $userdata['id'] != $threadcreator) || ($thread['minread'] > $userdata['rank']))
	error('403');

if (!IS_MOD && $thread['closed'])
	error('403', __("This thread is closed."));

if (isset($_POST['action'])) {
	$title = $_POST['title'] ?? '';

	if (trim($title) != '' && $title != $thread['title'])
		query("UPDATE threads SET title = ? WHERE id = ?", [$title, $id]);

	if (IS_MOD) {
		$moveforum = $_POST['forumselect'] ?? null;

		if ($moveforum && $moveforum != $thread['forum_id'])
			movethread($thread['id'], $moveforum);

		$close = isset($_POST['close']) ? 1 : 0;
		$sticky = isset($_POST['sticky']) ? 1 : 0;

		query("UPDATE threads SET closed = ?, sticky = ? WHERE id = ?",
			[$close, $sticky, $id]);
	}

	redirect("thread?id=$id");
}

$breadcrumb = [
	'forum?id='.$thread['forum_id'] => $thread['forum_title'],
	'thread?id='.$thread['id'] => $thread['title']];

twigloader()->display('editthread.twig', [
	'id' => $id,
	'thread' => $thread,
	'breadcrumb' => $breadcrumb
]);
