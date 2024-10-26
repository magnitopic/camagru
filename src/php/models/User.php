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

	public function getUserByEmail($email)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE email = :email";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function getUserEmailCommentPreference($id)
	{
		$query = "SELECT emailCommentPreference FROM " . $this->table . " WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_OBJ);
		return $result ? (bool) $result->emailCommentPreference : null;
	}

	public function createUser($username, $email, $password, $token)
	{
		$query = "INSERT INTO " . $this->table . " (username, email, password, confirmationToken, emailConfirmed) 
				  VALUES (:username, :email, :password, :token, 0)";
		$stmt = $this->conn->prepare($query);

		// Hash the password before storing it
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $hashed_password);
		$stmt->bindParam(':token', $token);

		if ($stmt->execute()) {
			return true;
		}
		return false;
	}

	public function getUserByToken($token)
	{
		$query = "SELECT * FROM " . $this->table . " WHERE confirmationToken = :token";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':token', $token);
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function confirmEmail($id)
	{
		$query = "UPDATE " . $this->table . " SET emailConfirmed = 1, confirmationToken = NULL WHERE id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);

		if ($stmt->execute()) {
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
