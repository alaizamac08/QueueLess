<?php
require_once __DIR__ . '/../core/database.php';

class StaffController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        if ($db) {
            $this->db = $db;
        }
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

    public function getStaffByUserId(int $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM staff WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function createStaff(int $user_id, array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO staff
            (user_id, first_name, middle_name, last_name, position, contact_number, email)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issssss",
            $user_id,
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['position'],
            $data['contact_number'],
            $data['email']
        );

        return $stmt->execute();
    }

    public function updateStaff(int $user_id, array $data)
    {
        $stmt = $this->db->prepare("
            UPDATE staff
            SET first_name=?, middle_name=?, last_name=?, position=?, contact_number=?, email=?
            WHERE user_id=?
        ");

        $stmt->bind_param(
            "ssssssi",
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['position'],
            $data['contact_number'],
            $data['email'],
            $user_id
        );

        return $stmt->execute();
    }

    public function saveStaff(int $user_id, array $data)
    {
        $existing = $this->getStaffByUserId($user_id);

        if ($existing) {
            return $this->updateStaff($user_id, $data);
        }

        return $this->createStaff($user_id, $data);
    }
}