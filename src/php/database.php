<?php

class Database {
	private $host = "mariadb";
	private $db_name = "camagru";
	private $username = "alaparic";
	private $password = "pass123";
	private $conn;

	public function connect() {
		$this->conn = null;

		try {
			$dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_PERSISTENT => true, // Persistent connection
				PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
			];

			$this->conn = new PDO($dsn, $this->username, $this->password, $options);
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}

		return $this->conn;
	}
}
