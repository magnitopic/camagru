<?php

require_once 'database.php';
require_once 'models/Like.php';

class LikeController
{
	private $like;

	public function __construct()
	{
		$database = new Database();
		$db = $database->connect();
		$this->like = new Like($db);
	}

	public function toggleLike($userId, $postId)
	{
		$like = $this->like->getPostLikedByUser($userId, $postId);

		if ($like) {
			$this->like->unlikePost($userId, $postId);
			return false;
		} else {
			$this->like->likePost($userId, $postId);
			return true;
		}
	}
}
