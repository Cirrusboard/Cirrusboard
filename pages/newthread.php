<?php
needsLogin();

$fid = $_GET['id'] ?? null;
$action = $_POST['action'] ?? null;
$title = $_POST['title'] ?? '';
$message = $_POST['message'] ?? '';

$forum = fetch("SELECT id, title, minthread FROM forums WHERE id = ?", [$fid]);

if (!$forum)
	error('404');
if ($forum['minthread'] > $userdata['rank'])
	error('403');

$error = [];

if ($action == __('Submit')) {
	if (strlen(trim($title)) < 10)
		$error[] = __("You need to enter a longer title.");

	if (strlen(trim($message)) == 0)
		$error[] = __("You need to enter a message to your thread.");

	if ($userdata['lastpost'] > time() - 30 && !IS_ROOT)
		$error[] = __("Please wait at least 30 seconds before starting a new thread.");

	if ($error == []) {
		$tid = newThread([
			'forum' => $fid,
			'title' => $title,
			'ip' => $ipaddr,
			'message' => $message,
			'u_id' => $userdata['id'],
			'u_name' => $userdata['name']
		]);

		redirect("thread?id=$tid");
	}
} elseif ($action == __('Preview')) {
	$post['date'] = time();
	$post['text'] = $message;
	foreach ($userdata as $field => $val)
		$post['u_'.$field] = $val;
	$post['headerbar'] = __('Post preview');
}

$breadcrumb = [
	'forum?id='.$forum['id'] => $forum['title']];

twigloader()->display('newthread.twig', [
	'breadcrumb' => $breadcrumb,
	'threadtitle' => $title,
	'message' => $message,
	'error' => $error,
	'post' => $post ?? null,
	'action' => $action
]);
