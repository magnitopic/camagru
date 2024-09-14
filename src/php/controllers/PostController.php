<?php

require_once 'database.php';
require_once 'models/Post.php';

class PostController
{
	private $post;

	public function __construct()
	{
		$database = new Database();
		$db = $database->connect();
		$this->post = new Post($db);
	}

	public function getPostById($postId)
	{
		return $this->post->getPostById($postId);
	}

	public function createNewPost($userId, $imagePath, $title)
	{
		return $this->post->createNewPost($userId, $imagePath, $title);
	}

	public function getPosts()
	{
		return $this->post->getPosts();
	}

	public function getIdLastPost()
	{
		return $this->post->getIdLastPost();
	}
}