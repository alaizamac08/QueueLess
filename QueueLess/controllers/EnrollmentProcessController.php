<?php 
    class EnrollmentProcessController{
    private $process;

    public function __construct($db){
        $this->process = new EnrollmentProcess($db);
    }

    public function logProcessStep($data){
        
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