<?php
needsLogin();

$page = (int)($_GET['page'] ?? 1);
$view = $_GET['view'] ?? 'read';
$sent = $view == 'sent';

if ($sent) {
	$fieldn = 'to';
	$fieldn2 = 'from';
} else {
	$fieldn = 'from';
	$fieldn2 = 'to';
}

if (isset($_GET['action']) && $_GET['action'] == "del") {
	$pmid = $_GET['id'] ?? null;
	$owner = result("SELECT user$fieldn2 FROM pmsgs WHERE id = ?", [$pmid]);
	if ($owner == $userdata['id'])
		query("UPDATE pmsgs SET del_$fieldn2 = 1 WHERE id = ?", [$pmid]);
}

$ptitle = 'Private messages' . ($sent ? ' (sent)' : '');

$pmsgc = result("SELECT COUNT(*) FROM pmsgs
		WHERE user$fieldn2 = ? AND del_$fieldn2 = 0",
	[$userdata['id']]);

$ufields = userfields('u');
$pmsgs = query("SELECT $ufields p.* FROM pmsgs p
		LEFT JOIN users u ON u.id = p.user$fieldn
		WHERE p.user$fieldn2 = ? AND del_$fieldn2 = 0
		ORDER BY p.unread DESC, p.date DESC
		".paginate($page, $tpp),
	[$userdata['id']]);

if ($pmsgc > $tpp)
	$pagelist = pagination($pmsgc, $tpp, 'private?view='.$view.'&page=%s', $page);

twigloader()->display('private.twig', [
	'pmsgs' => $pmsgs,
	'pmsgc' => $pmsgc,
	'fieldn' => $fieldn,
	'view' => $view,
	'sent' => $sent,
	'page' => $page,
	'pagelist' => $pagelist ?? null,
]);
