<?php
    class StudentRequirement{
        private mysqli $conn;
        private string $table_name = "student_requirements";


        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function addStudentRequirement(
            int $enrollment_id,
            int $requirement_id,
            string $status,
            string $date_submitted,
            int $staff_id,
            string $file_path
        ){
            $sql = "INSERT INTO " . $this->table_name . " (
                enrollment_id,
                requirement_id,
                status,
                date_submitted,
                staff_id,
                file_path
            ) VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param(
                "iissis",
                $enrollment_id,
                $requirement_id,
                $status,
                $date_submitted,
                $staff_id,
                $file_path
            );

            return $stmt->execute();
        }


        public function getStudentRequirements(int $enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

?>