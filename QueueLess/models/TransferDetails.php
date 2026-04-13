<?php 
    class TransferDetails{
        private $conn;
        private $table_name = "transfer_details";

        public function __construct($db){
            $this->conn = $db;
        }

        public function create(
            $transfer_id, 
            $enrollment_id, 
            $previous_school_name, 
            $school_address, 
            $last_grade_completed, 
            $general_average){

            $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (
                transfer_id, 
                enrollment_id, 
                previous_school_name, 
                school_address, 
                last_grade_completed, 
                general_average
            ) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("iissis", 
                $transfer_id, 
                $enrollment_id, 
                $previous_school_name, 
                $school_address, 
                $last_grade_completed, 
                $general_average
            );
            return $stmt->execute();

        }


        public function getByEnrollmentId($enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM" . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

?>