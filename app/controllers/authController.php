<?php
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../includes/db.php';
    session_start();

    if ($_GET['action'] === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
            ];

            switch ($user['role']) {
                case 'admin':
                    header("Location: ../views/admin/dashboard.php");
                    break;
                case 'staff':
                    header("Location: ../views/staff/dashboard.php");
                    break;
                case 'counselor':
                    header("Location: ../views/counselor/dashboard.php");
                    break;
                case 'student':
                    header("Location: ../views/student/dashboard.php");
                    break;
                default:
                    header("Location: ../views/auth/login.php?error=Invalid role");
            }
        } else {
            echo "Invalid username or password.";
        }
    } elseif ($_GET['action'] === 'register') {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt =$conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $hashedPassword, $role);
        $stmt->execute();

        header("Location: ../views/auth/login.php?registered=true");
    }
 }
?>