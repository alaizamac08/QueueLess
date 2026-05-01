<?php
class Controller {

    protected function view(string $path, array $data = []) {
        extract($data);
        require __DIR__ . "/../../views/" . $path . ".php";
    }

    protected function redirect(string $url) {
        header("Location: index.php?url=" . $url);
        exit();
    }
}