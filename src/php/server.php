<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "camagru";
$conn = "";

try {
	$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);
} catch (Exception $e) {
	echo "Connection failed: " . $e->getMessage();
}

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
} else
	echo "Connected successfully";
