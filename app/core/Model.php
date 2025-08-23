<?php

trait Model
{
    use Database;

    public function __construct()
    {
        // Ensure PDO is connected
        $this->connect();
    }

    public function where($data)
    {
        try {
            $keys = array_keys($data);
            $conditions = implode(" AND ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "SELECT * FROM {$this->table} WHERE $conditions";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("WHERE query failed: " . $e->getMessage());
        }
    }

    public function first($data)
    {
        try {
            $keys = array_keys($data);
            $conditions = implode(" AND ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("FIRST query failed: " . $e->getMessage());
        }
    }

    public function insert($data)
    {
        try {
            $keys = array_keys($data);
            $columns = implode(", ", $keys);
            $placeholders = implode(", ", array_map(fn($key) => ":$key", $keys));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            die("INSERT failed: " . $e->getMessage());
        }
    }

    public function update($id, $data, $id_column = 'id')
    {
        try {
            $keys = array_keys($data);
            $set = implode(", ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "UPDATE {$this->table} SET $set WHERE $id_column = :id";
            $data['id'] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            die("UPDATE failed: " . $e->getMessage());
        }
    }

    public function delete($id, $id_column = 'id')
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE $id_column = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            die("DELETE failed: " . $e->getMessage());
        }
    }
}