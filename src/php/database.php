<?php

class Database
{
	private $host;
	private $db_name;
	private $username;
	private $password;
	private $conn;

	public function __construct()
	{
		$this->host = $_ENV['MARIADB_HOST'];
		$this->db_name = $_ENV['MARIADB_DATABASE'];
		$this->username = $_ENV['MARIADB_USER'];
		$this->password = $_ENV['MARIADB_PASSWORD'];
	}

	public function connect()
	{
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
