<?php
session_start();

require_once 'php/parseData.php';
require_once 'php/controllers/UserController.php';
$userController = new UserController();

if (isset($_SESSION['user_id'])) {
	header("Location: /camera.php");
	die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = parseData($_POST['username']);
	$pass = parseData($_POST['pass']);

	if ($userController->login($user, $pass)) {
		$_SESSION['user_id'] = $userController->getUserByUsername($user)['id'];
		$_SESSION['user_name'] = $user;
		header("Location: /camera.php");
	} else {
		echo "invalid UserName or Password";
	}
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
	<link rel="shortcut icon" href="/img/logo.png" type="image/x-icon" />
</head>

<body>
	<?php include 'components/header.php'; ?>
	<main>
		<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
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
			<p>Don't have an account? <a href="register.php">Register</a></p>
		</form>
		<div class="resetEmail">
			<details>
				<summary>¿Forgot your password?</summary>
				<form action="" method="post">
					<input
						required
						type="email"
						name="email"
						id="email"
						placeholder="Email address" />
					<button type="submit">Reset password</button>
				</form>
			</details>
		</div>
	</main>
	<?php include 'components/footer.html'; ?>
</body>

</html>