<?php
session_start();
?>
<link rel="stylesheet" href="css/_general.css" />
<link rel="stylesheet" href="css/header.css" />

<!-- Error message container -->
<div id="error-message" class="error-message"></div>

<!-- Header -->
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
				<script defer src="js/header.js"></script>
				<div class="userDropdown">
					<details class="loginDetails">
						<summary class="userName"><?php echo $_SESSION["user_name"] ?></summary>
					</details>
					<div class="dropdownContent" id="dropdownContent">
						<a href="/settings.php">Settings</a>
						<a class="logout" href="/php/logout.php">Logout</a>
					</div>
				</div>
			<?php else : ?>
				<a href="/login.php">LogIn</a>
				<a href="/register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>