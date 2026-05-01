<?php

class Auth
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /public/Admin_Staff_Login.php");
            exit();
        }
    }

    public static function adminOnly()
    {
        self::check();
        if ($_SESSION['role_id'] != 1) {
            http_response_code(403);
            exit("Forbidden");
        }
    }

    public static function staffOnly()
    {
        self::check();
        if ($_SESSION['role_id'] != 2) {
            http_response_code(403);
            exit("Forbidden");
        }
    }
}