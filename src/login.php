<?php
session_start();

require_once 'php/parseData.php';
require_once 'php/controllers/UserController.php';
$userController = new UserController();

if (isset($_SESSION['user_id'])) {
	header("Location: /camera.php");
	die();
}

$response = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = parseData($_POST['username']);
	$pass = parseData($_POST['pass']);

	$response = $userController->login($username, $pass);

	if ($response['success']) {
		$_SESSION['user_id'] = $response['user']->id;
		$_SESSION['user_name'] = $response['user']->username;
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
	<title>Camagru | LogIn</title>
	<link rel="stylesheet" href="/css/_general.css" />
	<link rel="stylesheet" href="/css/accountForms.css" />
	<script defer src="/js/error.js"></script>
	<script defer src="/js/resetPass.js"></script>
	<link rel="shortcut icon" href="/img/logo.png" type="image/x-icon" />
</head>

<body>
	<?php include 'components/header.php'; ?>
	<main>
		<form id="loginForm" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<h2>Login</h2>
			<input
				required
				type="text"
				name="username"
				id="username"
				autofocus
				placeholder="Username" />
			<input
				required
				type="password"
				name="pass"
				id="pass"
				placeholder="Password" />
			<button type="submit">Access account</button>
			<div id="form-error-message" class="form-error-message"></div>
			<p>Don't have an account? <a href="register.php">Register</a></p>
		</form>
		<div class="resetEmail">
			<details>
				<summary>Forgot your password?</summary>
				<form id="resetPasswordForm" action="php/resetPass.php" method="post">
					<input
						required
						type="email"
						name="email"
						id="email"
						placeholder="Email address" />
					<button type="submit" id="reset-form-submit">Reset password</button>
					<div id="reset-form-message" class="form-message"></div>
				</form>
			</details>
		</div>
	</main>
	<?php include 'components/footer.html'; ?>
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const loginForm = document.getElementById("loginForm");
			handleFormSubmit(loginForm, '/camera.php');
		});
	</script>
</body>

</html>