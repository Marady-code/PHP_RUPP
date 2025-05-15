<?php
class Logger {
    private static $logFile = 'api_error.log';
    
    public static function init() {
        // Set custom error handler
        set_error_handler(array('Logger', 'handleError'));
        // Set exception handler
        set_exception_handler(array('Logger', 'handleException'));
        // Register shutdown function for fatal errors
        register_shutdown_function(array('Logger', 'handleFatalError'));
    }
    
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $message = date('Y-m-d H:i:s') . " [ERROR] $errstr in $errfile on line $errline";
        self::writeToLog($message);
        return true; // Don't execute PHP internal error handler
    }
    
    public static function handleException($exception) {
        $message = date('Y-m-d H:i:s') . " [EXCEPTION] " . $exception->getMessage() . 
                  " in " . $exception->getFile() . " on line " . $exception->getLine() .
                  "\nTrace: " . $exception->getTraceAsString();
        self::writeToLog($message);
        
        // Return a JSON response for the API
        header("HTTP/1.1 500 Internal Server Error");
        header("Content-Type: application/json");
        echo json_encode(array(
            "status" => "error",
            "message" => "An internal error occurred. Please try again later.",
            "error_id" => uniqid()
        ));
        exit;
    }
    
    public static function handleFatalError() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $message = date('Y-m-d H:i:s') . " [FATAL ERROR] " . $error['message'] . 
                      " in " . $error['file'] . " on line " . $error['line'];
            self::writeToLog($message);
        }
    }
    
    public static function log($message, $level = "INFO") {
        $formattedMessage = date('Y-m-d H:i:s') . " [$level] $message";
        self::writeToLog($formattedMessage);
    }
    
    private static function writeToLog($message) {
        $logPath = dirname(__FILE__) . '/' . self::$logFile;
        file_put_contents($logPath, $message . PHP_EOL, FILE_APPEND);
    }
}

// Initialize the logger
Logger::init();
?>
