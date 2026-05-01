<?php

class ReportController {

    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    public function getSummary(string $from, string $to) {

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(status = 'approved') as approved,
                    SUM(status = 'pending') as pending,
                    SUM(status = 'rejected') as rejected
                FROM enrollments
                WHERE created_at BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getDetailed(string $from, string $to) {

        $sql = "SELECT
                    e.*, u.username
                FROM enrollments e
                LEFT JOIN users u ON e.processed_by = u.user_id
                WHERE e.created_at BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();

        return $stmt->get_result();
    }
}