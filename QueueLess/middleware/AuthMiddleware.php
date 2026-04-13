<?php

class AuthMiddleware{
    public static function checkLogin(){
        session_start();
        if (!isset($_SESSION['user_id'])){
            die("Access denied. Please log in.");
        }
    }

    public static function checkRole($allowedRoles){
        session_start();

        if (!isset($_SESSION['role'])){
            die("Access denied. No role found.");
        }

        if (!in_array($_SESSION['role'], $allowedRoles)){
            die("Access denied. Insufficient permissions.");
        }
    }

    public static function requireAdmin(){
        self::checkRole(['Admin']);
    }

    public static function requireStaff(){
        self::checkRole(['Admin', 'Staff']);
    }
}

?>