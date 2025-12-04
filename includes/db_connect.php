<?php
// Include environment configuration
require_once dirname(__DIR__) . '/config.php';

// Attempt to connect to MySQL database using environment settings
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    if (ENVIRONMENT === 'production') {
        // Log error in production instead of displaying it
        error_log("Database connection failed: " . $conn->connect_error);
        die("Service temporarily unavailable. Please try again later.");
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Set character set to UTF-8 for proper data handling
$conn->set_charset("utf8mb4");
?>