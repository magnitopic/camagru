<?php
session_start();
include_once 'controllers/PostController.php';
$postController = new PostController();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	// check if the user is logged in
	if (!isset($_SESSION['user_id'])) {
		header('Content-Type: application/json');
		echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
		exit;
	}

	// check if the logged in user is the owner of the post
	$post = $postController->getPostById($_GET['id']);
	if ($post['user_id'] != $_SESSION['user_id']) {
		header('Content-Type: application/json');
		echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
		exit;
	}

	// delete the post
	$postController->deletePost($_GET['id']);
	echo json_encode(['message' => 'Post deleted']);
} else {
	header('Content-Type: application/json');
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
