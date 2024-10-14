<?php
session_start();

require_once 'controllers/PostController.php';
require_once 'parseData.php';

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

	// Check if the required fields are set
	$postMsg = parseData($_POST['postMsg']);

	if (!empty($_FILES['backgroundImage']['name']) && !empty($postMsg)) {
		// Receive the images
		$result = recvImages();
		if ($result['status'] === 'error') {
			echo json_encode($result);
			exit;
		}


		// Generate the post image
		$postImageResult = generatePostImage($result['selectedImgPaths']);
		if ($postImageResult['status'] === 'error') {
			echo json_encode($postImageResult);
			exit;
		}

		// Save the post to the database
		$newFileName = savePost($_POST['user_id'], $postMsg);
		if ($newFileName['status'] === 'error') {
			echo json_encode($newFileName);
			exit;
		}

		// save image with post id
		if (!rename($postImageResult['fileName'], $newFileName['fileName'])) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to save post image']);
			exit;
		}

		// Return a response
		echo json_encode(['status' => 'success', 'message' => 'Images uploaded and processed successfully']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Background image or post message not uploaded']);
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

	// Handle background image
	if (!move_uploaded_file($backgroundImage['tmp_name'], $backgroundImagePath))
		return ['status' => 'error', 'message' => 'Failed to move background image'];

	// Handle sticker images
	for ($i = 0; $i < 5; $i++) {
		$key = 'selectedImg' . $i;
		if (isset($_FILES[$key]) && $_FILES[$key]['error'] == UPLOAD_ERR_OK) {
			$selectedImg = $_FILES[$key];
			$selectedImgPath = $uploadsDir . '/' . basename($selectedImg['name']);

			if (move_uploaded_file($selectedImg['tmp_name'], $selectedImgPath)) {
				$selectedImgPaths[] = $selectedImgPath;
			} else {
				// If a file fails to move, log it but continue processing
				error_log("Failed to move uploaded file: $key");
			}
		}
	}

	return [
		'status' => 'success',
		'backgroundImagePath' => $backgroundImagePath,
		'selectedImgPaths' => $selectedImgPaths
	];
}

function generatePostImage($selectedImgPaths)
{
	$backgroundImagePath = 'uploads/backgroundImage.png';

	// Load the background image
	$backgroundImage = imagecreatefrompng($backgroundImagePath);
	if (!$backgroundImage)
		return ['status' => 'error', 'message' => 'Failed to load background image'];

	// Get dimensions of the background image
	$backgroundWidth = imagesx($backgroundImage);
	$backgroundHeight = imagesy($backgroundImage);

	// Place each selected image
	for ($i = 0; isset($_POST["posx$i"]); $i++) {
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
	$selectedImgPath = "uploads/selectedImg$index.png";
	if (!file_exists($selectedImgPath))
		return; // Skip if the image doesn't exist

	$postX = $_POST['posx' . $index] ?? 0;
	$postY = $_POST['posy' . $index] ?? 0;
	$size = $_POST['size' . $index] ?? 10;
	$rotation = ($_POST['rotation' . $index] ?? 0) * -1;

	// Load the selected image
	$selectedImg = imagecreatefrompng($selectedImgPath);
	if (!$selectedImg)
		return; // Skip this image if it can't be loaded

	// Calculate the actual pixel values based on percentages
	$posX = ($postX / 100) * $backgroundWidth;
	$posY = ($postY / 100) * $backgroundHeight;
	$width = ($size / 100) * $backgroundWidth;

	// Calculate height based on the original aspect ratio
	$originalWidth = imagesx($selectedImg);
	$originalHeight = imagesy($selectedImg);
	$aspectRatio = $originalWidth / $originalHeight;
	$height = $width / $aspectRatio;

	// Resize the selected image
	$resizedImg = imagecreatetruecolor($width, $height);
	imagealphablending($resizedImg, false);
	imagesavealpha($resizedImg, true);
	imagecopyresampled($resizedImg, $selectedImg, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

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
