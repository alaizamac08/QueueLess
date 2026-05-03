<?php
session_start();

require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/Logger.php';

$db = (new Database())->connect();
Logger::init($db);

if (isset($_SESSION['user_id'])) {
    Logger::log(
        "LOGOUT",
        "User logged out: " . $_SESSION['user_id']
    );
}

$_SESSION = [];
session_destroy();

header("Location: /queueless/views/main/main_page.html");
exit();