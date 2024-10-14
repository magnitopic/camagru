<?php
session_start();
require_once 'controllers/LikeController.php';

header('Content-Type: application/json');

// check if the user is logged in
if (!isset($_SESSION['user_id'])) {
	http_response_code(401);
	echo json_encode(['error' => 'Unauthorized']);
	exit();
}

$userId = $_GET['userId'];
$postId = $_GET['postId'];

if (empty($userId) || empty($postId)) {
	http_response_code(401);
	echo json_encode(['error' => 'Unauthorized']);
	exit();
}

$likeController = new LikeController();
$like = $likeController->toggleLike($userId, $postId);

echo json_encode(['likes' => $like->likes]);
