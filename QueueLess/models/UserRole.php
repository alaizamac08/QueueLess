<?php 
    class UserRole{
    private $conn;
    private $table_name = "user_roles";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getUserRole($user_id){
        $stmt = $this->conn->prepare(
            "SELECT r.role_name 
            FROM roles r
            INNER JOIN user_roles ur 
            ON r.role_id = ur.role_id
            WHERE ur.user_id = ?"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $roles = [];

        while ($row = $result->fetch_assoc()){
            $roles[] = $row['role_name'];
        }

        return $roles;
    }

    public function assignRoleToUser($user_id, $role_id){
        $sql = "INSERT INTO " . $this->table_name . " (user_id, role_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $role_id);
        return $stmt->execute();
    }
    }
?>