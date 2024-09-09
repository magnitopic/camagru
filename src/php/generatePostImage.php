<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['backgroundImage']) && isset($_POST['selectedImg']) && isset($_POST['postMsg'])) {
        $backgroundImage = $_POST['backgroundImage'];
        $selectedImg = $_POST['selectedImg'];
        $postMsg = $_POST['postMsg'];

        // Decode the base64-encoded images
        $backgroundImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $backgroundImage));
        $selectedImgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selectedImg));

        // Ensure the uploads directory exists
        $uploadsDir = 'uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

		file_put_contents("test.txt", $backgroundImageData. "\n". $postMsg);

        // Save the images to the server
        $backgroundImagePath = $uploadsDir . '/backgroundImage.png';
        $selectedImgPath = $uploadsDir . '/selectedImg.png';

        if (file_put_contents($backgroundImagePath, $backgroundImageData) === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save background image']);
            exit;
        }

        if (file_put_contents($selectedImgPath, $selectedImgData) === false) {
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
?>