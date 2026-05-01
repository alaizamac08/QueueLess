<?php
require_once __DIR__ . '/../models/EnrollmentProcess.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../core/database.php';

    class EnrollmentProcessController{
    private EnrollmentProcess $process;

    public function __construct(mysqli $db){
        $this->process = new EnrollmentProcess($db);
    }

    public function logProcessStep(array $data){
        $result = $this->process->logStep(
            $data['process_id'],
            $data['enrollment_id'],
            $data['current_step'],
            $data['step_status'],
            date('Y-m-d H:i:s'),
            $data['staff_id']
        );

        if ($result){
            Logger::log(
                "SUBMIT_REQUIREMENT",
                "Requirement ID " . $data['requirement_id'] . " submitted for enrollment ID: " . $data['enrollment_id']
            );
        }

        return $result;
    }
    }


?>