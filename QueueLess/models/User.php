<?php
class User{
    private $conn;
    private $table_name = "users";

    public function __construct($db){
        $this->conn = $db;
    }

    public function getDb(){
        return $this->conn;
    }

    public function createUser($username, $password, $is_active, $created_at){
        $sql = "INSERT INTO " . $this->table_name . " (
        username, 
        password, 
        is_active, 
        created_at) 
        VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssis", $username, 
        $password, 
        $is_active, 
        $created_at);

        return $stmt->execute();
    }

    public function findByUsername($username){
        $sql = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getAll(){
        return $this->conn->query("SELECT * FROM " . $this->table_name);
    }
}

?>