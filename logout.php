<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the home page or login page
require_once 'includes/functions.php'; // Needed for the redirect function
set_session_message('success', 'You have been successfully logged out.');
redirect('index.php'); // Or 'login.php' if you prefer
exit();
?>