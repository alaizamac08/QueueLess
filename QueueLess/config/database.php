<?php
    class Database{
        private $conn;

        public function connect(){
            $this->conn = new mysqli(
                "localhost", 
                "root", 
                "", 
                "queueless_db");

                if ($this->conn->connect_error){
                    die("Connection failed: " . $this->conn->connect_error);
                }
                return $this->conn;
        }
    }

?>