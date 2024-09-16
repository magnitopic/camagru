<?php

class Like
{
	private $conn;
	private $table = 'likes';

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function getNumberLikes($postId)
	{
		$query = "SELECT COUNT(likeId) FROM " . $this->table . " WHERE postId = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getPostLikedByUser($userId, $postId)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE userId = :user_id AND postId = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function likePost($userId, $postId)
	{
		echo $userId;
		echo $postId;
		$query = "INSERT INTO " . $this->table . " (userId, postId) VALUES (:user_id, :post_id)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->rowCount();
	}

	public function unlikePost($userId, $postId)
	{
		$query = "DELETE FROM " . $this->table . " WHERE userId = :user_id AND postId = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->rowCount();
	}
}
