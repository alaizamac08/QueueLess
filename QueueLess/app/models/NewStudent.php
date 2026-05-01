<?php
class NewStudentDetails{
    private mysqli $conn;
    private string $table_name = "new_student_details";

    public function __construct(mysqli $db){
        $this->conn = $db;
    }

    public function create(
        int $enrollment_id,
        string $previous_school,
        bool $kindergarten_completed){
            $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (
            enrollment_id,
            previous_school,
            kindergarten_completed
            ) VALUES (?, ?, ?)");

            $stmt->bind_param("isi", $enrollment_id, $previous_school, $kindergarten_completed);

            return $stmt->execute();
        }


        public function getByEnrollmentId(int $enrollment_id){
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE enrollment_id = ?");
            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
}
?>