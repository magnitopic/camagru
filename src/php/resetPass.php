<?php
session_start();

require_once 'utils/parseData.php';
require_once 'controllers/EmailController.php';
require_once 'controllers/UserController.php';

$response = ['success' => false, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = parseData($_POST['email']);

	// Input validation
	if (empty($email)) {
		$response['errors'][] = "Email is required.";
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response['errors'][] = "Invalid email format.";
	} else {
		$userController = new UserController();
		$user = $userController->getUserByEmail($email);

		if (!$user) {
			$response['errors'][] = "No account found with this email address.";
		} else {
			$emailController = new EmailController();

			if ($emailController->sendPasswordReset($email)) {
				$response['success'] = true;
				$response['message'] = "Password reset email sent successfully.";
			} else {
				$response['errors'][] = "Failed to send password reset email.";
			}
		}
	}

	header('Content-Type: application/json');
	echo json_encode($response);
	exit;
}

// If accessed directly without POST, redirect to login page
header("Location: /404.php");
die();
