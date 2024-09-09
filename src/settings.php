<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
	<link rel="stylesheet" href="css/_general.css" />
	<link rel="stylesheet" href="css/settigs.css" />
	<title>Camagru | Settings</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<main>
		<form action="" method="post">
			<h2>Change your settings</h2>
			<input
				type="text"
				name="username"
				id="username"
				placeholder="Change username"
				autofocus
				required />
			<input
				type="email"
				name="email"
				id="email"
				placeholder="Change email"
				required />
			<div class="passwordContainer">
				<input
					type="password"
					name="pass"
					id="pass"
					placeholder="Change password" />
				<label for="pass">
					<i>Leave password empty to not change it</i>
				</label>
			</div>
			<div class="switchContainer">
				<label class="switch">
					<input
						checked
						type="checkbox"
						name="emailPreference"
						id="emailPreference" />
					<span class="slider"></span>
				</label>
				<label for="emailPreference">
					Email notifications on new comments
				</label>
			</div>
			<button>Save settings</button>
		</form>
	</main>
	<?php include 'components/footer.html'; ?>
</body>

</html>