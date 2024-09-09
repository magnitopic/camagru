<?php
session_start();

require_once 'php/parseData.php';
require_once 'php/controllers/UserController.php';
$userController = new UserController();

if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = parseData($_POST['username']);
	$email = parseData($_POST['email']);
	$pass = parseData($_POST['pass']);
	$emailPreference = $_POST['emailPreference'];
	$id = $_SESSION['user_id'];

	if (!$userController->updateUserData($id, $username, $email, $pass, $emailPreference)) {
		echo 'Error: Could not update user data.';
		exit();
	}
	$_SESSION['user_name'] = $username;
}

$user = $userController->getUserById($_SESSION['user_id']);
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
		<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<h2>Change your settings</h2>
			<input
				type="text"
				name="username"
				id="username"
				placeholder="Change username"
				autofocus
				value="<?php echo $user->username ?>"
				required />
			<input
				type="email"
				name="email"
				id="email"
				placeholder="Change email"
				value="<?php echo $user->email ?>"
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
						<?php echo $user->emailCommentPreference ? 'checked' : ''; ?>
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