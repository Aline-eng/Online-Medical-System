<?php
// Environment Configuration
// Set to 'production' when hosting online, 'development' for local
define('ENVIRONMENT', 'development'); // Change to 'production' for live hosting

// Database Configuration
if (ENVIRONMENT === 'production') {
    // Production database settings (update these with your hosting provider's details)
    define('DB_SERVER', 'your-hosting-server.com');
    define('DB_USERNAME', 'your_db_username');
    define('DB_PASSWORD', 'your_db_password');
    define('DB_NAME', 'your_db_name');
    
    // Production settings
    define('BASE_URL', 'https://yourdomain.com/');
    define('SITE_URL', 'https://yourdomain.com');
    
    // Error reporting (hide errors in production)
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    // Development database settings (current XAMPP setup)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'online_medical_system');
    
    // Development settings
    define('BASE_URL', 'http://localhost/online_medical_system/');
    define('SITE_URL', 'http://localhost/online_medical_system');
    
    // Error reporting (show errors in development)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', 'uploads/');
?>