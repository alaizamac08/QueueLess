<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../core/database.php';

class StudentController{
    private Student $student;

    public function __construct(mysqli $db){
        $this->student = new Student($db);
    }

    public function registerStudent(array $data){

        $result = $this->student->createStudent($data);

        if ($result){
            Logger::log(
                "CREATE_STUDENT",
                "Added student: " . $data['first_name'] . " " . $data['last_name']
            );
        }
        return $result;
    }

    public function getStudentById(int $student_id){
        return $this->student->getStudentById($student_id);
    }
}
?>