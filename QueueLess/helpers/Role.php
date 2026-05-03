<?php
class Role{
    public static function getUserRole(){
        session_start();
        return $_SESSION['roles'] ?? [];
    }

    public static function isAdmin(){
        return in_array('admin', self::getUserRole());
    }

    public static function isStaff(){
        return in_array('staff', self::getUserRole());
    }

    public static function isStudent(){
        return in_array('student', self::getUserRole());
    }
}

?>