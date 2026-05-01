<?php
    class UserRole{
    private mysqli $conn;
    private string $table_name = "users";

    public function __construct(mysqli $db){
        $this->conn = $db;
    }

    public function getUserRole(){
        $stmt = $this->conn->prepare(
            "SELECT r.role_name FROM " . $this->table_name . " INNER JOIN users u ON r.role_id = u.role_id WHERE u.user_id = ?"
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

    public function assignRoleToUser(int $user_id, int $role_id){
        $sql = "INSERT INTO " . $this->table_name . " (user_id, role_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $role_id);
        return $stmt->execute();
    }
    }
?>