<?php
class Requirement{
    private mysqli $conn;
    private string $table_name = "requirements";

    public function __construct(mysqli $db){
        $this->conn = $db;
    }

    public function addRequirement(string $name){
        $sql = "INSERT INTO " . $this->table_name . " (requirement_name) VALUES (?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public function getAllRequirements(){
        $sql = "SELECT * FROM " . $this->table_name;
        return $this->conn->query($sql);
    }
}


?>