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
