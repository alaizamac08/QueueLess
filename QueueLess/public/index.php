<?php
session_start();

require_once "../app/core/Router.php";
require_once "../app/core/Database.php";

$db = (new Database())->connect();

$router = new Router($db);

/* AUTH ROUTES */
$router->get("/", "AuthController@showLogin");
$router->get("/login", "AuthController@showLogin");
$router->post("/login", "AuthController@login");
$router->get("/logout", "AuthController@logout");
$router->get("/register", "AuthController@showRegister");
$router->post("/register", "AuthController@register");

/* ADMIN ROUTES */
$router->get("/admin/dashboard", "AdminController@dashboard");
$router->get("/admin/users", "AdminController@users");
$router->get("/admin/reports", "ReportController@adminReport");

/* STAFF ROUTES */
$router->get("/staff/dashboard", "StaffController@dashboard");
$router->get("/staff/profile", "StaffController@profile");

/* DISPATCH */
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);