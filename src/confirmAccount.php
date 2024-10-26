<?php
session_start();
require_once 'php/controllers/UserController.php';
require_once "php/parseData.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	header("Location: /login.php");
	exit();
}

if (!isset($_GET['token']) || empty($_GET['token'])) {
	header("Location: /login.php");
	exit();
}

error_log("Working on token: " . $_GET['token']);

$token = parseData($_GET['token']);
$userController = new UserController();
$userData = $userController->getUserByUsername("alaparic");
error_log("User token in db: " . json_encode($userData));
$result = $userController->confirmAccount($token);

error_log("Result: " . json_encode($result));
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Camagru | Email verification</title>
	<link rel="stylesheet" href="/css/_general.css">
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
</head>

<body>
	<?php include 'components/header.php'; ?>

	<center>

		<h1>Email confirmation</h1>

		<?php
		if ($result['success']) {
			echo "<p>Email confirmed successfully! You can now log in.</p>";
		} else {
			echo "<p>Invalid confirmation token. Please try again.</p>";
		}
		?>
		<br>
		<a href="/login.php">Go to login</a>
		<br>
		<br>
		<br>

	</center>

	<?php include 'components/footer.html'; ?>

</body>

</html>