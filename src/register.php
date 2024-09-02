<?php
session_start();

require __DIR__ . "php/parseData.php";

if (isset($_SESSION['user'])) {
	header("Location: /camera.php");
	die();
}

if (isset($_POST['login'])) {
	$user = parserData($_POST['user']);
	$pass = parserData($_POST['pass']);
	$repeatPass = parserData($_POST['repeatPass']);

	if ($pass != $repeatPass)
		echo "Passwords don't match";
	elseif ($user == "Ank" && $pass == "1234") { // TODO -> check database values
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
	<title>Camagru | Register</title>
	<link rel="stylesheet" href="css/_general.css" />
	<link rel="stylesheet" href="css/accountForms.css" />
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
</head>

<body>

	<?php include 'header.php'; ?>

	<main>
		<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
			<h2>Register</h2>
			<input required type="text" name="" id="username" placeholder="Username" />
			<input required type="email" name="" id="email" placeholder="Email" />
			<input required minlength="3" maxlength="30" type="password" name="" id="pass" placeholder="Password" />
			<input required minlength="3" maxlength="30" type="password" name="repeatPass" id="" placeholder="Repeat Password" />
			<button type="submit">Create account</button>
		</form>
	</main>

	<?php include 'footer.html'; ?>
</body>

</html>