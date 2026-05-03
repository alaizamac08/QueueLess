<?php
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/NewStudent.php';
require_once __DIR__ . '/../models/TransferDetails.php';
require_once __DIR__ . '/../models/RequirementChecker.php';
require_once __DIR__ . '/../models/Document.php';


class EnrollmentController
{
    private mysqli $conn;
    private Enrollment $enrollment;
    private NewStudentDetails $newStudent;
    private TransferDetails $transferStudent;
    private RequirementChecker $requirementChecker;
    private Document $document;

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
        $this->enrollment = new Enrollment($db);
        $this->newStudent = new NewStudentDetails($db);
        $this->transferStudent = new TransferDetails($db);
        $this->requirementChecker = new RequirementChecker($db);
        $this->document = new Document($db);
    }

    public function enrollStudent(array $data): int
    {
        if (!isset($data['enrollment_type'])) {
            throw new InvalidArgumentException("Enrollment type is required");
        }

        $type = strtolower($data['enrollment_type']);

        $this->conn->begin_transaction();

        try {

            if ($this->enrollment->studentHasEnrollment($data['student_id'])) {
                throw new RuntimeException("Student already has an enrollment.");
            }

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

    public function getStudentDashboard(int $studentId): array
    {
        $enrollment = $this->enrollment->getLatestByStudentId($studentId);

        if (!$enrollment) {
            return [
                'enrollment' => null,
                'missing_documents' => []
            ];
        }

        $missing = $this->document->getMissing($enrollment['enrollment_id']);

        return [
            'enrollment' => $enrollment,
            'missing_documents' => $missing
        ];
    }

    public function uploadDocument(int $studentId, array $file, string $type): bool
    {
        $enrollment = $this->enrollment->getByStudentId($studentId);

        if (!$enrollment) {
            throw new RuntimeException("No enrollment found.");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_', true) . '.' . $ext;
        $path = "uploads/" . $filename;

        move_uploaded_file($file['tmp_name'], __DIR__ . "/../../public/" . $path);

        $this->document->upload(
            $enrollment['enrollment_id'],
            $type,
            $path
        );

        // 🔥 ADD THIS: refresh requirement status
        $checker = new RequirementChecker($this->conn);
        $status = $checker->checkCompletion($enrollment['enrollment_id']);

        $stmt = $this->conn->prepare("
            UPDATE enrollments 
            SET status = ? 
            WHERE enrollment_id = ?
        ");

        $stmt->bind_param("si", $status, $enrollment['enrollment_id']);
        $stmt->execute();

        return true;
    }

    public function submitEnrollment(int $studentId): bool
    {
        $enrollment = $this->enrollment->getByStudentId($studentId);

        if (!$enrollment) {
            throw new RuntimeException("No enrollment found.");
        }

        return $this->enrollment->submit($enrollment['enrollment_id']);
    }
}