<?php
require('lib/common.php');

$id = $_GET['id'] ?? null;

$thread = fetch("SELECT t.*, f.title forum_title, f.id forum_id, f.minread, f.minreply FROM threads t
			JOIN forums f ON f.id = t.forum
			WHERE t.id = ? AND ? >= f.minread",
		[$id, $userdata['rank']]);

if (!$thread) error('404', "This thread doesn't exist.");

$threadcreator = result("SELECT user FROM threads WHERE id = ?", [$id]);

if ($userdata['rank'] < 3 && $userdata['id'] != $threadcreator) error('403', "You are not allowed to edit this thread.");

if (isset($_POST['action'])) {
	$title = $_POST['title'] ?? '';

	if (trim($title) != '') {
		query("UPDATE threads SET title = ? WHERE id = ?", [$title, $id]);

		redirect("thread.php?id=$id");
	}
}

$breadcrumb = [
	'forum.php?id='.$thread['forum_id'] => $thread['forum_title'],
	'thread.php?id='.$thread['id'] => $thread['title']];

echo twigloader()->render('editthread.twig', [
	'id' => $id,
	'thread' => $thread,
	'breadcrumb' => $breadcrumb
]);
