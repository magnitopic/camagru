<?php

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . 'utils/database.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'utils/regexValidations.php';

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

		// Generate confirmation token
		$token = $this->generateConfirmationToken($email);

		// Create user in database
		if ($this->user->createUser($username, $email, $password, $token)) {
			// Send confirmation email
			require_once BASE_PATH . 'controllers/EmailController.php';
			$emailController = new EmailController();

			if (!$emailController->sendAccountConfirmation($email, $token)) {
				return ['success' => false, 'errors' => ["Registered successfully but failed to send confirmation email."]];
			}

			return ['success' => true];
		}

		return ['success' => false, 'errors' => ["Could not register user."]];
	}

	private function generateConfirmationToken($email)
	{
		$timestamp = time();
		$randomBytes = random_bytes(16);
		$data = $email . $timestamp . $randomBytes;

		// Create token with email, timestamp and random bytes
		return hash('sha256', $data);
	}

	public function confirmAccount($token)
	{
		$errors = [];

		if (empty($token)) {
			$errors[] = "Invalid confirmation link.";
			return ['success' => false, 'errors' => $errors];
		}

		// Get user by token
		$user = $this->user->getUserByToken($token);

		if (!$user) {
			$errors[] = "Invalid confirmation token.";
			return ['success' => false, 'errors' => $errors];
		}

		if ($user->emailConfirmed) {
			$errors[] = "Email already confirmed.";
			return ['success' => false, 'errors' => $errors];
		}

		// Confirm email
		if ($this->user->confirmEmail($user->id)) {
			return ['success' => true];
		}

		return ['success' => false, 'errors' => ["Could not confirm email."]];
	}

	public function getUserConfirmationToken($email)
	{
		$user = $this->user->getUserByEmail($email);
		return $user ? $user->confirmationToken : null;
	}

	public function login($username, $password)
	{
		$errors = [];

		// Check for empty fields
		if (empty($username) || empty($password)) {
			$errors[] = "Username and password are required.";
			return ['success' => false, 'errors' => $errors];
		}

		// Check if the user exists
		$user = $this->user->getUserByUsername($username);
		if (!$user) {
			$errors[] = "This user does not exist.";
			return ['success' => false, 'errors' => $errors];
		}

		// Verify the password
		if (!password_verify($password, $user->password)) {
			$errors[] = "Invalid username or password.";
			return ['success' => false, 'errors' => $errors];
		}

		// Check if the user's email is verified (if you have email verification)
		if (!$user->emailConfirmed) {
			$errors[] = "Please verify your email address before logging in.";
			return ['success' => false, 'errors' => $errors];
		}

		// If everything is okay, return success
		return ['success' => true, 'user' => $user];
	}

	public function getUserById($userId)
	{
		return $this->user->getUserById(($userId));
	}

	public function getUserByUsername($username)
	{
		return $this->user->getUserByUsername($username);
	}

	public function getUserByEmail($email)
	{
		return $this->user->getUserByEmail($email);
	}

	public function getUserEmailCommentPreference($userId)
	{
		return $this->user->getUserEmailCommentPreference($userId);
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

	public function updatePassword($email, $newPassword)
	{
		$id = $this->user->getUserByEmail($email)->id;
		return $this->user->updatePassword($id, $newPassword);
	}
}
