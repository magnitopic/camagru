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
		// if comment is to long return error
		if (strlen($content) > 200)
			return json_encode(['status' => 'error', 'message' => 'Comment is too long (maximum 200 characters)']);

		if ($this->comment->newComment($userId, $postId, $content))
			return json_encode(['status' => 'success', 'message' => 'Comment added successfully', 'comments' => $this->getCommentsPost($postId)]);

		return json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
	}
}
