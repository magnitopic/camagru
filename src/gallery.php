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
	<script defer src="js/error.js"></script>
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
			<img src="" alt="" id="postImg">
			<div class="postIconsContainer">
				<div id="like">
					<i class="fa-solid fa-heart"></i>
					<span id="postLikes"></span>
				</div>
				<div id="comment">
					<i class="fa-solid fa-comment"></i>
					<span id="postComments"></span>
				</div>
			</div>
		</div>

		<div class="postInfoContainer">
			<div class="postInfo">
				<div class="mainInfo">
					<img src="" alt="" id="postInfoImg">
					<div id="postInfo">
						<div class="postLike" id="likePostButton">
							<i class="fa-solid fa-heart"></i>
							<span id="postInfoLikes">5</span>
						</div>
						<div>
							<span id="postInfoTitle">PostTitle</span>
							<span>-</span>
							<span id="postInfoAuthor" class="usernameSpan">PostAuthor</span>
						</div>
						<div><span id="postDate"></span></div>
					</div>
				</div>
				<form id="newCommentForm">
					<input type="text" placeholder="New comment..." minlength="1" id="newComment">
					<button id="newCommentButton"><i class="fa-solid fa-paper-plane"></i></button>
				</form>
				<div id="commentsContainer">
				</div>
				<div id="fullComment">
					<div class="commentTop">
						<span class="commentAuthor usernameSpan"></span>
					</div>
					<span id="commentMsg"></span>
				</div>
			</div>
		</div>
		<div id="galleryContainer"></div>
	</main>

	<?php include 'components/footer.html'; ?>
</body>

</html>