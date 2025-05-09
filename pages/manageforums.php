<?php
if (!IS_ADMIN) error('403');

if (isset($_POST['savecat'])) { // save new/existing category

	$cid = $_GET['cid'];
	$title = $_POST['title'];
	$order = $_POST['ord'];

	if (!trim($title))
		error('400', 'Please enter a title for the category.');

	if ($cid == 'new') {
		$cid = (result("SELECT MAX(id) FROM categories") ?: 0)+1;

		insertInto('categories', [
			'id' => $cid, 'title' => $title, 'ord' => $order
		]);
	} else {
		if (!result("SELECT COUNT(*) FROM categories WHERE id = ?", [$cid]))
			redirect('manageforums');

		query("UPDATE categories SET title = ?, ord = ? WHERE id = ?", [$title, $order, $cid]);
	}
	redirect('manageforums?cid=%s', $cid);

} elseif (isset($_POST['delcat'])) { // delete category

	query("DELETE FROM categories WHERE id = ?", [$_GET['cid']]);
	redirect('manageforums');

} elseif (isset($_POST['saveforum'])) { // save new/existing forum

	$fid = $_GET['fid'];
	$cat = (int)$_POST['cat'];
	$title = $_POST['title'];
	$descr = $_POST['descr'];
	$ord = (int)$_POST['ord'];

	$minread = (int)$_POST['minread'];
	$minthread = (int)$_POST['minthread'];
	$minreply = (int)$_POST['minreply'];

	if (!trim($title))
		error('400', 'Please enter a title for the forum.');

	if ($fid == 'new') {
		$fid = (result("SELECT MAX(id) FROM forums") ?: 0)+1;

		insertInto('forums', [
			'id' => $fid, 'cat' => $cat, 'title' => $title, 'descr' => $descr, 'ord' => $ord,
			'minread' => $minread, 'minthread' => $minthread, 'minreply' => $minreply
		]);
	} else {
		$fid = (int)$fid;
		if (!result("SELECT COUNT(*) FROM forums WHERE id = ?", [$fid]))
			redirect('manageforums');

		query("UPDATE forums SET cat=?, title=?, descr=?, ord=?, minread=?, minthread=?, minreply=? WHERE id=?",
			[$cat, $title, $descr, $ord, $minread, $minthread, $minreply, $fid]);
	}
	redirect('manageforums?fid=%s', $fid);

} elseif (isset($_POST['delforum'])) { // delete forum

	query("DELETE FROM forums WHERE id = ?", [$_GET['fid']]);
	redirect('manageforums');
}

if (isset($_GET['cid']) && $cid = $_GET['cid']) { // category editor

	if ($cid == 'new')
		$cat = ['id' => 0, 'title' => '', 'ord' => 0];
	else
		$cat = fetch("SELECT * FROM categories WHERE id=?",[$cid]);

	twigloader()->display("manageforums_category.twig", [
		'cid' => $cid,
		'cat' => $cat
	]);

} elseif (isset($_GET['fid']) && $fid = $_GET['fid']) { // forum editor

	if ($fid == 'new') {
		$forum = [
			'id' => 0, 'cat' => 1, 'title' => '', 'descr' => '',
			'ord' => 0,
			'minread' => -1, 'minthread' => 1, 'minreply' => 1];
	} else
		$forum = fetch("SELECT * FROM forums WHERE id = ?", [$fid]);

	$qcats = query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = [];
	while ($cat = $qcats->fetch())
		$cats[$cat['id']] = $cat['title'];

	$perms = [
		'minread' => 'Who can view',
		'minthread' => 'Who can make threads',
		'minreply' => 'Who can reply'
	];

	twigloader()->display("manageforums_forum.twig", [
		'fid' => $fid,
		'forum' => $forum,
		'cats' => $cats,
		'ranks' => $ranks,
		'perms' => $perms
	]);

} else {
	// main page -- category/forum listing

	$qcats = query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = [];
	while ($cat = $qcats->fetch())
		$cats[$cat['id']] = $cat;

	$qforums = query("SELECT f.id,f.title,f.cat FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord, c.id, f.ord, f.id");
	$forums = [];
	while ($forum = $qforums->fetch())
		$forums[$forum['id']] = $forum;

	$catlist = ''; $c = 1;
	foreach ($cats as $cat) {
		$catlist .= sprintf('<tr><td class="n%s"><a href="manageforums?cid=%s">%s</a></td></tr>', $c, $cat['id'], $cat['title']);
		$c = ($c == 1) ? 2 : 1;
	}

	$forumlist = ''; $c = 1; $lc = -1;
	foreach ($forums as $forum) {
		if ($forum['cat'] != $lc) {
			$lc = $forum['cat'];
			$forumlist .= sprintf('<tr class="c"><td>%s</td></tr>', $cats[$forum['cat']]['title']);
		}
		$forumlist .= sprintf('<tr><td class="n%s"><a href="manageforums?fid=%s">%s</a></td></tr>', $c, $forum['id'], $forum['title']);
		$c = ($c == 1) ? 2 : 1;
	}

	twigloader()->display("manageforums.twig", [
		'catlist' => $catlist,
		'forumlist' => $forumlist
	]);
}
