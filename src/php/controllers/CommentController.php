<?php

require_once 'utils/database.php';
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

	public function getPostAuthorEmail($postId)
	{
		$authorEmail = $this->comment->getPostAuthorEmail($postId);
		return $authorEmail;
	}

	public function newComment($userId, $postId, $content)
	{
		// if comment is to long return error
		if (strlen($content) > 200)
			return ['status' => 'error', 'message' => 'Comment is too long (maximum 200 characters)'];

		if ($this->comment->newComment($userId, $postId, $content))
			return ['status' => 'success', 'message' => 'Comment added successfully', 'comments' => $this->getCommentsPost($postId)];

		return ['status' => 'error', 'message' => 'Failed to add comment'];
	}
}
