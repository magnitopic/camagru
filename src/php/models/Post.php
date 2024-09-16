<?php
class Post
{
	private $conn;
	private $table = 'post';

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function getPostById($id)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function createNewPost($userId, $title)
	{
		$query = "INSERT INTO " . $this->table . " (posterId, imagePath, title, date ) VALUES (:userId, :imagePath, :title, :date)";
		$stmt = $this->conn->prepare($query);

		$date = date('Y-m-d H:i:s');
		$lastPostId = $this->getIdLastPost();
		$imagePath = 'uploads/' . ($lastPostId + 1) . '.png';

		$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
		$stmt->bindParam(':imagePath', $imagePath);
		$stmt->bindParam(':title', $title);
		$stmt->bindParam(':date', $date);

		if ($stmt->execute()) {
			return $imagePath;
		}

		return false;
	}

	public function getPosts($limit, $offset)
	{
		$query = "SELECT * FROM " . $this->table . " LIMIT :limit OFFSET :offset";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}

	public function getIdLastPost()
	{
		$query = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		if ($stmt->rowCount() == 0)
			return -1;

		$result = $stmt->fetch(PDO::FETCH_OBJ);
		return $result->id;
	}
}
