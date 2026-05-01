<?php
    class RolePermission{
        private mysqli $conn;
        private string $table_name = "role_permissions";

        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function assignPermissionToRole(int $role_id, int $permission_id){
            $sql = "INSERT INTO " . $this->table_name . " (role_id, permission_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $role_id, $permission_id);
            return $stmt->execute();
        }
    }
?>