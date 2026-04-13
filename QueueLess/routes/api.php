<?php
session_start();
require_once "../config/database.php";
require_once "../controller/AuthController.php";
require_once "../controller/EnrollmentController.php";


$db = (new Database())->connect();

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if($request == "/api/login" && $method == "POST"){
    
    $data = json_decode(file_get_contents("php://input"), true);

    (new AuthController($db))->login(
        $data['username'],
        $data['password']
    );
}

if ($request == "/api/enroll"){
    $data = json_decode(file_get_contents("php://input"), true);

    (new EnrollmentController($db))->enrollStudent($data);
}

?>