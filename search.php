<?php
require('lib/common.php');

$query = trim($_GET['query'] ?? '');
$where = $_GET['where'] ?? 0;

if ($query && $where == 1) {
	// Search by post text (list threadposts)

	$fieldlist = postfields_user();
	$posts = query("SELECT $userfields $fieldlist p.*, pt.text, pt.date ptdate, pt.revision cur_revision, t.id tid, t.title ttitle, t.forum tforum
			FROM posts p
			JOIN poststext pt ON p.id = pt.id AND p.revision = pt.revision
			JOIN users u ON p.user = u.id
			JOIN threads t ON p.thread = t.id
			JOIN forums f ON f.id = t.forum
			WHERE pt.text LIKE CONCAT('%', ?, '%') AND ? >= f.minread
			ORDER BY p.id DESC LIMIT 20",
		[$query, $userdata['rank']]);

} elseif ($query) {
	// Search by thread title (list threads)

	$threads = query("SELECT $userfields t.*
		FROM threads t
		JOIN users u ON u.id = t.user
		JOIN forums f ON f.id = t.forum
		WHERE t.title LIKE CONCAT('%', ?, '%') AND ? >= f.minread
		ORDER BY t.lastdate DESC",
	[$query, $userdata['rank']]);

}

twigloader()->display('search.twig', [
	'query' => $query,
	'where' => $where,
	'threads' => $threads ?? null,
	'posts' => $posts ?? null
]);
