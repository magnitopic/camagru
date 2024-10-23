<?php
session_start();

require_once 'php/controllers/UserController.php';
require_once "php/parseData.php";

if (isset($_SESSION['user_id'])) {
	header("Location: /camera.php");
	die();
}

$response = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = parseData($_POST['username']);
	$email = parseData($_POST['email']);
	$pass = parseData($_POST['pass']);
	$repeatPass = parseData($_POST['repeatPass']);

	if ($pass != $repeatPass) {
		$response = ['success' => false, 'errors' => ["Passwords don't match"]];
	} else {
		$userController = new UserController();
		$response = $userController->register($username, $email, $pass);
	}

	header('Content-Type: application/json');
	echo json_encode($response);
	exit;
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
	<script defer src="js/error.js"></script>
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
</head>

<body>
	<?php include 'components/header.php'; ?>
	<main>
		<form id="registerForm" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<h2>Register</h2>
			<input required type="text" name="username" id="username" placeholder="Username" autofocus />
			<input required type="email" name="email" id="email" placeholder="Email" />
			<input required minlength="3" maxlength="30" type="password" name="pass" id="pass" placeholder="Password" />
			<input required minlength="3" maxlength="30" type="password" name="repeatPass" id="repeatPass" placeholder="Repeat Password" />
			<button type="submit">Create account</button>
			<div id="form-error-message" class="form-error-message"></div>
			<p>Already have an account? <a href="login.php">LogIn</a></p>
		</form>
	</main>
	<?php include 'components/footer.html'; ?>
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const registerForm = document.getElementById("registerForm");
			handleFormSubmit(registerForm, '/camera.php');
		});
	</script>
</body>

</html>