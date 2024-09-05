<?php

require_once 'php/database.php';
require_once 'php/models/User.php';

class UserController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->connect();
        $this->user = new User($db);
    }

    public function register($username, $email, $password) {
        if ($this->user->createUser($username, $email, $password)) {
            // Successfully created the user
            header("Location: /login.php");
            exit();
        } else {
            // Failed to create the user
            echo "Error: Could not register user.";
        }
    }
}
