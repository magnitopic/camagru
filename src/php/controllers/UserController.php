<?php

require_once 'php/database.php';
require_once 'php/models/User.php';

class UserController
{
	private $user;

	public function __construct()
	{
		$database = new Database();
		$db = $database->connect();
		$this->user = new User($db);
	}

	public function register($username, $email, $password)
	{
		if ($this->user->createUser($username, $email, $password)) {
			header("Location: /login.php");
			exit();
		} else {
			echo "Error: Could not register user.";
		}
	}

	public function login($username, $password)
	{
		return $this->user->login($username, $password);
	}

	public function getUserById($userId)
	{
		return $this->user->getUserById(($userId));
	}

	public function getUserByUsername($username)
	{
		return $this->user->getUserByUsername($username);
	}

	public function updateUserData($id, $username, $email, $pass, $emailPreference)
	{
		$status = true;

		// unless the password is set, we don't want to update it
		if (!empty($pass))
			$status = $status && $this->user->updatePassword($id, $pass);

		$emailPreference = $emailPreference === 'on' ? 1 : 0;

		if ($status && isset($username) && isset($email) && isset($emailPreference))
			$status = $status && $this->user->updateUserData($id, $username, $email, $emailPreference);

		return $status;
	}
}
