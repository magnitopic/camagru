<?php

require_once 'php/database.php';
require_once 'php/models/User.php';
require_once 'php/regexValidations.php';

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
		$errors = [];

		// Checks before registering
		if (empty($username) || empty($email) || empty($password))
			$errors[] = "Empty fields.";
		else if ($this->user->getUserByUsername($username))
			$errors[] = "Username already exists.";
		else if ($this->user->getUserByEmail($email))
			$errors[] = "Email already exists.";


		// regex validations
		if (!preg_match($GLOBALS['usernameRegex'], $username))
			$errors[] = "Invalid username.";
		if (!preg_match($GLOBALS['emailRegex'], $email))
			$errors[] = "Invalid email.";
		if (!preg_match($GLOBALS['passRegex'], $password))
			$errors[] = "Invalid password.<br>Password must contain at least:<br>&emsp;&ensp;· One lowercase letter<br>&emsp;&ensp;· One uppercase letter<br>&emsp;&ensp;· One digit<br>&emsp;&ensp;· One special character<br>&emsp;&ensp;· Be at least 6 characters long";

		if (!empty($errors))
			return ['success' => false, 'errors' => $errors];


		if ($this->user->createUser($username, $email, $password))
			return ['success' => true];
		else
			return ['success' => false, 'errors' => ["Could not register user."]];
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
		$errors = [];

		if (empty($username) || empty($email))
			$errors[] = "Username and email cannot be empty.";
		else {
			$existingUser = $this->user->getUserByUsername($username);
			if ($existingUser && $existingUser->id != $id)
				$errors[] = "Username already exists.";

			$existingEmail = $this->user->getUserByEmail($email);
			if ($existingEmail && $existingEmail->id != $id)
				$errors[] = "Email already exists.";
		}

		if (!preg_match($GLOBALS['usernameRegex'], $username))
			$errors[] = "Invalid username format.";
		if (!preg_match($GLOBALS['emailRegex'], $email))
			$errors[] = "Invalid email format.";
		if (!empty($pass) && !preg_match($GLOBALS['passRegex'], $pass))
		$errors[] = "Invalid password.<br>Password must contain at least:<br>&emsp;&ensp;· One lowercase letter<br>&emsp;&ensp;· One uppercase letter<br>&emsp;&ensp;· One digit<br>&emsp;&ensp;· One special character<br>&emsp;&ensp;· Be at least 6 characters long";

		if (!empty($errors))
			return ['success' => false, 'errors' => $errors];

		$status = true;

		// Update password only if it's not empty
		if (!empty($pass))
			$status = $status && $this->user->updatePassword($id, $pass);

		$emailPreference = $emailPreference === 'on' ? 1 : 0;

		$status = $status && $this->user->updateUserData($id, $username, $email, $emailPreference);

		if ($status)
			return ['success' => true];
		else
			return ['success' => false, 'errors' => ["Could not update user data."]];
	}
}
