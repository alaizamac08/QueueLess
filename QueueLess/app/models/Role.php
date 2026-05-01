<?php

class Role {
    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    public function getRoles(int $role_id) {
        $stmt = $this->conn->prepare("SELECT role_name FROM roles WHERE role_id=?");
        $stmt->bind_param("i", $role_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['role_name'];
        }

        return $roles;
    }
}