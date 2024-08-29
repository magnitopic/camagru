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

	<?php include 'header.html'; ?>

	<main>
		<form method="post">
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