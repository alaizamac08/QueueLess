<?php
class Enrollment {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function createEnrollment(array $data)
    {

    if ($this->studentHasEnrollment($data['student_id'])) {
            throw new RuntimeException("Enrollment already exists.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO enrollments (
                student_id,
                school_year,
                grade_level,
                section,
                enrollment_type,
                status,
                is_complete,
                created_at,
                processed_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $student_id = $data['student_id'];
        $school_year = $data['school_year'];
        $grade_level = $data['grade_level'];
        $section = $data['section'] ?? '';
        $type = strtolower($data['enrollment_type']);
        $status = $data['status'] ?? 'pending';
        $is_complete = $data['is_complete'] ?? 0;
        $created_at = date('Y-m-d H:i:s');
        $processed_by = $data['processed_by'] ?? null;

        $stmt->bind_param(
            "issssissi",
            $student_id,
            $school_year,
            $grade_level,
            $section,
            $type,
            $status,
            $is_complete,
            $created_at,
            $processed_by
        );

        $stmt->execute();

        return $this->db->insert_id;
    }

    public function studentHasEnrollment(int $studentId): bool {
        $stmt = $this->db->prepare("
            SELECT enrollment_id FROM enrollments WHERE student_id = ?
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getByStudentId(int $studentId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM enrollments WHERE student_id = ? LIMIT 1
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function submit(int $enrollmentId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE enrollments 
            SET status = 'submitted'
            WHERE enrollment_id = ?
        ");
        $stmt->bind_param("i", $enrollmentId);
        return $stmt->execute();
    }

    public function getPending() {
        return $this->db->query("
            SELECT e.enrollment_id, s.first_name, s.last_name, e.status
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            WHERE e.status = 'pending'
        ");
    }

    public function updateStatus(int $id, string $status) {
        $stmt = $this->db->prepare("
            UPDATE enrollments SET status = ? WHERE enrollment_id = ?
        ");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function getLatestByStudentId(int $studentId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM enrollments 
            WHERE student_id = ?
            ORDER BY enrollment_id DESC
            LIMIT 1
        ");

        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}