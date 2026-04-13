<?php
class ActivityLog{
    private $conn;
    private $table_name = "activity_logging";

    public function __construct($db){
        $this->conn = $db;
    }

    public function logAction($action, $desctription){

    if (!isset($_SESSION['user_id'])) {
        return false; // No user logged in, cannot log action
    }

    $user_id = $_SESSION['user_id'];
    $timestamp = date("Y-m-d H:i:s");

    $sql = "INSERT INTO " . $this->table_name . " (
    log_id, 
    user_id, 
    action, 
    description, 
    timestamp
    ) VALUES (NULL, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isss",
        $_SESSION['user_id'],
        $action,
        $desctription,
        $timestamp);

        return $stmt->execute();
    }

    public function getAll(){
        return $this->conn->query("SELECT a.*, u.username FROM " . $this->table_name . " a INNER JOIN users u ON a.user_id = u.user_id ORDER BY a.timestamp DESC");
    }
}

?>