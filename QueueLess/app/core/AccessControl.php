<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Requirement.php';
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Role.php';


    class AccessControl{
        private Student $student;
        private Requirement $requirement;


        public function __construct(Student $studentModel, Requirement $requirementModel){
            $this->student = $studentModel;
            $this->requirement = $requirementModel;
        }


private static function getUserRoles(): array
    {
        if (isset($_SESSION['roles']) && is_array($_SESSION['roles']) && !empty($_SESSION['roles'])) {
            return $_SESSION['roles'];
        }

        if (isset($_SESSION['role']) && is_string($_SESSION['role']) && $_SESSION['role'] !== '') {
            return [$_SESSION['role']];
        }

        if (isset($_SESSION['role_id'])) {
            $roleId = filter_var($_SESSION['role_id'], FILTER_VALIDATE_INT);
            if ($roleId !== false) {
                $roleName = self::getRoleName($roleId);
                if ($roleName !== null) {
                    return [$roleName];
                }
            }
        }

        return [];
    }

    private static function getRoleName(int $roleId): ?string
    {
        switch ($roleId) {
            case 1:
                return 'admin';
            case 2:
                return 'staff';
            default:
                return null;
        }
    }

    public static function requireRole(array $allowedRoles){
        $userRoles = self::getUserRoles();
        if (empty($userRoles)) {
            die("Access denied: Not logged in.");
        }

        foreach ($userRoles as $userRole){
            if (in_array($userRole, $allowedRoles, true)){
                    return true;
                }
            }

            die("Access denied: Insufficient permissions.");
        }

        public function deleteStudent(int $student_id){
            self::requireRole(['admin']);
            return $this->student->deleteStudent($student_id);
        }

        public function addRequirement(array $data){
            self::requireRole(['admin', 'staff']);
            return $this->requirement->addRequirement($data['name']);
        }
    }

?>