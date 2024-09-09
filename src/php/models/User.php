<?php

class User
{
	private $conn;
	private $table = 'user';

	public function __construct($db)
	{
		$this->conn = $db;
	}

	public function getUserById($id)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function getUserByUsername($username)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE username = :username";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function createUser($username, $email, $password)
	{
		$query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)";
		$stmt = $this->conn->prepare($query);

		// Hash the password before storing it
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $hashed_password);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	public function login($username, $password)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE username = :username";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->execute();

		$user = $stmt->fetch(PDO::FETCH_OBJ);

		if ($user && password_verify($password, $user->password)) {
			return true;
		}

		return false;
	}

	public function updatePassword($id, $newPass)
	{
		$query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
		$stmt = $this->conn->prepare($query);

		// Hash the password before storing it
		$hashed_password = password_hash($newPass, PASSWORD_DEFAULT);

		$stmt->bindParam(':password', $hashed_password);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	public function updateUserData($id, $username, $email, $emailPreference)
	{
		$query = "UPDATE " . $this->table . " SET username = :username, email = :email, emailCommentPreference = :emailPreference WHERE id = :id";
		$stmt = $this->conn->prepare($query);

		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':emailPreference', $emailPreference);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}
}
