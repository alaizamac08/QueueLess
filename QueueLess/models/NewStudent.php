<?php
class NewStudentDetails{
    private $conn;
    private $table_name = "new_student_details";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create(
        $new_id, 
        $enrollment_id, 
        $previous_school, 
        $kindergarten_completed){
            $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (
            new_id, 
            enrollment_id, 
            previous_school, 
            kindergarten_completed
            ) VALUES (?, ?, ?, ?)");

            $stmt->bind_param("iiss", $new_id, $enrollment_id, $previous_school, $kindergarten_completed);
            
            return $stmt->execute();
        }


        public function getByEnrollmentId($enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    
}
?>