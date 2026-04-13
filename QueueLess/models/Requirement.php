<?php
class Requirement{
    private $conn;
    private $table_name = "requirements";

    public function __construct($db){
        $this->conn = $db;
    }

    public function addRequirement($name){
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