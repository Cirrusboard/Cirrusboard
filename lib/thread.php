<?php

/**
 * Moves a thread to another forum, updating the threads/posts values and last* fields.
 */
function movethread($id, $forum) {
	if (!result("SELECT COUNT(*) FROM forums WHERE id = ?", [$forum])) return;

	$thread = fetch("SELECT forum, posts FROM threads WHERE id = ?", [$id]);
	query("UPDATE threads SET forum = ? WHERE id = ?", [$forum, $id]);

	$last1 = fetch("SELECT lastdate,lastuser,lastid FROM threads WHERE forum = ? ORDER BY lastdate DESC LIMIT 1", [$thread['forum']]);
	$last2 = fetch("SELECT lastdate,lastuser,lastid FROM threads WHERE forum = ? ORDER BY lastdate DESC LIMIT 1", [$forum]);
	if ($last1)
		query("UPDATE forums SET posts = posts - ?, threads = threads - 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[$thread['posts'], $last1['lastdate'], $last1['lastuser'], $last1['lastid'], $thread['forum']]);

	if ($last2)
		query("UPDATE forums SET posts = posts + ?, threads = threads + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
			[$thread['posts'], $last2['lastdate'], $last2['lastuser'], $last2['lastid'], $forum]);
}

function newPost($data) {
	if (!isset($data['time']))
		$data['time'] = time();

	insertInto('posts', [
		'user' => $data['u_id'],
		'thread' => $data['thread'],
		'date' => $data['time'],
		'ip' => $data['ip'],
		'text' => $data['message']
	]);

	$pid = insertId();
	$data['pid'] = $pid;

	query("UPDATE forums
			SET posts = posts + 1,
			lastdate = ?, lastuser = ?, lastid = ?
			WHERE id = ?",
		[$data['time'], $data['u_id'], $pid, $data['forum']]);

	query("UPDATE threads
			SET posts = posts + 1,
			lastdate = ?, lastuser = ?, lastid = ?
			WHERE id = ?",
		[$data['time'], $data['u_id'], $pid, $data['thread']]);

	query("UPDATE users SET posts = posts + 1 WHERE id = ?",
		[$data['u_id']]);

	if (!isset($data['newthread'])) {
		// nuke entries of this thread in the "threadsread" table
		query("DELETE FROM threadsread WHERE tid = ? AND NOT (uid = ?)", [$data['thread'], $data['u_id']]);
	}

	return $pid;
}

function newThread($data) {
	$data['newthread'] = true;
	if (!isset($data['time']))
		$data['time'] = time();

	insertInto('threads', [
		'title' => $data['title'],
		'forum' => $data['forum'],
		'user' => $data['u_id'],
	]);
	$tid = insertId();
	$data['thread'] = $tid;

	query("UPDATE forums SET threads = threads + 1 WHERE id = ?",
		[$data['forum']]);

	query("UPDATE users SET threads = threads + 1 WHERE id = ?",
		[$data['u_id']]);

	newPost($data);

	return $tid;
}
