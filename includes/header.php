<?php
// header.php
// Start session if not already started (important for session messages)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Require functions and database connection
require_once 'functions.php';
require_once 'db_connect.php';

// Check if a user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Determine the dashboard link based on the user's role
$dashboard_link = 'index.php';
if ($is_logged_in && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'patient') {
        $dashboard_link = 'patient_dashboard.php';
    } elseif ($_SESSION['role'] === 'doctor') {
        $dashboard_link = 'doctor_dashboard.php';
    } elseif ($_SESSION['role'] === 'admin') {
        $dashboard_link = 'admin_dashboard.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Medical System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="nav-brand" href="<?php echo $dashboard_link; ?>" >
                <img src="assets/images/logo.jpg" alt="MediBook Logo" style="height: 40px; margin-right: 10px;" > </a>
                <a class="navbar-brand" href="<?php echo $dashboard_link; ?>">MediBook</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <?php if (!$is_logged_in): // If not logged in ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="doctors.php">Doctors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_us.php">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faq.php">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <?php else: // If logged in ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $dashboard_link; ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_us.php">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faq.php">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="py-4"> <div class="container">
            <?php display_session_message(); // Display any session messages here ?>