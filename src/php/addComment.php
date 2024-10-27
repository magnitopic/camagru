<?php
session_start();

require_once 'controllers/EmailController.php';
require_once 'controllers/CommentController.php';
require_once 'controllers/UserController.php';
require_once 'utils/parseData.php';

$emailController = new EmailController();
$commentController = new CommentController();
$userController = new UserController();


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
	exit();
}

$userId = $_POST['userId'];
$postId = $_POST['postId'];
$comment = parseData($_POST['comment']);


if (empty($userId)) {
	echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
	exit;
}

if (empty($postId) || empty($comment)) {
	http_response_code(400);
	echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
	exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
	http_response_code(401);
	echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
	exit();
}

# save new comment
$response = $commentController->newComment($userId, $postId, $comment);



# send email notification if the user has the preference enabled and there where no errors saving the comment
if ($response['status'] != 'error' && $userController->getUserEmailCommentPreference($userId)) {
	$authorEmail = $commentController->getPostAuthorEmail($postId);
	$emailRes = $emailController->sendCommentNotification($authorEmail, $comment);
}

echo json_encode($response);
