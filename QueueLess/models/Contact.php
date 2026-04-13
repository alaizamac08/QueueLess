<?php 
    class Contact{
        private $conn;
        private $table_name = "contacts";

        public function __construct($db){
            $this->conn = $db;
        }

        public function createContact($data){
            $sql = $this->conn->prepare("INSERT INTO " . $this->table_name . " (
            contact_id,
            student_id, 
            phone_number, 
            email
            ) VALUES (?, ?, ?, ?)");

            $sql->bind_param("isss",
            $data['contact_id'],
            $data['student_id'],
            $data['phone_number'],
            $data['email']);

            return $sql->execute();

        }


        public function getByStudent($student_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

?>