<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/_general.css">
	<link rel="stylesheet" href="css/gallery.css">
	<script defer src="js/gallery.js"></script>
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon" />
	<script src="https://kit.fontawesome.com/eca98d4b47.js" crossorigin="anonymous"></script>
	<title>Camagru | Gallery</title>
</head>

<body>

	<?php include 'components/header.php'; ?>

	<main>
		<div id="postContainer">
			<img src="img/rocket.png" alt="" id="postImg">
			<div class="postIconsContainer">
				<div id="like">
					<i class="fa-solid fa-heart"></i>
					<span>5</span>
				</div>
				<div id="comment">
					<i class="fa-solid fa-comment"></i>
					<span>5</span>
				</div>
			</div>
		</div>

		<div class="postInfoContainer">
			<div class="postInfo">
				<div class="mainInfo">
					<img src="img/rocket.png" alt="" id="postInfoImg">
					<div class="postLike" id="like">
						<i class="fa-solid fa-heart"></i>
						<span>5</span>
					</div>
					<div>
						<p id="postInfoTitle">PostTitle</p>
					</div>
				</div>
				<div class="commentsContainer">
					<div class="fullComment">
						<div class="commentTop">
							<a href="#">UserName</a>
							<div id="like">
								<i class="fa-solid fa-heart"></i>
								<span>5</span>
							</div>
						</div>
						<span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus ullam corporis provident, nemo quidem ipsa nam commodi placeat molestiae accusantium dolore ipsam, incidunt itaque rem. Labore nulla animi aperiam delectus?</span>
					</div>
					<div class="fullComment">
						<div class="commentTop">
							<a href="#">UserName</a>
							<div id="like">
								<i class="fa-solid fa-heart"></i>
								<span>5</span>
							</div>
						</div>
						<span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus ullam corporis provident, nemo quidem ipsa nam commodi placeat molestiae accusantium dolore ipsam, incidunt itaque rem. Labore nulla animi aperiam delectus?</span>
					</div>
				</div>
			</div>
		</div>
	</main>

	<?php include 'components/footer.html'; ?>
</body>

</html>