<?php

class Comment
{
	private $conn;
	private $table = 'comment';

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function getCommentsPost($postId)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE postId = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function newComment($commenterId, $postId, $content)
	{
		$query = "INSERT INTO " . $this->table . " (commenterId, postId, message) VALUES (:commenterId, :postId, :content)";
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(':commenterId', $commenterId, PDO::PARAM_INT);
		$stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
		$stmt->bindParam(':content', $content);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	public function deleteComment($commentId)
	{
		$query = "DELETE FROM " . $this->table . " WHERE id = :comment_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}
}
