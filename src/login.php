<?php
session_start();

require_once "php/parseData.php";

if (isset($_SESSION['user'])) {
	header("Location: /camera.php");
	die();
}

if (isset($_POST['login'])) {
	$user = parserData($_POST['user']);
	$pass = parserData($_POST['pass']);

	if ($user == "Ank" && $pass == "1234") {

		$_SESSION['use'] = $user;


		echo '<script type="text/javascript"> window.open("camera.php","_self");</script>';
	} else {
		// TODO -> give proper error
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
	<link rel="stylesheet" href="css/_general.css" />
	<link rel="stylesheet" href="css/accountForms.css" />
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
</head>

<body>
	<?php include 'header.php'; ?>
	<main>
		<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<h2>Login</h2>
			<input
				required
				type="text"
				name="username"
				id="username"
				placeholder="Username or Email" />
			<input
				required
				type="password"
				name="pass"
				id="pass"
				placeholder="Password" />
			<button type="submit">Access account</button>
		</form>
	</main>
	<?php include 'footer.html'; ?>
</body>

</html>