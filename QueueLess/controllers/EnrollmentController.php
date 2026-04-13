<?php
    class EnrollmentController{
    private $enrollment;
    private $newStudent;
    private $transferStudent;
    private $requirementChecker;

    public function __construct($db){
        $this->enrollment = new Enrollment($db);
        $this->newStudent = new NewStudentDetails($db);
        $this->transferStudent = new TransferDetails($db);
        $this->requirementChecker = new RequirementChecker($db);
    }

    public function enrollStudent($data){
        $enrollment_id = $this->enrollment->createEnrollment($data);

        Logger::log(
            "ENROLLMENT_CREATE",
            "Enrollment created ID: " . $enrollment_id
        );

        if ($data['enrollment_type'] == 'New'){

        $this->newStudent->create(
            null,
            $enrollment_id,
            $data['previous_school'] ?? "None",
            $data['kindergarten_completed'] ?? 0
        );
        } elseif ($data['enrollment_type'] == 'Transfer'){
        $this->transferStudent->create(
            null,
            $enrollment_id,
            $data['previous_school'],
            $data['school_address'] ?? '',
            $data['last_grade_completed'],
            $data['general_average'] ?? 0
        );

        Logger::log(
            "ENROLLMENT_TYPE_PROCESS",
            "Processed transfer enrollment ID: " . $enrollment_id
        );
        }
        return $enrollment_id;
    }

    public function evaluateRequirements($enrollment_id){
        $status = $this->requirementChecker->checkCompletion($enrollment_id);
        $this->enrollment->updateStatus($enrollment_id, $status);
        return $status;
    }

    public function updateEnrollmentStatus($enrollment_id, $status){
        return $this->enrollment->updateStatus($enrollment_id, $status);
    }

    public function getStudentEnrollments($student_id){
        return $this->enrollment->getEnrollmentById($student_id);

    }

}
?>