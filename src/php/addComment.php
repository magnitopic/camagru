<?php
session_start();

require_once 'controllers/CommentController.php';
require_once 'parseData.php';

header('Content-Type: application/json');

$userId = $_POST['userId'];
$postId = $_POST['postId'];
$comment = parseData($_POST['comment']);

if (empty($userId) || empty($postId) || empty($comment)) {
	http_response_code(400);
	echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
	exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
	http_response_code(401);
	echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
	exit();
}

$commentController = new CommentController();
$response = $commentController->newComment($userId, $postId, $comment);

echo $response;
