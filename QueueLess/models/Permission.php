<?php 
    class Permission{
    private $conn;
    private $table_name = "permissions";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getPermissionByName($permission_name){
        $sql = "SELECT * FROM " . $this->table_name . " WHERE permission_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $permission_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();

    }
    }
?>