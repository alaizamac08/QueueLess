<?php

class Router {
    private array $routes = [];
    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    public function get(string $route, string $action) {
        $this->routes["GET"][$route] = $action;
    }

    public function post(string $route, string $action) {
        $this->routes["POST"][$route] = $action;
    }

    public function dispatch(string $uri, string $method) {

        $path = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->routes[$method][$path])) {
            echo "404 Not Found";
            return;
        }

        $action = $this->routes[$method][$path];

        list($controllerName, $methodName) = explode("@", $action);

        require_once "../app/controllers/$controllerName.php";

        $controller = new $controllerName($this->db);
        $controller->$methodName();
    }
}