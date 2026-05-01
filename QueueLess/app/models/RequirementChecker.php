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
            SELECT
                COUNT(DISTINCT r.requirement_id)       AS total,
                SUM(sr.status = 'Submitted')           AS submitted
                FROM requirements r
                LEFT JOIN students_requirements sr
                ON sr.requirement_id  = r.requirement_id
                AND sr.enrollment_id   = ?
                AND sr.status          = 'Submitted'
        ");
        $stmt->bind_param('i', $enrollmentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $total     = (int) ($row['total']     ?? 0);
        $submitted = (int) ($row['submitted'] ?? 0);

        if ($total === 0 || $submitted === 0) {
            return 'Pending';
        }

        return ($submitted >= $total) ? 'Complete' : 'Incomplete';
    }
}
