<?php

trait Database
{
    private $host = DBHOST;
    private $port = DBPORT;
    private $dbname = DBNAME;
    private $user = DBNAME;
    private $password = DBPASSWORD;

    protected $pdo;

    // Initialize PDO only once
    private function connect()
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($query, $data = [])
    {
        $conn = $this->connect();
        $stmt = $conn->prepare($query);

        if ($stmt->execute($data)) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            return (!empty($result)) ? $result : false;
        }

        return false;
    }

    public function get_row($query, $data = [])
    {
        $conn = $this->connect();
        $stmt = $conn->prepare($query);

        if ($stmt->execute($data)) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ?: false;
        }

        return false;
    }
}
