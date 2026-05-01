<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/StaffController.php';
require_once __DIR__ . '/../app/controllers/EnrollmentController.php';

function route(string $request, string $method, mysqli $db)
{
    $path = parse_url($request, PHP_URL_PATH);

    // AUTH
    if ($path === '/login' && $method === 'POST') {
        (new AuthController($db))->login($_POST);
        return;
    }

    if ($path === '/logout') {
        (new AuthController($db))->logout();
        return;
    }

    // ADMIN
    if ($path === '/admin/dashboard') {
        (new AdminController($db))->dashboard();
        return;
    }

    if ($path === '/admin/users') {
        (new AdminController($db))->users();
        return;
    }

    if ($path === '/admin/reports') {
        (new AdminController($db))->reports();
        return;
    }

    // STAFF
    if ($path === '/staff/dashboard') {
        (new StaffController($db))->dashboard();
        return;
    }

    if ($path === '/staff/profile') {
        (new StaffController($db))->profile();
        return;
    }

    // DEFAULT
    http_response_code(404);
    echo "404 Not Found";
}