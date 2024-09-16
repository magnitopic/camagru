<?php
require_once 'controllers/LikeController.php';

$userId = $_GET['userId'];
$postId = $_GET['postId'];
$likeController = new LikeController();
$like = $likeController->toggleLike($userId, $postId);

header('Content-Type: application/json');
echo json_encode($like);