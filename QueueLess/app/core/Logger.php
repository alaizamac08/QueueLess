<?php

class Logger
{
    private static mysqli $db;

    public static function init(mysqli $db)
    {
        self::$db = $db;
    }

    public static function log(string $action, string $description)
    {
        $userId = $_SESSION['user_id'] ?? null;

        $stmt = self::$db->prepare("
            INSERT INTO activity_logging (user_id, action, description)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param("iss", $userId, $action, $description);
        $stmt->execute();
    }
}