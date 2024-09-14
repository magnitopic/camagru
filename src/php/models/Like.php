<?php

class Like
{
	private $conn;
	private $table = 'like';

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function getNumberLikes($postId)
	{
		$query = "SELECT COUNT(likeId) FROM " . $this->table . " WHERE post_id = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getPostLikedByUser($userId, $postId)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id AND post_id = :post_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function likePost($userId, $postId)
	{
		$query = "INSERT INTO " . $this->table . " (user_id, post_id) VALUES (:user_id, :post_id)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->rowCount();
	}
}
