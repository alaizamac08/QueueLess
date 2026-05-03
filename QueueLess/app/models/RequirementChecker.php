<?php
class RequirementChecker
{
    private mysqli $conn;

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
    }

    public function checkCompletion(int $enrollmentId)
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total
            FROM documents
            WHERE enrollment_id = ?
        ");
        $stmt->bind_param('i', $enrollmentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $uploaded = (int)$row['total'];

        $required = 3; // birth_certificate, report_card, good_moral

        if ($uploaded === 0) return 'Pending';
        if ($uploaded >= $required) return 'Complete';

        return 'Incomplete';
    }
}
