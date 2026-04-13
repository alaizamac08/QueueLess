<?php
class AdminController{
    public function countStudents($conn){
        return $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc();
    }

    public function countEnrollments($conn){
        return $conn->query("SELECT COUNT(*) AS total FROM enrollments")->fetch_assoc();
    }

    public function pendingEnrollments($conn){
        return $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE status = 'pending'")->fetch_assoc();
    }
}


?>