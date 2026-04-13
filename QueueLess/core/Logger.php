<?php 
    class Logger{
        private static $log;

        public static function init($db){
            self::$log = new ActivityLog($db);
        }

        public static function log($action, $description){
            if (self::$log) {
                self::$log->logAction($action, $description);
            }
        }
    }


?>