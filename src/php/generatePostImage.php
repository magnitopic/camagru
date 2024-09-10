<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['backgroundImage']) && isset($_FILES['selectedImg']) && isset($_POST['postMsg'])) {
		$backgroundImage = $_FILES['backgroundImage'];
		$selectedImg = $_FILES['selectedImg'];
		$postMsg = $_POST['postMsg'];

		// Ensure the uploads directory exists
		$uploadsDir = 'uploads';
		if (!is_dir($uploadsDir)) {
			mkdir($uploadsDir, 0755, true);
		}

		// Save the images to the server
		$backgroundImagePath = $uploadsDir . '/backgroundImage.png';
		$selectedImgPath = $uploadsDir . '/selectedImg.png';

		if (!move_uploaded_file($backgroundImage['tmp_name'], $backgroundImagePath)) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to save background image']);
			exit;
		}

		if (!move_uploaded_file($selectedImg['tmp_name'], $selectedImgPath)) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to save selected image']);
			exit;
		}

		// Generate the post image
		$result = generatePostImage();
		if ($result['status'] === 'error') {
			echo json_encode($result);
			exit;
		}

		// Return a response
		echo json_encode(['status' => 'success', 'message' => 'Images uploaded and processed successfully', 'postMsg' => $postMsg]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Images or post message not uploaded']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

function generatePostImage()
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
