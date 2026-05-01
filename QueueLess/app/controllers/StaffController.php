<?php

require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/Logger.php';

class StaffController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function dashboard()
    {
        Auth::staffOnly();
        require __DIR__ . '/../../views/staff/dashboard.php';
    }

    public function profile()
    {
        Auth::staffOnly();

        $user_id = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        require __DIR__ . '/../../views/staff/profile.php';
    }
}