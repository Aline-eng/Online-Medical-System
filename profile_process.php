<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only logged-in users can use this script
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Common data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

// Initialize an array to hold validation errors
$errors = [];

// --- Common Validation ---
if (empty($username)) {
    $errors['username'] = "Username is required.";
}
if (empty($email)) {
    $errors['email'] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format.";
}

// Check for existing username or email (excluding the current user)
if (empty($errors)) {
    // Check for existing username
    $stmt_username = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
    $stmt_username->bind_param("si", $username, $user_id);
    $stmt_username->execute();
    $stmt_username->store_result();
    if ($stmt_username->num_rows > 0) {
        $errors['username'] = "Username already exists.";
    }
    $stmt_username->close();

    // Check for existing email
    $stmt_email = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt_email->bind_param("si", $email, $user_id);
    $stmt_email->execute();
    $stmt_email->store_result();
    if ($stmt_email->num_rows > 0) {
        $errors['email'] = "Email already registered.";
    }
    $stmt_email->close();
}


// --- Role-Specific Validation ---
if ($role === 'patient') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($first_name)) $errors['first_name'] = "First name is required.";
    if (empty($last_name)) $errors['last_name'] = "Last name is required.";
} elseif ($role === 'doctor') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $specialization_id = trim($_POST['specialization_id'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $office_address = trim($_POST['office_address'] ?? '');

    if (empty($first_name)) $errors['first_name'] = "First name is required.";
    if (empty($last_name)) $errors['last_name'] = "Last name is required.";
    if (empty($specialization_id)) $errors['specialization_id'] = "Specialization is required.";
    if (empty($license_number)) $errors['license_number'] = "License number is required.";

    // Check if license number already exists (excluding the current doctor)
    if (empty($errors['license_number'])) {
        $stmt_license = $conn->prepare("SELECT doctor_id FROM doctors WHERE license_number = ? AND user_id != ?");
        $stmt_license->bind_param("si", $license_number, $user_id);
        $stmt_license->execute();
        $stmt_license->store_result();
        if ($stmt_license->num_rows > 0) {
            $errors['license_number'] = "License number already registered.";
        }
        $stmt_license->close();
    }
}

// If there are any validation errors, redirect back to profile page
if (!empty($errors)) {
    // We can't use $_SESSION['form_data'] here because the form is pre-filled from the database.
    // Instead, we set a general error message and let the form re-fetch data.
    set_session_message('error', 'Please correct the errors in the form.');
    $_SESSION['form_errors'] = $errors;
    redirect('profile.php');
    exit();
}

// --- Process Updates if no errors ---
$conn->begin_transaction();
$update_successful = false;

try {
    // 1. Update users table
    $stmt_user = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
    $stmt_user->bind_param("ssi", $username, $email, $user_id);
    if (!$stmt_user->execute()) {
        throw new Exception("Error updating users table: " . $stmt_user->error);
    }
    $stmt_user->close();

    // 2. Update role-specific table
    if ($role === 'patient') {
        $stmt_patient = $conn->prepare("UPDATE patients SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, phone_number = ?, address = ? WHERE user_id = ?");
        $stmt_patient->bind_param("ssssssi",
            $first_name,
            $last_name,
            $date_of_birth,
            $gender,
            $phone_number,
            $address,
            $user_id
        );
        if (!$stmt_patient->execute()) {
            throw new Exception("Error updating patients table: " . $stmt_patient->error);
        }
        $stmt_patient->close();

    } elseif ($role === 'doctor') {
        $stmt_doctor = $conn->prepare("UPDATE doctors SET first_name = ?, last_name = ?, specialization_id = ?, license_number = ?, phone_number = ?, office_address = ? WHERE user_id = ?");
        $stmt_doctor->bind_param("ssssssi",
            $first_name,
            $last_name,
            $specialization_id,
            $license_number,
            $phone_number,
            $office_address,
            $user_id
        );
        if (!$stmt_doctor->execute()) {
            throw new Exception("Error updating doctors table: " . $stmt_doctor->error);
        }
        $stmt_doctor->close();
    }

    $conn->commit();
    $update_successful = true;

} catch (Exception $e) {
    $conn->rollback();
    set_session_message('error', 'Profile update failed: ' . $e->getMessage());
    error_log("Profile Update Error: " . $e->getMessage());
    redirect('profile.php');
    exit();
}

if ($update_successful) {
    // Update session variables with new data
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    
    set_session_message('success', 'Your profile has been updated successfully.');
    
    // Redirect back to their dashboard
    if ($role === 'patient') redirect('patient_dashboard.php');
    elseif ($role === 'doctor') redirect('doctor_dashboard.php');
    else redirect('admin_dashboard.php');
    exit();
}

// Redirect back to the profile page as a final fallback
redirect('profile.php');
exit();

$conn->close();
?>