<?php 
    class EnrollmentProcess{
        private $conn;
        private $table_name = "enrollment_process";

        public function __construct($db){
            $this->conn = $db;
        }

        public function logStep($process_id,
                                $enrollment_id, 
                                $current_step,
                                $step_status, 
                                $timestamp,
                                $staff_id){

            $sql = "INSERT INTO " . $this->table_name . " (
            process_id, 
            enrollment_id, 
            step_status, 
            timestamp,
            staff_id) 
            VALUES (?, ?, ?, ?, ?)";


            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iss", 
            $process_id,
            $enrollment_id, 
            $current_step,
            $step_status, 
            $timestamp,
            $staff_id);
            return $stmt->execute();
        }
    }

?>