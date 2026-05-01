<?php

require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../middleware/Auth.php';

class AdminController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function dashboard()
    {
        Auth::adminOnly();
        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function users()
    {
        Auth::adminOnly();

        $users = $this->db->query("SELECT * FROM users");
        require __DIR__ . '/../../views/admin/users.php';
    }

    public function reports()
    {
        Auth::adminOnly();

        $from = $_GET['from'] ?? date('Y-01-01');
        $to = $_GET['to'] ?? date('Y-m-d');

        require __DIR__ . '/../../views/admin/reports.php';
    }
}