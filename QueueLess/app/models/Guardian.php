<?php
    class Guardian{
        private mysqli $conn;
        private string $table_name = "guardians";

        public function __construct(mysqli $db){
            $this->conn = $db;
        }

        public function createGuardian(array $data){
            $sql = "INSERT INTO " . $this->table_name . " (
            first_name,
            last_name,
            relationship,
            occupation,
            contact_number)
            VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param("sssss",
            $data['first_name'],
            $data['last_name'],
            $data['relationship'],
            $data['occupation'],
            $data['contact_number']);

            return $stmt->execute();
        }

    }

?>