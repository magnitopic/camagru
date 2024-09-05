<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: /login.php");
	exit();
}
?>
<link rel="stylesheet" href="css/_general.css" />
<link rel="stylesheet" href="css/header.css" />
<header>
	<a href="/" class="header-logo-container">
		<img src="img/logo.png" alt="logo" />
		<h1>Camagru</h1>
	</a>
	<div>
		<nav>
			<a href="/gallery.php">Gallery</a>
			<a href="/camera.php">Camera</a>
			<?php if (isset($_SESSION["user_id"])) : ?>
				<details>
					<summary class="userName"><?php echo $_SESSION["user_name"] ?></summary>
					<a href="/preferences.php">User Preferences</a>
					<a class="logout" href="/logout.php">Logout</a>
				</details>
			<?php else : ?>
				<a href="/login.php">LogIn</a>
				<a href="/register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>