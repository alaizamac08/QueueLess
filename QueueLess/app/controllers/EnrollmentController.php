<?php

class EnrollmentController
{
    private mysqli $conn;
    private Enrollment $enrollment;
    private NewStudentDetails $newStudent;
    private TransferDetails $transferStudent;
    private RequirementChecker $requirementChecker;

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
        $this->enrollment = new Enrollment($db);
        $this->newStudent = new NewStudentDetails($db);
        $this->transferStudent = new TransferDetails($db);
        $this->requirementChecker = new RequirementChecker($db);
    }

    public function enrollStudent(array $data): int
    {
        if (!isset($data['enrollment_type'])) {
            throw new InvalidArgumentException("Enrollment type is required");
        }

        $type = strtolower($data['enrollment_type']);

        $this->conn->begin_transaction();

        try {
            $enrollmentId = $this->enrollment->createEnrollment($data);

            Logger::log(
                'ENROLLMENT_CREATE',
                "Enrollment created ID: {$enrollmentId}"
            );

            if ($type === 'new') {

                $this->newStudent->create(
                    $enrollmentId,
                    $data['previous_school'] ?? 'None',
                    (int)($data['kindergarten_completed'] ?? 0)
                );

            } elseif ($type === 'transfer') {

                $this->transferStudent->create(
                    $enrollmentId,
                    $data['previous_school_name'] ?? '',
                    $data['school_address'] ?? '',
                    $data['last_grade_completed'] ?? '',
                    (int)($data['general_average'] ?? 0)
                );

                Logger::log(
                    'ENROLLMENT_TYPE_PROCESS',
                    "Transfer enrollment processed ID: {$enrollmentId}"
                );

            } elseif ($type === 'old') {
                // Currently no extra details table for old students
                Logger::log(
                    'ENROLLMENT_TYPE_PROCESS',
                    "Old enrollment processed ID: {$enrollmentId}"
                );
            } else {
                throw new InvalidArgumentException("Invalid enrollment type: {$type}");
            }

            $this->conn->commit();
            return $enrollmentId;

        } catch (Throwable $e) {
            $this->conn->rollback();

            throw new RuntimeException(
                'enrollStudent failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function evaluateRequirements(int $enrollmentId): string
    {
        $status = $this->requirementChecker->checkCompletion($enrollmentId);

        $isComplete = ($status === 'approved') ? 1 : 0;

        $stmt = $this->conn->prepare("
            UPDATE enrollments 
            SET status = ?, is_complete = ? 
            WHERE enrollment_id = ?
        ");

        $stmt->bind_param('sii', $status, $isComplete, $enrollmentId);
        $stmt->execute();

        return $status;
    }

    public function updateEnrollmentStatus(
        int $enrollmentId,
        string $status,
        int $userId
    ): bool {
        $stmt = $this->conn->prepare("
            UPDATE enrollments 
            SET status = ? 
            WHERE enrollment_id = ?
        ");

        $stmt->bind_param('si', $status, $enrollmentId);
        $ok = $stmt->execute();

        if ($ok) {
            Logger::log(
                'STATUS_UPDATE',
                "User {$userId} set {$status} for enrollment ID {$enrollmentId}"
            );
        }

        return $ok;
    }

    public function getStudentEnrollments(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                e.enrollment_id,
                e.school_year,
                e.grade_level,
                e.section,
                e.enrollment_type,
                e.status,
                e.is_complete,
                CONCAT(s.first_name, ' ', s.last_name) AS student_name
            FROM enrollments e
            LEFT JOIN students s ON e.student_id = s.student_id
            WHERE e.student_id = ?
            ORDER BY e.enrollment_id DESC
        ");

        $stmt->bind_param('i', $studentId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}