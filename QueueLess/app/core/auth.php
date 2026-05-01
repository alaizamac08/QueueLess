<?php

class Auth {

    private static function getRoleName(int $roleId): ?string
    {
        switch ($roleId) {
            case 1:
                return 'admin';
            case 2:
                return 'staff';
            default:
                return null;
        }
    }

    public static function checkRole(string $role) {
        if (isset($_SESSION['roles']) && in_array($role, $_SESSION['roles'], true)) {
            return;
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === $role) {
            return;
        }

        if (isset($_SESSION['role_id'])) {
            $roleId = filter_var($_SESSION['role_id'], FILTER_VALIDATE_INT);
            if ($roleId !== false && self::getRoleName($roleId) === $role) {
                return;
            }
        }

        header("Location: /login");
        exit();
    }

    public static function user() {
        return $_SESSION['user_id'] ?? null;
    }
}