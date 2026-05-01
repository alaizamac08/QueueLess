<?php
class DashboardController
{
    private mysqli $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function adminDashboard(): array
    {
        AccessControl::requireRole(['admin']);

        $stmt = $this->conn->prepare("
            SELECT
                (SELECT COUNT(*) FROM students)          AS total_students,
                COUNT(*)                                 AS total_enrollments,
                SUM(status = 'Approved')                 AS approved,
                SUM(status = 'Pending')                  AS pending,
                SUM(status = 'Rejected')                 AS rejected,
                SUM(status = 'Incomplete')               AS incomplete
            FROM enrollments
        ");
        $stmt->execute();
        $metrics = $stmt->get_result()->fetch_assoc();

        $logStmt = $this->conn->prepare("
            SELECT
                a.log_id,
                a.action,
                a.description,
                a.timestamp,
                u.username
            FROM activity_logging a
            INNER JOIN users u ON a.user_id = u.user_id
            ORDER BY a.timestamp DESC
            LIMIT 10
        ");
        $logStmt->execute();
        $recentLogs = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'total_students'     => (int) $metrics['total_students'],
            'total_enrollments'  => (int) $metrics['total_enrollments'],
            'approved'           => (int) $metrics['approved'],
            'pending'            => (int) $metrics['pending'],
            'rejected'           => (int) $metrics['rejected'],
            'incomplete'         => (int) $metrics['incomplete'],
            'recent_logs'        => $recentLogs,
        ];
    }

    public function staffDashboard(): array
    {
        AccessControl::requireRole(['staff', 'admin']);

        $enrollStmt = $this->conn->prepare("
            SELECT
                e.enrollment_id,
                e.student_id,
                e.grade_level,
                e.section,
                e.enrollment_type,
                e.status,
                e.school_year,
                CONCAT(s.first_name, ' ', s.last_name) AS student_name
            FROM enrollments e
            INNER JOIN students s ON e.student_id = s.student_id
            WHERE e.status IN ('Pending', 'Incomplete')
            ORDER BY e.status, e.enrollment_id DESC
        ");
        $enrollStmt->execute();
        $rows = $enrollStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $pending    = array_filter($rows, fn($r) => $r['status'] === 'Pending');
        $incomplete = array_filter($rows, fn($r) => $r['status'] === 'Incomplete');

        $recentStmt = $this->conn->prepare("
            SELECT
                ep.process_id,
                ep.enrollment_id,
                ep.current_step,
                ep.step_status,
                ep.timestamp,
                CONCAT(st.first_name, ' ', st.last_name) AS staff_name
            FROM enrollment_process ep
            INNER JOIN staff st ON ep.staff_id = st.staff_id
            ORDER BY ep.timestamp DESC
            LIMIT 10
        ");
        $recentStmt->execute();
        $recent = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'pending'    => array_values($pending),
            'incomplete' => array_values($incomplete),
            'recent'     => $recent,
        ];
    }
}
