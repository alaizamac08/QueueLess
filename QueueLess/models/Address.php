<?php 
    class Address{
        private $conn;
        private $table_name = "addresses";

        public function __construct($db){
            $this->conn = $db;
        }

        public function createAddress($data){
            $sql = "INSERT INTO " . $this->table_name . " (
            address_id,
            student_id, 
            street, 
            barangay, 
            city, 
            province,
            zip_code
            ) VALUES (?, ?, ?, ?, ?, ?)";



            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param("isssss",
            $data['address_id'],
            $data['student_id'],
            $data['street'],
            $data['barangay'],
            $data['city'],
            $data['province'],
            $data['zip_code']);

            return $stmt->execute();
        }

        public function getAddressByStudentId($student_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }


    }

?>