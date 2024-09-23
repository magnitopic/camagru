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

	public function createNewPost($userId, $title)
	{
		return $this->post->createNewPost($userId, $title);
	}

	public function getPosts($limit, $offset)
	{
		return $this->post->getPosts($limit, $offset);
	}

	public function getUserPosts($userId)
	{
		return $this->post->getUserPosts($userId);
	}

	public function getIdLastPost()
	{
		return $this->post->getIdLastPost();
	}

	public function deletePost($postId)
	{
		return $this->post->deletePost($postId);
	}
}
