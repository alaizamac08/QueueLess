<?php
    class TransferDetails{
        private mysqli $conn;
        private string $table_name = "transfer_details";

        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function create(
            int $enrollment_id,
            string $previous_school_name,
            string $school_address,
            string $last_grade_completed,
            float $general_average){

            $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (
                enrollment_id,
                previous_school_name,
                school_address,
                last_grade_completed,
                general_average
            ) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("isssi",
                $enrollment_id,
                $previous_school_name,
                $school_address,
                $last_grade_completed,
                $general_average
            );
            return $stmt->execute();

        }


        public function getByEnrollmentId(int $enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

?>