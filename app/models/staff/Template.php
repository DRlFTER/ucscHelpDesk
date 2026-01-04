<?php


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
    $sql = "INSERT INTO templates (name, category, fields, letter_required, created_by, division) VALUES (?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $err = $conn->error;
        $conn->close();
        throw new Exception('Prepare failed: ' . $err);
    }
    // Bind: name(s), category(s), fields(s), letter_required(i), created_by(i), division(i)
    $stmt->bind_param("sssiii", $data['name'], $data['category'], $fields_json, $data['letter_required'], $data['created_by'], $data['division']);
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
    public function getStaffDivisions(int $staff_id): array
{
    $db = Database::getInstance();
    $sql = "SELECT d.did, d.name 
            FROM division d
            JOIN staff_division sd ON d.did = sd.did  # Fixed: 'sd.did' instead of 'sd.d_id'
            WHERE sd.u_id = ?
            ORDER BY d.name";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $divisions = [];
    while ($row = $result->fetch_assoc()) {
        $divisions[] = $row;
    }
    $stmt->close();
    return $divisions;
}

    public function getById($template_id): ?array
    {
        $conn = self::getConnection();
        $sql = "SELECT * FROM templates WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param("i", $template_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $template = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $template ?: null;
    }

     public function delete($template_id): bool
    {
        $conn = self :: getConnection();
        $sql = "DELETE FROM templates WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $err = $conn->error;
            $conn->close();
            throw new Exception('Prepare failed: ' . $err);
        }
        $stmt->bind_param("i", $template_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
}
?>