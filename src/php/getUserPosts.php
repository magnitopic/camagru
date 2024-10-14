<?php
require_once 'controllers/PostController.php';

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

$postController = new PostController();

$posts = $postController->getUserPosts($userId);

echo json_encode($posts);
