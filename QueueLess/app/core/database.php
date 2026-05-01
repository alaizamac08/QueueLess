<?php

class Database {
    private string $host = "localhost";
    private string $db = "queueless_db";
    private string $user = "root";
    private string $pass = "";
    public ?mysqli $conn;

    public function connect(): mysqli {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->conn->connect_error) {
            die("DB Connection failed");
        }

        return $this->conn;
    }
}