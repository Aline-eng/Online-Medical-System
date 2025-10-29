<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only doctors can use this script via POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$action = $_POST['action'] ?? '';
$doctor_id = $_POST['doctor_id'] ?? '';

// Handle add action
if ($action === 'add') {
    $available_date = $_POST['available_date'] ?? '';
    $available_time = $_POST['available_time'] ?? '';

    // Validation
    $errors = [];
    if (empty($available_date)) {
        $errors['available_date'] = "Date is required.";
    }
    if (empty($available_time)) {
        $errors['available_time'] = "Time is required.";
    }
    
    // Check if the date is in the future
    if (!empty($available_date) && strtotime($available_date) < strtotime('today')) {
        $errors['available_date'] = "You cannot add a time slot in the past.";
    }

    // Check for existing schedule entry
    if (empty($errors)) {
        $stmt_check = $conn->prepare("SELECT schedule_id FROM doctor_schedules WHERE doctor_id = ? AND available_date = ? AND available_time = ?");
        $stmt_check->bind_param("iss", $doctor_id, $available_date, $available_time);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $errors['available_time'] = "This time slot is already in your schedule.";
        }
        $stmt_check->close();
    }
    
    // Check for existing appointment
    if (empty($errors)) {
        $stmt_check_app = $conn->prepare("SELECT appointment_id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?");
        $stmt_check_app->bind_param("iss", $doctor_id, $available_date, $available_time);
        $stmt_check_app->execute();
        $stmt_check_app->store_result();
        if ($stmt_check_app->num_rows > 0) {
            $errors['available_time'] = "An appointment is already booked for this time. Cannot add a time slot.";
        }
        $stmt_check_app->close();
    }
    
    // If there are errors, redirect back
    if (!empty($errors)) {
        set_session_message('error', 'Please correct the errors in the form.');
        $_SESSION['form_data'] = $_POST;
        $_SESSION['form_errors'] = $errors;
        redirect('manage_schedule.php');
        exit();
    }
    
    // Insert new schedule entry
    $stmt_insert = $conn->prepare("INSERT INTO doctor_schedules (doctor_id, available_date, available_time) VALUES (?, ?, ?)");
    if ($stmt_insert) {
        $stmt_insert->bind_param("iss", $doctor_id, $available_date, $available_time);
        if ($stmt_insert->execute()) {
            set_session_message('success', 'Time slot added successfully!');
        } else {
            set_session_message('error', 'Error adding time slot: ' . $stmt_insert->error);
        }
        $stmt_insert->close();
    } else {
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    }
    redirect('manage_schedule.php');
    exit();
} 
// Handle delete action
elseif ($action === 'delete') {
    $schedule_id = $_POST['schedule_id'] ?? null;
    
    if (empty($schedule_id)) {
        set_session_message('error', 'Invalid schedule ID.');
        redirect('manage_schedule.php');
        exit();
    }
    
    // Check if an appointment is booked for this schedule
    $stmt_check_app = $conn->prepare("SELECT appointment_id FROM appointments WHERE doctor_id = ? AND appointment_date = (SELECT available_date FROM doctor_schedules WHERE schedule_id = ?) AND appointment_time = (SELECT available_time FROM doctor_schedules WHERE schedule_id = ?)");
    $stmt_check_app->bind_param("iii", $doctor_id, $schedule_id, $schedule_id);
    $stmt_check_app->execute();
    $stmt_check_app->store_result();
    
    if ($stmt_check_app->num_rows > 0) {
        set_session_message('error', 'Cannot delete this time slot, an appointment is already booked.');
        redirect('manage_schedule.php');
        exit();
    }
    $stmt_check_app->close();
    
    // Delete the schedule entry
    $stmt_delete = $conn->prepare("DELETE FROM doctor_schedules WHERE schedule_id = ? AND doctor_id = ?");
    if ($stmt_delete) {
        $stmt_delete->bind_param("ii", $schedule_id, $doctor_id);
        if ($stmt_delete->execute()) {
            if ($stmt_delete->affected_rows > 0) {
                set_session_message('success', 'Time slot deleted successfully.');
            } else {
                set_session_message('error', 'Time slot not found or you do not have permission to delete it.');
            }
        } else {
            set_session_message('error', 'Error deleting time slot: ' . $stmt_delete->error);
        }
        $stmt_delete->close();
    } else {
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    }
    redirect('manage_schedule.php');
    exit();
}
else {
    set_session_message('error', 'Invalid action specified.');
    redirect('doctor_dashboard.php');
    exit();
}

$conn->close();
?>