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

		// Return a response
		echo json_encode(['status' => 'success', 'message' => 'Images uploaded successfully', 'postMsg' => $postMsg]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Images or post message not uploaded']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
