<?php
require_once __DIR__ . '/../core/database.php';

class UserController {

    private mysqli $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getUser(int $id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserLogs(int $user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT action, created_at
            FROM activity_logging
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateUser(int $id, string $username, ?string $password)
    {
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("
                UPDATE users
                SET username=?, password=?
                WHERE user_id=?
            ");

            $stmt->bind_param("ssi", $username, $password, $id);
        } else {

            $stmt = $this->conn->prepare("
                UPDATE users
                SET username=?
                WHERE user_id=?
            ");

            $stmt->bind_param("si", $username, $id);
        }

        return $stmt->execute();
    }
}