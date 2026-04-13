<?php
class Role{
    public static function get(){
        session_start();
        return $_SESSION['role'] ?? null;
    }

    public static function isAdmin(){
        return self::get() === 'Admin';
    }

    public static function isStaff(){
        return self::get() === 'Staff';
    }
}

?>