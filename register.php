<?php
require('lib/common.php');

$error = [];

if (isset($_POST['action'])) {
	$name = trim($_POST['name'] ?? null);
	$pass = $_POST['password'] ?? null;
	$pass2 = $_POST['password2'] ?? null;

	// Check to see user should be able to register...

	if (!$name)
		$error[] = 'Blank username.';

	if (!$pass || strlen($pass) < 15)
		$error[] = 'Password is too short (needs to be at least 15 characters).';

	if (!$pass2 || $pass != $pass2)
		$error[] = "The passwords don't match.";

	if (result("SELECT COUNT(*) FROM users WHERE LOWER(name) = ?", [strtolower($name)]))
		$error[] = "Username has already been taken.";

	if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $name))
		$error[] = "Username contains invalid characters (Only alphanumeric and underscore allowed). ";

	if (result("SELECT COUNT(*) FROM users WHERE ip = ?", [$ipaddr]))
		$error[] = "Creating multiple accounts (alts) aren't allowed. ";

	// If no error found, it will register and redirect to index page.
	// Otherwise register page will be shown again, with $error displayed to the user.

	if ($error == []) {
		// Generate a random 64-length hexadecimal string for token.
		$token = bin2hex(random_bytes(32));

		insertInto('users', [
			'name' => $name,
			'password' => password_hash($pass, PASSWORD_DEFAULT),
			'token' => $token,
			'joined' => time()
		]);

		$id = insertId();
		// If user is ID 1, make them root.
		if ($id == 1) query("UPDATE users SET rank = 4 WHERE id = ?", [$id]);

		// Log in user right away.
		setcookie('token', $token, 2147483647);

		redirect('/?rd');
	}
}

echo twigloader()->render('register.twig', [
	'error' => $error,
	'name' => $name ?? null
]);