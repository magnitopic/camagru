<?php
session_start();

require_once 'php/controllers/UserController.php';
require_once "php/parseData.php";

if (isset($_SESSION['user'])) {
	header("Location: /camera.php");
	die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = parseData($_POST['username']);
	$email = parseData($_POST['email']);
	$pass = parseData($_POST['pass']);
	$repeatPass = parseData($_POST['repeatPass']);

	if ($pass != $repeatPass)
		echo "Passwords don't match";
	else {
		$userController = new UserController();
		$userController->register($username, $email, $pass);
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Camagru | Register</title>
	<link rel="stylesheet" href="css/_general.css" />
	<link rel="stylesheet" href="css/accountForms.css" />
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
</head>

<body>

	<?php include 'header.php'; ?>

	<main>
		<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<h2>Register</h2>
			<input required type="text" name="username" id="username" placeholder="Username" />
			<input required type="email" name="email" id="email" placeholder="Email" />
			<input required minlength="3" maxlength="30" type="password" name="pass" id="pass" placeholder="Password" />
			<input required minlength="3" maxlength="30" type="password" name="repeatPass" id="repeatPass" placeholder="Repeat Password" />
			<button type="submit">Create account</button>
		</form>
	</main>

	<?php include 'footer.html'; ?>
</body>

</html>