<?php
    class Staff{
        private mysqli $conn;
        private string $table_name = "staff";

        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function createStaff(array $data){
            $sql = "INSERT INTO " . $this->table_name . " (
            staff_id,
            user_id,
            first_name,
            middle_name,
            last_name,
            position,
            contact_number,
            email)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param("issss",
            $data['user_id'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['position'],
            $data['contact_number'],
            $data['email']);

            return $stmt->execute();
        }
    }


?>