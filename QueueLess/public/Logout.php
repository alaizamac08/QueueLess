<?php 
session_start();

include(__DIR__ . '/../config/database.php');
include(__DIR__ . '/../models/ActivityLog.php');
include(__DIR__ . '/../core/Logger.php');

$db = (new Database())->connect();
Logger::init($db);

if (isset($_SESSION['user_id'])) {
    Logger::log(
        "LOGOUT",
        "User logged out: " . $_SESSION['user_id']
    );
}

// destroy the session to log out the user
$_SESSION = [];
session_destroy();

// redirect to the login page
header("Location: Admin_Staff_LogIn.php");
exit();
?>