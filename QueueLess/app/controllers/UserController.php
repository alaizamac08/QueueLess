<?php
require_once __DIR__ . '/../core/database.php';

class UserController {

    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    public function getUser(int $id) {
        $stmt = $this->conn->prepare("
            SELECT u.user_id, u.username, p.display_name, p.profile_picture
            FROM users u
            LEFT JOIN user_profiles p ON u.user_id = p.user_id
            WHERE u.user_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateDisplayName(int $id, string $displayName): bool
    {
        $check = $this->conn->prepare("
            SELECT user_id FROM user_profiles WHERE user_id = ?
        ");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $stmt = $this->conn->prepare("
                UPDATE user_profiles 
                SET display_name = ? 
                WHERE user_id = ?
            ");

            // IMPORTANT: correct order
            $stmt->bind_param("si", $displayName, $id);

        } else {

            $stmt = $this->conn->prepare("
                INSERT INTO user_profiles (user_id, display_name) 
                VALUES (?, ?)
            ");

            // insert order is fine
            $stmt->bind_param("is", $id, $displayName);
        }

        return $stmt->execute();
    }

    public function changePassword(int $id, string $password): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            UPDATE users 
            SET password = ? 
            WHERE user_id = ?
        ");

        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }

    public function uploadProfilePicture(int $id, array $file): bool
{
    if ($file['error'] !== 0) {
        throw new RuntimeException("Invalid file upload.");
    }

    $allowed = ['image/jpeg', 'image/png'];

    if (!in_array($file['type'], $allowed)) {
        throw new RuntimeException("Only JPG and PNG allowed.");
    }

    // ✅ REPLACE STARTS HERE
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('profile_', true) . '.' . $ext;

    // real folder (server side)
    $uploadDir = __DIR__ . "/../../public/profiles/";

    // path saved in DB
    $path = "profiles/" . $filename;

    // create folder if missing
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // move file correctly
    move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
    // ✅ REPLACE ENDS HERE

    // check if profile exists
    $check = $this->conn->prepare("
        SELECT user_id FROM user_profiles WHERE user_id = ?
    ");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $stmt = $this->conn->prepare("
            UPDATE user_profiles 
            SET profile_picture = ? 
            WHERE user_id = ?
        ");
        $stmt->bind_param("si", $path, $id);
    } else {
        $stmt = $this->conn->prepare("
            INSERT INTO user_profiles (user_id, profile_picture) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $id, $path);
    }

    return $stmt->execute();
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