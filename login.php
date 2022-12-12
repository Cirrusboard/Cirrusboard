<?php
require('lib/common.php');

$error = '';

$action = $_POST['action'] ?? null;

if ($action == 'Login') {
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
} elseif ($action == 'Logout') {
	// Destroy the token cookie to log out the user.
	setcookie('token', '');
	redirect('./');
}

echo twigloader()->render('login.twig', [
	'error' => $error,
	'name' => $name ?? null
]);
