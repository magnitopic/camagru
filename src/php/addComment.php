<?php
session_start();

require_once 'controllers/CommentController.php';
require_once 'controllers/EmailController.php';
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
$emailController = new EmailController('noreply@yourwebsite.com', 'Your Website Name');

$response = $commentController->newComment($userId, $postId, $comment);
/* if ($response['status'] === 'error') {
	echo $response;
	exit();
} */

$authorEmail = $commentController->getPostAuthorEmail($postId);
$yes = $emailController->sendCommentNotification($authorEmail, $comment);
return $yes;
/* echo $response; */
