<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: /login.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Camagru | Camera</title>
	<link rel="stylesheet" href="css/_general.css">
	<link rel="stylesheet" href="css/camera.css">
	<script src="js/camera.js" defer></script> <!-- !-> Remove comment -->
	<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>

	<?php include 'components/header.php'; ?>

	<main>
		<div class="postImages">
			<div class="canvasContainer">
				<video id="video" class="stream_player" disablePictureInPicture="true">Video not supported in this browser</video>
				<canvas class="final_image_canvas"></canvas>
			</div>
			<div class="side_panel">
				<button id="startButton">Take picture</button>
				<button id="retakeButton">Retake picture</button>
				<input type="file" name="imgFileInput" id="imgFileInput">
				<button id="imgFile">
					<svg class="svg-icon" width="24" viewBox="0 0 24 24" height="24" fill="none">
						<g stroke-width="2" stroke-linecap="round" stroke="#056dfa" fill-rule="evenodd" clip-rule="evenodd">
							<path d="m3 7h17c.5523 0 1 .44772 1 1v11c0 .5523-.4477 1-1 1h-16c-.55228 0-1-.4477-1-1z"></path>
							<path d="m3 4.5c0-.27614.22386-.5.5-.5h6.29289c.13261 0 .25981.05268.35351.14645l2.8536 2.85355h-10z"></path>
						</g>
					</svg>
					<span id="fileNameDisplay">Upload picture</span>
				</button>
				<div class="imgContainer">
					<p class="text-font font-semibold">Select an overlay image</p>
					<img class="defaultImgs" src="img/rocket.png" alt="rocket" title="Rocket">
					<img class="defaultImgs" src="img/flower.png" alt="flower" title="Flower">
					<img class="defaultImgs" src="img/42.png" alt="42logo" title="42logo">
					<img class="defaultImgs" src="img/c.png" alt="c" title="c">
					<img class="defaultImgs" src="img/github.png" alt="github" title="github">
				</div>
				<div class="sliderContainer">
					<label for="size">Change image size </label>
					<input type="range" min="30" max="1000" value="100" class="slider" id="size" name="rotation">
				</div>
				<div class="sliderContainer">
					<label for="rotation">Change image rotationw</label>
					<input type="range" min="0" max="360" value="1" class="slider" id="rotation" name="rotation">
				</div>
			</div>
		</div>
		<div class="postInfo">
			<input id="postMsg" type="text" placeholder="Post message..." maxlength="20">
			<button id="saveButton" disabled>Publish picture</button>
		</div>
	</main>

	<?php include 'components/footer.html'; ?>
</body>

</html>