<?php
require_once '../QueueLess/controllers/AuthController.php';
require_once '../QueueLess/models/User.php';
require_once '../QueueLess/config/database.php';

$db = (new Database())->connect();

$user = new User($db);
$auth = new AuthController($db);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$auth->login(['username' => $username, 'password' => $password]);

?>