<?php
require_once 'controllers/CommentController.php';
session_start();

$userId = $_POST['userId'];
$postId = $_POST['postId'];
$comment = $_POST['comment'];

if (empty($userId) || empty($postId) || empty($comment)) {
	http_response_code(401);
	echo json_encode(['error' => 'Unauthorized']);
	exit();
}

$commentController = new CommentController();
$response = $commentController->newComment($userId, $postId, $comment);

header('Content-Type: application/json');
echo $response;