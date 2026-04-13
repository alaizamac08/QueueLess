<?php
class Role {
    private $conn;
    private $table_name = "roles";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRoleByName($role_name) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE role_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $role_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllRoles() {
        return $this->conn->query("SELECT * FROM " . $this->table_name);
    }
}



?>