<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only patients can use this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

// Collect form data
$patient_user_id = $_SESSION['user_id'];
$doctor_id = $_POST['doctor_id'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$appointment_time = $_POST['appointment_time'] ?? ''; // Corrected variable name to match the new form input
$reason = trim($_POST['reason'] ?? '');

// Store form data in session for re-population if there are errors
$_SESSION['form_data'] = $_POST;

// Initialize an array to hold validation errors
$errors = [];

// --- Validation ---
if (empty($doctor_id)) {
    $errors['doctor_id'] = "Please select a doctor.";
}
if (empty($appointment_date)) {
    $errors['appointment_date'] = "Appointment date is required.";
}
if (empty($appointment_time)) {
    $errors['appointment_time'] = "Appointment time is required.";
}
if (empty($reason)) {
    $errors['reason'] = "Reason for appointment is required.";
}

// Check if the chosen date is in the future
if (!empty($appointment_date) && strtotime($appointment_date) < strtotime('today')) {
    $errors['appointment_date'] = "You cannot book an appointment in the past.";
}

// Check if the chosen time slot is actually available
if (empty($errors)) {
    $stmt_check_schedule = $conn->prepare("
        SELECT schedule_id
        FROM doctor_schedules
        WHERE doctor_id = ? AND available_date = ? AND available_time = ?
    ");
    $stmt_check_schedule->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    $stmt_check_schedule->execute();
    $stmt_check_schedule->store_result();

    if ($stmt_check_schedule->num_rows == 0) {
        $errors['appointment_time'] = "The selected time slot is not available or has been removed. Please choose another one.";
    }
    $stmt_check_schedule->close();
}

// Check for existing appointments for this doctor at the same date and time
if (empty($errors)) {
    // First, get the patient's `patient_id` from their `user_id`
    $stmt_get_patient_id = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = ?");
    $stmt_get_patient_id->bind_param("i", $patient_user_id);
    $stmt_get_patient_id->execute();
    $result_patient_id = $stmt_get_patient_id->get_result();
    $patient_id_row = $result_patient_id->fetch_assoc();
    $patient_id = $patient_id_row['patient_id'];
    $stmt_get_patient_id->close();

    // Check for conflicts
    $stmt_check_appointment = $conn->prepare("SELECT appointment_id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?");
    $stmt_check_appointment->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    $stmt_check_appointment->execute();
    $stmt_check_appointment->store_result();

    if ($stmt_check_appointment->num_rows > 0) {
        $errors['appointment_time'] = "This time slot is already booked for this doctor. Please choose another time.";
    }
    $stmt_check_appointment->close();
}


// If there are any validation errors, store them and redirect back
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    set_session_message('error', 'Please correct the errors in the form.');
    redirect('book_appointment.php');
    exit();
}

// --- Process Appointment Booking if no errors ---
$stmt_insert = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
if ($stmt_insert) {
    // Bind parameters
    $stmt_insert->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);

    if ($stmt_insert->execute()) {
        set_session_message('success', 'Appointment booked successfully!');
        redirect('patient_dashboard.php'); // Redirect to patient dashboard on success
    } else {
        set_session_message('error', 'Error booking appointment: ' . $stmt_insert->error);
        redirect('book_appointment.php');
    }
    $stmt_insert->close();
} else {
    set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    redirect('book_appointment.php');
}
$conn->close();
exit();
