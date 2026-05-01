<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../core/database.php';

class AuthController
{
    private mysqli $db;
    private User $userModel;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
        Logger::init($db);
    }

    public function login(array $data)
    {
        $username = $data['username'];
        $password = $data['password'];

        $user = $this->userModel->findByUsername($username);

        if (!$user || !$user['is_active']) {
            header("Location: /public/Admin_Staff_Login.php?error=notfound");
            exit();
        }

        if (!password_verify($password, $user['password_hash'])) {
            header("Location: /public/Admin_Staff_Login.php?error=invalid");
            exit();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role'] = $user['role_id'] == 1 ? 'admin' : 'staff';
        $_SESSION['roles'] = [$_SESSION['role']];

        Logger::log("LOGIN", "User logged in: $username");

        if ($user['role_id'] == 1) {
            header("Location: /admin/dashboard");
        } else {
            header("Location: /staff/dashboard");
        }
        exit();
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            Logger::log("LOGOUT", "User logged out: " . $_SESSION['user_id']);
        }

        session_destroy();
        header("Location: /public/Admin_Staff_Login.php");
        exit();
    }
}