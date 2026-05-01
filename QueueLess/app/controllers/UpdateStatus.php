<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/EnrollmentController.php';

$db = (new Database())->connect();

if (!$db) {
    die("Database connection failed");
}

$controller = new EnrollmentController($db);

$id = $_GET['enrollment_id'];
$status = $_GET['status'];
$user_id = $_SESSION['user_id'];

$controller->updateEnrollmentStatus($id, $status, $user_id);

header('Location: ../views/staff/enrollments.php');
exit;
?>