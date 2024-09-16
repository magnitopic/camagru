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
	<script>
		const user_id = "<?php echo $_SESSION['user_id']; ?>";
	</script>
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
					<span id="postLikes">5</span>
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
					<div class="postLike" id="likePostButton">
						<i class="fa-solid fa-heart"></i>
						<span id="postInfoLikes">5</span>
					</div>
					<div>
						<span id="postInfoTitle">PostTitle</span>
						<span>-</span>
						<span id="postInfoAuthor" class="usernameSpan">PostAuthor</span>
					</div>
				</div>
				<div class="commentsContainer">
					<div class="fullComment">
						<div class="commentTop">
							<span class="usernameSpan">UserName</span>
							<div id="like">
								<i class="fa-solid fa-heart"></i>
								<span>5</span>
							</div>
						</div>
						<span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus ullam corporis provident, nemo quidem ipsa nam commodi placeat molestiae accusantium dolore ipsam, incidunt itaque rem. Labore nulla animi aperiam delectus?</span>
					</div>
					<div class="fullComment">
						<div class="commentTop">
							<span class="usernameSpan">UserName</span>
							<div>
								<i class="fa-solid fa-heart"></i>
								<span>5</span>
							</div>
						</div>
						<span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus ullam corporis provident, nemo quidem ipsa nam commodi placeat molestiae accusantium dolore ipsam, incidunt itaque rem. Labore nulla animi aperiam delectus?</span>
					</div>
				</div>
			</div>
		</div>
		<div id="galleryContainer"></div>
	</main>

	<?php include 'components/footer.html'; ?>
</body>

</html>