<?php
require_once 'controllers/PostController.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$postsPerPage = 5;
$offset = ($page - 1) * $postsPerPage;

$postController = new PostController();
$posts = $postController->getPosts($postsPerPage, $offset);

header('Content-Type: application/json');
echo json_encode($posts);
