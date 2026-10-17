<?php
needsLogin();

$fieldlist = userfields('u').postfields_user();

$pid = $_GET['id'] ?? null;

$pmsg = fetch("SELECT $fieldlist p.* FROM pmsgs p LEFT JOIN users u ON u.id = p.userfrom WHERE p.id = ?", [$pid]);
if (!$pmsg) error('404');
$tologuser = ($pmsg['userto'] == $userdata['id']);

if ((!$tologuser && $pmsg['userfrom'] != $userdata['id']) && !IS_ROOT)
	error('404');
elseif ($tologuser && $pmsg['unread'])
	query("UPDATE pmsgs SET unread = 0 WHERE id = ?", [$pid]);

$pmsg['id'] = 0;

twigloader()->display('showprivate.twig', [
	'pid' => $pid,
	'pmsg' => $pmsg
]);
