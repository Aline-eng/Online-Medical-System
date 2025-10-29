<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Store form data in session for re-population if there are errors
    $_SESSION['form_data'] = $_POST;

    // Initialize an array to hold validation errors
    $errors = [];

    // --- Validation ---
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($subject)) {
        $errors['subject'] = "Subject is required.";
    }
    if (empty($message)) {
        $errors['message'] = "Message is required.";
    }

    // If there are any validation errors, store them and redirect back
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        set_session_message('error', 'Please correct the errors in the form.');
        redirect('contact_us.php');
        exit();
    }
    
    // --- Insert message into the database ---
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            set_session_message('success', 'Thank you for your message! We will get back to you shortly.');
        } else {
            set_session_message('error', 'Error sending your message: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    }
    redirect('contact_us.php');
    exit();

} else {
    // If accessed directly without POST request, redirect
    redirect('contact_us.php');
    exit();
}

$conn->close();