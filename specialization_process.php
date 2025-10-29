<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only admins can use this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$action = $_POST['action'] ?? '';

// Handle add specialization action
if ($action === 'add') {
    $specialization_name = trim($_POST['specialization_name'] ?? '');

    if (empty($specialization_name)) {
        set_session_message('error', 'Specialization name cannot be empty.');
        redirect('specializations.php');
        exit();
    }

    // Check if the specialization already exists
    $stmt = $conn->prepare("SELECT specialization_id FROM specializations WHERE name = ?");
    $stmt->bind_param("s", $specialization_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        set_session_message('error', 'Specialization with that name already exists.');
        redirect('specializations.php');
        exit();
    }
    $stmt->close();

    // Insert the new specialization
    $stmt = $conn->prepare("INSERT INTO specializations (name) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $specialization_name);
        if ($stmt->execute()) {
            set_session_message('success', 'Specialization added successfully.');
        } else {
            set_session_message('error', 'Error adding specialization: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    }
    redirect('specializations.php');
    exit();

} 
// Handle delete specialization action
elseif ($action === 'delete') {
    $specialization_id = $_POST['specialization_id'] ?? null;

    if (empty($specialization_id)) {
        set_session_message('error', 'Invalid specialization ID.');
        redirect('specializations.php');
        exit();
    }

    // Delete the specialization
    $stmt = $conn->prepare("DELETE FROM specializations WHERE specialization_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $specialization_id);
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                set_session_message('success', 'Specialization deleted successfully.');
            } else {
                set_session_message('error', 'Specialization not found.');
            }
        } else {
            set_session_message('error', 'Error deleting specialization: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    }
    redirect('specializations.php');
    exit();

} else {
    // Invalid action
    set_session_message('error', 'Invalid action specified.');
    redirect('admin_dashboard.php'); // Redirect to admin dashboard as a fallback
    exit();
}

$conn->close();
?>

