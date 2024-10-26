<?php
/* user regex */

// username
$usernameRegex = "/^[a-zA-Z0-9]{3,20}$/";

// email
$emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

// password
// complex password
// at least one lowercase letter
// at least one uppercase letter
// at least one digit
// at least one special character
// at least 6 characters long
$passRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/";
