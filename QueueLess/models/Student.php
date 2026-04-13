<?php
class Student {
    private $conn;
    private $table_name = "students";

    public function __construct($db){
        $this->conn = $db;
    }

    public function createStudent($data){
        $sql = "INSERT INTO " . $this->table_name . " (lrn, 
        first_name, 
        middle_name, 
        last_name, 
        suffix, 
        sex, 
        birth_date, 
        age, 
        place_of_birth, 
        nationality) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("ssssssisss", 
            $data['lrn'], 
            $data['first_name'], 
            $data['middle_name'], 
            $data['last_name'], 
            $data['suffix'], 
            $data['sex'], 
            $data['birth_date'], 
            $data['age'], 
            $data['place_of_birth'], 
            $data['nationality']);

        return $stmt->execute();
    }

    public function getStudentById($student_id){
        return $this->conn->query("SELECT * FROM " . $this->table_name . " WHERE student_id = " . $student_id)->fetch_assoc();
    }
}

?>