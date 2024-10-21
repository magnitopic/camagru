<?php

class EmailController
{
	private $fromEmail;
	private $fromName;

	public function __construct()
	{
		$this->fromEmail = "magnitopic@gmail.com";
		$this->fromName = "magnitopic";
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
		$subject = "New Comment Notification";
		$body = "A new comment has been posted: <br><br>" . htmlspecialchars($commentContent);
		return $this->sendEmail($to, $subject, $body);
	}

	public function sendPasswordReset($to)
	{
		$newPassword = $this->generateRandomPassword();
		$subject = "Password Reset";
		$body = "Your new password is: " . $newPassword . "<br><br>Please login and change your password.";
		if ($this->sendEmail($to, $subject, $body)) {
			$this->updateUserPassword($to, $newPassword);
			return true;
		}
		return false;
	}

	public function sendAccountConfirmation($to)
	{
		$token = $this->generateConfirmationToken();
		$subject = "Account Confirmation";
		$confirmationLink = "https://yourwebsite.com/confirm-account.php?token=" . urlencode($token);
		$body = "Please click the following link to confirm your account: <br><br><a href='" . $confirmationLink . "'>Confirm Account</a>";
		if ($this->sendEmail($to, $subject, $body)) {
			$this->storeConfirmationToken($to, $token);
			return true;
		}
		return false;
	}

	private function generateRandomPassword($length = 26)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$&?!';
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $password;
	}

	private function generateConfirmationToken()
	{
		return bin2hex(random_bytes(32));
	}

	private function updateUserPassword($email, $newPassword)
	{
		// Implement the logic to update the user's password in your database
		// This is just a placeholder function
	}

	private function storeConfirmationToken($email, $token)
	{
		// Implement the logic to store the confirmation token in your database
		// This is just a placeholder function
	}
}

// Usage example
// $emailController = new EmailController('noreply@yourwebsite.com', 'Your Website Name');
// $emailController->sendCommentNotification('user@example.com', 'Great post!');
// $emailController->sendPasswordReset('user@example.com');
// $emailController->sendAccountConfirmation('newuser@example.com');
