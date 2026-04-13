<?php 
    class RequirementChecker{
        private $conn;

        public function __construct($db){
            $this->conn = $db;
        }

        public function checkCompletion($enrollment_id){
            // get total required documents
            $totalReqQuery = "SELECT COUNT(*) as total FROM requirements";
            $totalReqResult = $this->conn->query($totalReqQuery);
            $totalRequired = $totalReqResult->fetch_assoc()['total'];

            // get submitted requirements for this enrollment
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as submitted
                FROM student_requirements
                WHERE enrollment_id = ? AND status = 'Submitted'"
            );

            $stmt->bind_param("i", $enrollment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $submitted = $result->fetch_assoc()['submitted'];

            // determine if all requirements are submitted
            if ($submitted == 0){
                return "Pending";
            } elseif ($submitted == $totalRequired) {
                return "Complete";
            } else {
                return "Incomplete";
            }
        }
    }

?>