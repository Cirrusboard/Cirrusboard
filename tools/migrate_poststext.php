#!/usr/bin/php
<?php

print("Cirrusboard tools - migrate_poststext.php\n");
print("===========================================\n");
print("Migrate poststext data to the new format,\n");
print("with latest rev stored in posts table.\n");

require('lib/common.php');

// Get the latest poststext revisions for each post
$latestRevisions =  $sql->query("
	SELECT pt1.id, pt1.text, pt1.date
	FROM poststext pt1
	INNER JOIN (
		SELECT id, MAX(revision) AS max_revision
		FROM poststext
		GROUP BY id
	) pt2 ON pt1.id = pt2.id AND pt1.revision = pt2.max_revision")->fetchAll(PDO::FETCH_ASSOC);

// Update each post row
foreach ($latestRevisions as $rev)
	query("UPDATE posts SET text = ?, editdate = ? WHERE id = ?", [$rev['text'], $rev['date'], $rev['id']]);

// Cleanup poststext
$latestRevisions = query("SELECT id, revision FROM posts")->fetchAll(PDO::FETCH_ASSOC);

foreach ($latestRevisions as $post)
	query("DELETE FROM poststext WHERE id = ? AND revision = ?", [$post['id'], $post['revision']]);

echo "Migration completed successfully!\n";
