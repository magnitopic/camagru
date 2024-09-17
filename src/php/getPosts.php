<?php
require_once 'controllers/PostController.php';
require_once 'controllers/LikeController.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$postsPerPage = 5;
$offset = ($page - 1) * $postsPerPage;

$postController = new PostController();
$likeController = new LikeController();
$posts = $postController->getPosts($postsPerPage, $offset);

foreach ($posts as $post)
	$post->liked = $likeController->getPostLikedByUser($post->id, $userId);

header('Content-Type: application/json');
echo json_encode($posts);
