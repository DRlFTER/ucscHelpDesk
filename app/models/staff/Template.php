<?php
// models/staff/Template.php

require_once __DIR__ . '/../../core/config.php';

class Template
{
    private static function getConnection()
    {
        $conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);
        if ($conn->connect_error) {
            throw new Exception('DB Connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    public function create($data): bool
    {
        $conn = self::getConnection();
        $fields_json = json_encode($data['fields']);
        $sql = "INSERT INTO templates (name, category, fields, process, outcome, letter_required, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param("ssssssi", $data['name'], $data['category'], $fields_json, $data['process'], $data['outcome'], $data['letter_required'], $data['created_by']);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public function getAll(): array
    {
        $conn = self::getConnection();
        $sql = "SELECT * FROM templates ORDER BY created_at DESC";
        $result = $conn->query($sql);
        if (!$result) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Query failed: ' . $err);
        }
        $templates = [];
        while ($row = $result->fetch_assoc()) {
            $templates[] = $row;
        }
        $conn->close();
        return $templates;
    }
}
?>