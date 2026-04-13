<?php 
class DashboardController{
    private $conn;
    private $table_name = "activity_logging";
    private $table_name_ep = "enrollment_process";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function adminDashboard(){
        AccessControl::requireRole(['Admin']);

        $data = [];

        // total students
        $data['enrollment'] = $this->conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];

        // total enrollments
        $data['enrollment'] = $this->conn->query("SELECT COUNT(*) as total FROM enrollments")->fetch_assoc()['total'];

        // status
        $statusQuery = $this->conn->query(
            "SELECT status, COUNT(*) as count
            FROM enrollments
            GROUP BY status"
        );

        $status = [];
        while($row = $statusQuery->fetch_assoc()){
            $status[] = $row;
        }

        $data['status_breakdown'] = $status;

        // recent logs
        $logs = $this->conn->query(
            "SELECT * FROM " . $this->table_name . " ORDER BY timestamp DESC LIMIT 10"
        );

        $data['recent_logs'] = $logs;

        return $data;
    }

    public function staffDashboard(){
        AccessControl::requireRole(['staff', 'admin']);

        $data = [];

        // pending enrollments
        $data['pending'] = $this->conn->query(
            "SELECT * FROM enrollments WHERE status = 'Pending'"
        );

        // incomplete enrollments
        $data['incomplete'] = $this->conn->query(
            "SELECT * FROM enrollments WHERE status = 'Incomplete'"
        );

        // recently processed
        $data['recent'] = $this->conn->query(
            "SELECT * FROM" . $this->table_name_ep . "ORDER BY timestamp DESC LIMIT 10"
        );
        return $data;
    }
}

?>