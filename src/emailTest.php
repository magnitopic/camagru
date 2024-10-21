<?php
require_once 'php/controllers/EmailController.php';

$emailController = new EmailController();
$result = $emailController->sendCommentNotification('magnitrash@gmail.com', 'This is a test comment');

if ($result) {
	echo "Test email sent successfully!";
} else {
	echo "Failed to send test email. Check the error logs for more information.";
}
