<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    
    // Log error to file
    error_log(print_r($error, true), 3, __DIR__ . '/../logs/error.log');
    
    // Only show error in development
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
        strpos($_SERVER['HTTP_HOST'], '.test') !== false || 
        strpos($_SERVER['REMOTE_ADDR'], '127.0.0.1') !== false) {
        echo "<pre>Error: " . htmlspecialchars($errstr) . "\n";
        echo "File: " . htmlspecialchars($errfile) . "\n";
        echo "Line: " . $errline . "</pre>";
    }
    
    return true;
}

// Set the custom error handler
set_error_handler("customErrorHandler");

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
} 