<?php
// We start a session and include necessary files on every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';
require_once 'db_connect.php';

// Access control: Ensure user is logged in and is an admin
if (!is_logged_in() || get_user_role() !== 'admin') {
    redirect('../login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body, main {
            padding-top: 40px;
        }

        header, footer {
            background-color: #e9ecef;
            padding: 1rem 0;
        }

        footer {
            background-color: #343a40;
            color: #6c757d;
            text-align: center;
            padding: 20px 0;
            flex-shrink: 0;
        }
        /* Custom styles for Admin Navbar spacing and professionalism */
        .navbar {
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            margin-right: 2rem;
            display: flex;
            align-items: center;
            color: #fff; /* For dark navbar background */
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar-nav .nav-item {
            margin: 0 10px;
        }

        .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover, .nav-link:focus {
            color: #fff !important;
        }

        @media (max-width: 991.98px) {
            .navbar-nav .nav-item {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="admin_dashboard.php">
                <img src="assets/images/logo.jpg" alt="MediBook Logo" style="height: 40px;">
                <span class="ml-2">MediBook | Admin Panel</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_appointments.php">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_contact_messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specializations.php">Specializations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-4">