<?php

class Database
{
	private static $instance = null; // mysqli instance

	public static function getInstance()
	{
		if (self::$instance === null) {
			$host = defined('DBHOST') ? DBHOST : 'localhost';
			$user = defined('DBUSER') ? DBUSER : 'root';
			$pass = defined('DBPASSWORD') ? DBPASSWORD : '';
			$name = defined('DBNAME') ? DBNAME : 'ucschelpdesk';
			$port = defined('DBPORT') ? (int)DBPORT : 3306;

			$mysqli = @new mysqli($host, $user, $pass, $name, $port);
			if ($mysqli->connect_error) {
				die('Database connection failed: ' . $mysqli->connect_error);
			}
			// Set utf8mb4 for proper unicode support
			$mysqli->set_charset('utf8mb4');
			self::$instance = $mysqli;
		}
		return self::$instance;
	}
}

