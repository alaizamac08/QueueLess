<?php 
    class StudentRequirement{
        private $conn;
        private $table_name = "student_requirements";


        public function __construct($db){
            $this->conn = $db;
        }

        public function addStudentRequirement($student_requirement_id, 
        $enrollment_id,
        $requirement_id, 
        $status,
        $date_submitted,
        $staff_id){
            $sql = "INSERT INTO " . $this->table_name . " (
            student_requirement_id,
            enrollment_id, 
            requirement_id, 
            status,
            date_submitted,
            staff_id
            ) VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iisss", 
            $student_requirement_id, 
            $enrollment_id, 
            $requirement_id, 
            $status, 
            $date_submitted, 
            $staff_id);
            return $stmt->execute();
        }


        public function getStudentRequirements($enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

?>