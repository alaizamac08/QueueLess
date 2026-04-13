<?php 
    class Guardian{
        private $conn;
        private $table_name = "guardians";

        public function __construct($db){
            $this->conn = $db;
        }

        public function createGuardian($data){
            $sql = "INSERT INTO " . $this->table_name . " (
            guardian_id, 
            student_id, 
            first_name, 
            last_name,
            relationship,
            occupation, 
            contact_number) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param("isssss",
            $data['guardian_id'],
            $data['student_id'],
            $data['first_name'],
            $data['last_name'],
            $data['relationship'],
            $data['occupation'],
            $data['contact_number']);

            return $stmt->execute();
        }

    }

?>