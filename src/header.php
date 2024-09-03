<?php session_start(); ?>
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
				<p class="userName"><?php echo $_SESSION["username"] ?></p> <!-- TODO -> get user name -->
			<?php else : ?>
				<a href="/login.php">LogIn</a>
				<a href="/register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>