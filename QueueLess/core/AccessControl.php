<?php 
    class AccessControl{
        private $student;
        private $requirement;

        public function __construct($studentModel, $requirementModel){
            $this->student = $studentModel;
            $this->requirement = $requirementModel;
        }


        public static function requireRole($allowedRoles){
            if (!isset($_SESSION['roles']) || empty($_SESSION['roles'])){
                die("Access denied: Not logged in.");
            }

            foreach ($_SESSION['roles'] as $userRole){
                if (in_array($userRole, $allowedRoles)){
                    return true;
                }
            }

            die("Access denied: Insufficient permissions.");
        }

        public function deleteStudent($student_id){
            self::requireRole(['admin']);
            return $this->student->delete($student_id);
        }

        public function addRequirement($data){
            self::requireRole(['admin', 'staff']);
            return $this->requirement->createRequirement($data['name']);
        }
    }

?>