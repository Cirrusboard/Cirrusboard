#!/usr/bin/php
<?php

print("Cirrusboard tools - recalc.php\n");
print("===========================================\n");
print("Recalculate all the things in the database.\n");
print("Useful after permanent data deletion.\n");

require('lib/common.php');

// Recalculate threads (post count, last post data...)

$threads = query("SELECT id FROM threads");
while ($thread = $threads->fetch()) {
	// Fetch post count
	$posts = result("SELECT COUNT(*) FROM posts WHERE thread = ?", [$thread['id']]);

	// Fetch last post data
	$lastpost = fetch("SELECT id, user, date FROM posts WHERE thread = ? ORDER BY id DESC LIMIT 1", [$thread['id']]);

	query("UPDATE threads SET posts = ?, lastid = ?, lastuser = ?, lastdate = ? WHERE id = ?",
		[$posts, $lastpost['id'], $lastpost['user'], $lastpost['date'], $thread['id']]);
}

// Recalculate forums (threads, posts, last thread data...)
// (done after threads because then thread data is accurate and can be made use of)

$forums = query("SELECT id FROM forums");

while ($forum = $forums->fetch()) {
	$counts = fetch("SELECT (SELECT COUNT(*) FROM threads WHERE forum = ?) threads, (SELECT SUM(posts) FROM threads WHERE forum = ?) posts",
	[$forum['id'], $forum['id']]);

	if (!isset($counts['posts'])) {
		$counts = ['threads' => 0, 'posts' => 0];
	}

	// Fetch last post data
	$lastpost = fetch("SELECT lastid, lastuser, lastdate FROM threads WHERE forum = ? ORDER BY lastdate DESC LIMIT 1",
		[$forum['id']]);

	if (!$lastpost) {
		$lastpost = ['lastid' => null, 'lastuser' => null, 'lastdate' => null];
	}

	query("UPDATE forums SET threads = ?, posts = ?, lastid = ?, lastuser = ?, lastdate = ? WHERE id = ?",
		[$counts['threads'], $counts['posts'], $lastpost['lastid'], $lastpost['lastuser'], $lastpost['lastdate'], $forum['id']]);

}

// Recalculate user post and thread count.

$users = query("SELECT id FROM users");

while ($user = $users->fetch()) {
	$counts = fetch("SELECT (SELECT COUNT(*) FROM threads WHERE user = ?) threads, (SELECT COUNT(*) FROM posts WHERE user = ?) posts",
	[$user['id'], $user['id']]);

	query("UPDATE users SET threads = ?, posts = ? WHERE id = ?", [$counts['threads'], $counts['posts'], $user['id']]);
}


