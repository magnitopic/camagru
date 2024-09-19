<?php

require_once 'database.php';
require_once 'models/Comment.php';

class CommentController
{
	private $comment;

	public function __construct()
	{
		$database = new Database();
		$db = $database->connect();
		$this->comment = new Comment($db);
	}

	public function getCommentsPost($postId)
	{
		$comments = $this->comment->getCommentsPost($postId);
		return $comments;
	}

	public function getPostCommentInfo($postId)
	{
		$comments = $this->comment->getCommentsPost($postId);
		$commentsCount = count($comments);
		return ['comments' => $comments, 'commentsCount' => $commentsCount]; // Return the array directly
	}

	public function newComment($userId, $postId, $content)
	{
		if ($this->comment->newComment($userId, $postId, $content)) {
			return json_encode($this->getCommentsPost($postId));
		}

		return json_encode(['message' => 'Error adding comment']);
	}
}
