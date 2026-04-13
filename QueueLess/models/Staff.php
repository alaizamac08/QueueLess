<?php
    class Staff{
        private $conn;
        private $table_name = "staff";

        public function __construct($db){
            $this->conn = $db;
        }

        public function createStaff($data){
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