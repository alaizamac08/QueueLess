<?php

class User {
    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    public function findByUsername(string $username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function create(string $username, string $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password_hash, role_id)
            VALUES (?, ?, 1)
        ");

        $stmt->bind_param("ss", $username, $hash);
        return $stmt->execute();
    }
}