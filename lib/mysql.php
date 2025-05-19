<?php

$options = [
	PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES		=> false,
];
try {
	$sql = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
	die("Error - Can't connect to database. Please try again later.");
}

function query($query,$params = []) {
	global $sql;

	$res = $sql->prepare($query);
	$res->execute($params);
	return $res;
}

function fetch($query,$params = []) {
	$res = query($query,$params);
	return $res->fetch();
}

function result($query,$params = []) {
	$res = query($query,$params);
	return $res->fetchColumn();
}

function fetchArray($query) {
	$out = [];
	while ($record = $query->fetch()) {
		$out[] = $record;
	}
	return $out;
}

function insertId() {
	global $sql;
	return $sql->lastInsertId();
}

function commasep($str) {
	return implode(',', $str);
}

function clamp($current, $min, $max) {
    return max($min, min($max, $current));
}

/**
 * Helper function to insert a row into a table.
 */
function insertInto($table, $data, $dry = false) {
	$fields = [];
	$placeholders = [];
	$values = [];

	foreach ($data as $field => $value) {
		$fields[] = $field;
		$placeholders[] = '?';
		$values[] = $value;
	}

	$query = sprintf(
		"INSERT INTO %s (%s) VALUES (%s)",
	$table, commasep($fields), commasep($placeholders));

	if ($dry)
		return $query;
	else
		return query($query, $values);
}
