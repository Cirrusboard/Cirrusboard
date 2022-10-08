<?php
require('lib/common.php');

$error = '';

if (isset($_POST['action'])) {
	$name = $_POST['name'] ?? null;
	$pass = $_POST['password'] ?? null;

	$login = fetch("SELECT id,password,token FROM users WHERE name = ?", [$name]);

	// Don't give any more details than just "invalid credentials".

	if (!$name || !$pass || !$login || !password_verify($pass, $login['password']))
		$error = 'Invalid credentials.';

	if ($error == '') {
		setcookie('token', $login['token'], 2147483647);

		// Redirect to index... or should it redirect to the last page?
		redirect('./');
	}
}

$twig = twigloader();
echo $twig->render('login.twig', [
	'error' => $error,
	'name' => $name ?? null
]);
