<?php
require_once 'utils/database.php';
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
		if ($like)
			$this->like->unlikePost($userId, $postId);
		else
			$this->like->likePost($userId, $postId);
		$likes = $this->like->getNumberLikes($postId);
		return $likes;
	}

	public function getPostLikedByUser($postId, $userId)
	{
		$like = $this->like->getPostLikedByUser($userId, $postId);
		return $like ? true : false;
	}
}
