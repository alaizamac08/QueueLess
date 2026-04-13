<?php 
    class RolePermission{
        private $conn;
        private $table_name = "role_permissions";

        public function __construct($db){
            $this->conn = $db;
        }

        public function assignPermissionToRole($role_id, $permission_id){
            $sql = "INSERT INTO " . $this->table_name . " (role_id, permission_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $role_id, $permission_id);
            return $stmt->execute();
        }
    }
?>