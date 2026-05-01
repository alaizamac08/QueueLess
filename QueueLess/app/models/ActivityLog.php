<?php
class ActivityLog
{
    private mysqli $conn;
    private string $table = 'activity_logging';

    public function __construct(mysqli $db)
    {
        $this->conn = $db;
    }

    public function logAction(string $action, string $description): bool
    {
        if (empty($_SESSION['user_id'])) {
            return false;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (user_id, action, description)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param('iss', $_SESSION['user_id'], $action, $description);
        return $stmt->execute();
    }

    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $perPage = min($perPage, 100);
        $offset  = ($page - 1) * $perPage;

        // Total count for pagination controls — uses index on log_id (PK)
        $count = $this->conn
            ->query("SELECT COUNT(*) FROM {$this->table}")
            ->fetch_row()[0];

        $stmt = $this->conn->prepare("
            SELECT
                a.log_id,
                a.action,
                a.description,
                a.timestamp,
                u.username
            FROM {$this->table} a
            INNER JOIN users u ON a.user_id = u.user_id
            ORDER BY a.timestamp DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'rows'  => $rows,
            'total' => (int) $count,
            'pages' => (int) ceil($count / $perPage),
        ];
    }

    public function getByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->conn->prepare("
            SELECT log_id, action, description, timestamp
            FROM {$this->table}
            WHERE user_id = ?
            ORDER BY timestamp DESC
            LIMIT ?
        ");
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
