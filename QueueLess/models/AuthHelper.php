<?php 
    class AuthHelper{
        private $conn;

        public function __construct($db){
            $this->conn = $db;
        }

        public function gerUserRole($user_id){
            $stmt = $this->conn->prepare(
                "SELECT r.role_name 
                FROM roles r
                INNER JOIN user_roles ur 
                ON r.role_id = ur.role_id
                WHERE ur.user_id = ?"
            );

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $roles = [];

            while ($row = $result->fetch_assoc()){
                $roles[] = $row['role_name'];
            }

            return $roles;
        }

    }


?>