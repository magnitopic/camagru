<?php
session_start();

require_once 'controllers/PostController.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
	$postController = new PostController();
	// echo "PostController initialized successfully."; // Comment out or remove this line
} catch (Exception $e) {
	echo json_encode(['status' => 'error', 'message' => "Error initializing PostController: " . $e->getMessage()]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['backgroundImage']) && isset($_FILES['selectedImg']) && isset($_POST['postMsg'])) {

		// Receive the images
		$result = recvImages();
		if ($result['status'] === 'error') {
			echo json_encode($result);
			exit;
		}

		$postX = $_POST['posx'];
		$postY = $_POST['posy'];
		$size = $_POST['size'];
		$rotation = $_POST['rotation'];

		// Generate the post image
		$result = generatePostImage(postx, posty, size, rotation);
		if ($result['status'] === 'error') {
			echo json_encode($result);
			exit;
		}

		// Save the post to the database
		$newFileName = savePost($_POST['user_id'], $_POST['postMsg']);
		if ($newFileName['status'] === 'error') {
			echo json_encode($newFileName);
			exit;
		}

		// save image with post id
		if (!rename('uploads/postImage.png', $newFileName['fileName'])) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to save post image']);
			exit;
		}

		// Return a response
		echo json_encode(['status' => 'success', 'message' => 'Images uploaded and processed successfully', 'postMsg' => $_POST['postMsg']]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Images or post message not uploaded']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

function recvImages()
{
	$backgroundImage = $_FILES['backgroundImage'];
	$selectedImg = $_FILES['selectedImg'];
	$postMsg = $_POST['postMsg'];

	// Ensure the uploads directory exists
	$uploadsDir = 'uploads';
	if (!is_dir($uploadsDir))
		mkdir($uploadsDir, 0755, true);

	// Save the images to the server
	$backgroundImagePath = $uploadsDir . '/backgroundImage.png';
	$selectedImgPath = $uploadsDir . '/selectedImg.png';

	if (!move_uploaded_file($backgroundImage['tmp_name'], $backgroundImagePath))
		return ['status' => 'error', 'message' => 'Failed to save background image'];

	if (!move_uploaded_file($selectedImg['tmp_name'], $selectedImgPath))
		return ['status' => 'error', 'message' => 'Failed to save selected image'];

	return ['status' => 'success', 'message' => 'Images uploaded successfully', 'postMsg' => $postMsg];
}

function generatePostImage($postX, $postY, $size, $rotation)
{
	$backgroundImagePath = 'uploads/backgroundImage.png';
	$selectedImgPath = 'uploads/selectedImg.png';
	$outputPath = 'uploads/postImage.png';

	$backgroundImage = imagecreatefrompng($backgroundImagePath);
	if (!$backgroundImage) {
		return ['status' => 'error', 'message' => 'Failed to create background image from file'];
	}

	$selectedImg = imagecreatefrompng($selectedImgPath);
	if (!$selectedImg) {
		imagedestroy($backgroundImage);
		return ['status' => 'error', 'message' => 'Failed to create selected image from file'];
	}

	$backgroundWidth = imagesx($backgroundImage);
	$backgroundHeight = imagesy($backgroundImage);

	$selectedImgWidth = imagesx($selectedImg);
	$selectedImgHeight = imagesy($selectedImg);

	$x = ($backgroundWidth - $selectedImgWidth) / 2;
	$y = ($backgroundHeight - $selectedImgHeight) / 2;

	// Copy the selected image onto the background image
	imagecopy($backgroundImage, $selectedImg, $x, $y, 0, 0, $selectedImgWidth, $selectedImgHeight);

	// Save the final image
	if (!imagepng($backgroundImage, $outputPath)) {
		imagedestroy($backgroundImage);
		imagedestroy($selectedImg);
		return ['status' => 'error', 'message' => 'Failed to save final image'];
	}

	imagedestroy($backgroundImage);
	imagedestroy($selectedImg);

	return ['status' => 'success', 'message' => 'Image generated successfully', 'outputPath' => $outputPath];
}

function savePost($user_id, $postMsg)
{
	$postController = new PostController();
	$result = $postController->createNewPost($user_id, $postMsg);
	if ($result === false) {
		return ['status' => 'error', 'message' => 'Failed to save post to database'];
	}
	return ['status' => 'success', 'fileName' => $result];
}
