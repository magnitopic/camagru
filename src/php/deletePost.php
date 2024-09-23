<?php
include_once 'controllers/PostController.php';
$postController = new PostController();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	$postController->deletePost($_GET['id']);
	echo json_encode(['message' => 'Post deleted']);
} else {
	return http_response_code(405);
}
