<?php
session_start();

require_once 'controllers/PostController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
	echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
	exit;
}

try {
	$postController = new PostController();
} catch (Exception $e) {
	echo json_encode(['status' => 'error', 'message' => "Error initializing PostController: " . $e->getMessage()]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Verify that the logged-in user matches the user_id in the request
	if ($_SESSION['user_id'] != $_POST['user_id']) {
		echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
		exit;
	}

	if (isset($_FILES['backgroundImage']) && isset($_POST['postMsg'])) {

		// Receive the images
		$result = recvImages();
		if ($result['status'] === 'error') {
			echo json_encode($result);
			exit;
		}

		// Generate the post image
		$result = generatePostImage();
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
		echo json_encode(['status' => 'success', 'message' => 'Images uploaded and processed successfully']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Images or post message not uploaded']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

function recvImages()
{
	$backgroundImage = $_FILES['backgroundImage'];

	// Ensure the uploads directory exists
	$uploadsDir = 'uploads';
	if (!is_dir($uploadsDir))
		mkdir($uploadsDir, 0755, true);

	// Move the uploaded files to the uploads directory
	$backgroundImagePath = $uploadsDir . '/' . basename($backgroundImage['name']);

	$selectedImgPaths = [];

	for ($i = 0; $i <= 5; $i++) {
		if (isset($_FILES['selectedImg' . $i]) && $_FILES['selectedImg' . $i]['error'] == UPLOAD_ERR_OK) {
			$selectedImg = $_FILES['selectedImg' . $i];
			$selectedImgPath = $uploadsDir . '/' . basename($selectedImg['name']);

			if (!move_uploaded_file($selectedImg['tmp_name'], $selectedImgPath)) {
				return ['status' => 'error', 'message' => 'Failed to move uploaded file: selectedImg' . $i];
			}
			$selectedImgPaths[] = $selectedImgPath;
		}
	}

	if (!move_uploaded_file($backgroundImage['tmp_name'], $backgroundImagePath))
		return ['status' => 'error', 'message' => 'Failed to move background image'];

	return ['status' => 'success', 'backgroundImagePath' => $backgroundImagePath, 'selectedImgPaths' => $selectedImgPaths];
}

function generatePostImage()
{
	$backgroundImagePath = 'uploads/backgroundImage.png';

	// Load the background image
	$backgroundImage = imagecreatefrompng($backgroundImagePath);
	if (!$backgroundImage)
		return ['status' => 'error', 'message' => 'Failed to load background image'];

	// Get dimensions of the background image
	$backgroundWidth = imagesx($backgroundImage);
	$backgroundHeight = imagesy($backgroundImage);

	for ($i = 0; $i < 5; $i++) {
		placeSelectedImage($i, $backgroundImage, $backgroundWidth, $backgroundHeight);
	}

	// Save the final image
	$outputPath = 'uploads/postImage.png';
	if (!imagepng($backgroundImage, $outputPath))
		return ['status' => 'error', 'message' => 'Failed to save post image'];

	// Clean up
	imagedestroy($backgroundImage);

	return ['status' => 'success', 'fileName' => $outputPath];
}

function placeSelectedImage($index, $backgroundImage, $backgroundWidth, $backgroundHeight)
{
	$selectedImgPath = 'uploads/selectedImg' . $index . '.png';
	$postX = $_POST['posx' . $index];
	$postY = $_POST['posy' . $index];
	$size = $_POST['size' . $index];
	$rotation = $_POST['rotation' . $index];
	$rotation = $rotation * -1;

	// Load the selected image
	$selectedImg = imagecreatefrompng($selectedImgPath);
	if (!$selectedImg)
		return ['status' => 'error', 'message' => 'Failed to load selected image'];

	// Calculate the actual pixel values based on percentages
	$posX = ($postX / 100) * $backgroundWidth;
	$posY = ($postY / 100) * $backgroundHeight;
	$width = ($size / 100) * $backgroundWidth;
	$height = ($size / 100) * $backgroundHeight;

	// Resize the selected image
	$resizedImg = imagescale($selectedImg, $width, $height);

	// Create a transparent background for the rotated image
	$transparentBg = imagecreatetruecolor($width, $height);
	imagealphablending($transparentBg, false);
	imagesavealpha($transparentBg, true);
	$transparentColor = imagecolorallocatealpha($transparentBg, 0, 0, 0, 127);
	imagefill($transparentBg, 0, 0, $transparentColor);

	// Rotate the selected image with a transparent background
	$rotatedImg = imagerotate($resizedImg, $rotation, $transparentColor);

	// Get dimensions of the rotated image
	$rotatedWidth = imagesx($rotatedImg);
	$rotatedHeight = imagesy($rotatedImg);

	// Calculate the position to center the rotated image
	$centerX = $posX - ($rotatedWidth / 2);
	$centerY = $posY - ($rotatedHeight / 2);

	imagecopy($backgroundImage, $rotatedImg, $centerX, $centerY, 0, 0, $rotatedWidth, $rotatedHeight);

	// Clean up
	imagedestroy($selectedImg);
	imagedestroy($resizedImg);
	imagedestroy($rotatedImg);
	imagedestroy($transparentBg);
}

function savePost($userId, $postMsg)
{
	$postController = new PostController();
	$result = $postController->createNewPost($userId, $postMsg);
	if ($result === false)
		return ['status' => 'error', 'message' => 'Failed to save post to database'];
	return ['status' => 'success', 'fileName' => $result];
}
