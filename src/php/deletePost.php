<?php
session_start();
include_once 'controllers/PostController.php';
$postController = new PostController();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

	// check if the user is logged in
	if (!isset($_SESSION['user_id'])) {
		echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
		exit;
	}

	// check if the logged in user is the owner of the post
	$post = $postController->getPostById($_GET['id']);

	if ($post->posterId != $_SESSION['user_id']) {
		echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
		exit;
	}

	// delete the post
	$postController->deletePost($_GET['id']);
	echo json_encode(['status' => 'success', 'message' => 'Post deleted']);
} else {
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
