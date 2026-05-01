<?php 
    class EnrollmentProcess{
        private mysqli $conn;
        private string $table_name = "enrollment_process";

        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function logStep(int $process_id,
                                int $enrollment_id,
                                string $current_step,
                                string $step_status,
                                string $timestamp,
                                int $staff_id){

            $sql = "INSERT INTO " . $this->table_name . " (
            process_id,
            enrollment_id,
            step_status,
            timestamp,
            staff_id)
            VALUES (?, ?, ?, ?, ?, ?)";


            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iisssi",
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