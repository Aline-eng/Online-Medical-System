<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only doctors can use this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$doctor_id = $_POST['doctor_id'] ?? '';
$patient_id = $_POST['patient_id'] ?? '';
$diagnosis = trim($_POST['diagnosis'] ?? '');
$prescription = trim($_POST['prescription'] ?? '');
$notes = trim($_POST['notes'] ?? '');

$errors = [];

// --- Validation ---
if (empty($doctor_id) || empty($patient_id)) {
    $errors['general'] = "Internal error: Doctor or patient ID missing.";
}
if (empty($diagnosis)) {
    $errors['diagnosis'] = "Diagnosis is required.";
}

if (!empty($errors)) {
    set_session_message('error', 'Please correct the errors in the form.');
    $_SESSION['form_data'] = $_POST;
    redirect('medical_records.php');
    exit();
}

// --- Process Record Insertion ---
$current_date = date('Y-m-d');

$stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_id, record_date, diagnosis, prescription, notes) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("iissss",
        $patient_id,
        $doctor_id,
        $current_date,
        $diagnosis,
        $prescription,
        $notes
    );

    if ($stmt->execute()) {
        set_session_message('success', 'Medical record added successfully.');
        redirect('doctor_dashboard.php');
    } else {
        set_session_message('error', 'Error adding medical record: ' . $stmt->error);
        redirect('medical_records.php');
    }
    $stmt->close();
} else {
    set_session_message('error', 'Database query preparation failed: ' . $conn->error);
    redirect('medical_records.php');
}

$conn->close();
exit();
?>