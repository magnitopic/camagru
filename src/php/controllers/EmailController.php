<?php

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . 'controllers/UserController.php';

class EmailController
{
	private $fromEmail;
	private $fromName;
	private $userController;

	public function __construct()
	{
		$this->fromEmail = "alaparic@student.42madrid.com";
		$this->fromName = "alaparic";
		$this->userController = new UserController();
	}

	private function sendEmail($to, $subject, $body, $isHtml = true)
	{
		$headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
		$headers .= "Reply-To: {$this->fromEmail}\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

		if ($isHtml) {
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		}

		$result = mail($to, $subject, $body, $headers);
		if (!$result) {
			error_log("Failed to send email. Error: " . error_get_last()['message']);
		}
		return $result;
	}

	public function sendCommentNotification($to, $commentContent)
	{
		$subject = "camagru-alaparic: You have a new comment";
		$body = "You have a new comment in you post: <br><br><b>" . htmlspecialchars($commentContent) . "</b><br><br>Go check it out!";
		return $this->sendEmail($to, $subject, $body);
	}

	public function sendPasswordReset($to)
	{
		$newPassword = $this->generateRandomPassword();
		$subject = "camagru-alaparic: Password Reset";
		$body = "We have received a password reset request.<br>Your new password is:<br><b>" . $newPassword .
			"</b><br><br>Please take the following steps to access your account:<br>1. Log in to your account with your new password<br>2. Go to your account settings<br>3. Enter a new secure password<br>4. Save your changes";
		if ($this->sendEmail($to, $subject, $body)) {
			$this->userController->updatePassword($to, $newPassword);
			return true;
		}
		return false;
	}

	public function sendAccountConfirmation($to)
	{
		$token = $this->userController->getUserConfirmationToken($to);
		$subject = "camagru-alaparic: Account Confirmation";
		$confirmationLink = "http://localhost:8080/confirmAccount.php?token=" . urlencode($token);
		$body = "Please click the following link to confirm your account: <br><br><a href='" . $confirmationLink . "'>Confirm Account</a>";
		return $this->sendEmail($to, $subject, $body);
	}

	private function generateRandomPassword()
	{
		$length = 26;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$&?!';
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $password;
	}

	private function updateUserPassword($email, $newPassword)
	{
		$userController = new UserController();
	}
}
