<?php
require('lib/common.php');

$action = $_GET['action'] ?? '';

//mark forum read
if ($log && $action == 'markread') {
	$fid = $_GET['fid'];
	if ($fid != 'all') {
		// Delete obsolete threadsread entries
		query("DELETE r FROM threadsread r LEFT JOIN threads t ON t.id = r.tid WHERE t.forum = ? AND r.uid = ?", [$fid, $userdata['id']]);
		// Add new forumsread entry
		query("REPLACE INTO forumsread VALUES (?,?,?)", [$userdata['id'], $fid, time()]);
		// Redirect back to forum page
		redirect("forum.php?id=$fid");
	} else {
		// Mark all read
		query("DELETE FROM threadsread WHERE uid = ?", [$userdata['id']]);
		query("REPLACE INTO forumsread (uid, fid, time) SELECT ?, f.id, ? FROM forums f", [$userdata['id'], time()]);
		// Redirect back to index
		redirect('./');
	}
}

$categs = query("SELECT id, title FROM categories ORDER BY ord, id");
while ($c = $categs->fetch())
	$categories[$c['id']] = $c['title'];

$readtime = ($log ? "r.time rtime," : '');
$forumsread = "LEFT JOIN forumsread r ON r.fid = f.id AND r.uid = ".$userdata['id'];

$forums = query("SELECT $userfields $readtime f.* FROM forums f
		LEFT JOIN users u ON u.id = f.lastuser
		JOIN categories c ON c.id = f.cat
		$forumsread
		WHERE ? >= f.minread
		ORDER BY c.ord, c.id, f.ord, f.id",
	[$userdata['powerlevel']]);

// Get latest announcement to show at the top
$news = fetch("SELECT $userfields t.id tid, t.title, t.user, p.date date
			FROM threads t
			JOIN users u ON u.id = t.user
			JOIN posts p ON p.thread = t.id
			WHERE t.forum = ? ORDER BY t.lastdate DESC LIMIT 1",
		[$config['newsid']]);

// Online users stats
$args = [(time() - 15*60)];

$onlineUsers = query("SELECT id,name,powerlevel,lastview FROM users WHERE lastview > ? ORDER BY name", $args);
$onlineUsersCount = result("SELECT COUNT(*) FROM users WHERE lastview > ?", $args);

$guestsOnline = result("SELECT COUNT(*) guests FROM guests WHERE lastview > ?", $args);


$stats = fetch("SELECT (SELECT COUNT(*) FROM users) u, (SELECT COUNT(*) FROM threads) t, (SELECT COUNT(*) FROM posts) p");

$newestUser = fetch("SELECT id, name, powerlevel FROM users ORDER BY id DESC LIMIT 1");

echo twigloader()->render('index.twig', [
	'categories' => $categories,
	'forums' => $forums,
	'just_registered' => isset($_GET['rd']),
	'online_users' => $onlineUsers,
	'online_users_count' => $onlineUsersCount,
	'guests_online' => $guestsOnline,
	'stats' => $stats,
	'newestuser' => $newestUser,
	'news' => $news
]);
