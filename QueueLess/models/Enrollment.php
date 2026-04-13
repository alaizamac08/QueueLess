<?php
class Enrollment{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function createEnrollment($data){
        $sql = "INSERT INTO enrollments(
        student_id,
        school_year,
        grade_level,
        section,
        enrollment_type,
        status) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isssss",
        $data['student_id'],
        $data['school_year'],
        $data['grade_level'],
        $data['section'],
        $data['enrollment_type'],
        $data['status']);

        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function updateStatus($enrollment_id, $status){
        $stmt = $this->conn->prepare(
            "UPDATE enrollments SET status = ? WHERE enrollment_id = ?"
        );
        $stmt->bind_param("si", $status, $enrollment_id);
        return $stmt->execute();
    }

    public function getEnrollmentById($enrollment_id){
        $sql = "SELECT * FROM enrollments WHERE enrollment_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $enrollment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLastInsertId(){
        return $this->conn->insert_id;
    }
}
?>