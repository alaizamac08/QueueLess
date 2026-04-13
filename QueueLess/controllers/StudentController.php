<?php
class StudentController{
    private $student;

    public function __construct($db){
        $this->student = new Student($db);
    }

    public function registerStudent($data){
        
        $result = $this->student->createStudent(
            $data['lrn'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['suffix'],
            $data['sex'],
            $data['birth_date'],
            $data['age'],
            $data['place_of_birth'],
            $data['nationality']
        );

        if ($result){
            Logger::log(
                "CREATE_STUDENT",
                "Added student: " . $data['first_name'] . " " . $data['last_name']
            );
        }
        return $result;
        
    }

    public function getStudentById($student_id){
        return $this->student->getStudentById($student_id);
    }
    
}
?>