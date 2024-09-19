<?php
require_once 'controllers/CommentController.php';
session_start();

$userId = $_POST['userId'];
$postId = $_POST['postId'];
$comment = $_POST['comment'];

$commentController = new CommentController();
$response = $commentController->newComment($userId, $postId, $comment);

header('Content-Type: application/json');
echo $response;