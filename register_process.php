<?php
session_start(); // Start session at the very beginning
require_once 'includes/db_connect.php'; // Database connection
require_once 'includes/functions.php'; // Custom functions like set_session_message and redirect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect common form data
    $role = trim($_POST['role'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Initialize an array to hold validation errors
    $errors = [];

    // Store form data in session for re-population if there are errors
    $_SESSION['form_data'] = $_POST;

    // --- Common User Validation ---
    if (empty($role)) {
        $errors['role'] = "Please select a role.";
    } elseif (!in_array($role, ['patient', 'doctor'])) {
        $errors['role'] = "Invalid role selected.";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters long.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Check if username or email already exists in the users table
    if (empty($errors['username'])) { // Only check if username is valid so far
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['username'] = "Username already exists.";
        }
        $stmt->close();
    }

    if (empty($errors['email'])) { // Only check if email is valid so far
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email already registered.";
        }
        $stmt->close();
    }

    // --- Role-Specific Validation ---
    if ($role === 'patient') {
        $p_first_name = trim($_POST['p_first_name'] ?? '');
        $p_last_name = trim($_POST['p_last_name'] ?? '');
        $p_dob = trim($_POST['p_dob'] ?? '');
        $p_gender = trim($_POST['p_gender'] ?? '');
        $p_phone = trim($_POST['p_phone'] ?? '');
        $p_address = trim($_POST['p_address'] ?? '');

        if (empty($p_first_name)) $errors['p_first_name'] = "Patient's first name is required.";
        if (empty($p_last_name)) $errors['p_last_name'] = "Patient's last name is required.";
        // Add more patient-specific validations as needed (e.g., date format, phone number format)
    } elseif ($role === 'doctor') {
        $d_first_name = trim($_POST['d_first_name'] ?? '');
        $d_last_name = trim($_POST['d_last_name'] ?? '');
        $d_specialization_id = $_POST['d_specialization'] ?? ''; // This is ID from select
        $d_license_number = trim($_POST['d_license'] ?? '');
        $d_phone = trim($_POST['d_phone'] ?? '');
        $d_office_address = trim($_POST['d_office_address'] ?? '');

        if (empty($d_first_name)) $errors['d_first_name'] = "Doctor's first name is required.";
        if (empty($d_last_name)) $errors['d_last_name'] = "Doctor's last name is required.";
        if (empty($d_specialization_id)) $errors['d_specialization'] = "Specialization is required.";
        if (empty($d_license_number)) $errors['d_license'] = "License number is required.";
        // Add more doctor-specific validations (e.g., license number format, phone number format)

        // Check if license number already exists
        if (empty($errors['d_license'])) {
            $stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE license_number = ?");
            $stmt->bind_param("s", $d_license_number);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors['d_license'] = "License number already registered.";
            }
            $stmt->close();
        }
    }

    // If there are any validation errors, store them and redirect back
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        set_session_message('error', 'Please correct the errors in the form.');
        redirect('register.php');
        exit();
    }

    // --- Process Registration if no errors ---
    // Start a transaction to ensure both inserts succeed or fail together
    $conn->begin_transaction();
    $registration_successful = false;

    try {
        // 1. Insert into users table
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt_user = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if (!$stmt_user) {
            throw new Exception("Error preparing user statement: " . $conn->error);
        }
        $stmt_user->bind_param("ssss", $username, $email, $hashed_password, $role);
        if (!$stmt_user->execute()) {
            throw new Exception("Error executing user insertion: " . $stmt_user->error);
        }
        $user_id = $conn->insert_id; // Get the ID of the newly inserted user
        $stmt_user->close();

        // 2. Insert into role-specific table (patients or doctors)
        if ($role === 'patient') {
            $stmt_patient = $conn->prepare("INSERT INTO patients (user_id, first_name, last_name, date_of_birth, gender, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt_patient) {
                throw new Exception("Error preparing patient statement: " . $conn->error);
            }
            // Assign to variables first for bind_param to work correctly (pass by reference)
            $p_dob = $_POST['p_dob'] ?? NULL; // Use NULL for empty date, if allowed in DB
            $p_gender = $_POST['p_gender'] ?? NULL;
            $p_phone = $_POST['p_phone'] ?? NULL;
            $p_address = $_POST['p_address'] ?? NULL;

            $stmt_patient->bind_param("issssss",
                $user_id,
                $p_first_name,
                $p_last_name,
                $p_dob,
                $p_gender,
                $p_phone,
                $p_address
            );
            if (!$stmt_patient->execute()) {
                throw new Exception("Error executing patient insertion: " . $stmt_patient->error);
            }
            $stmt_patient->close();

        } elseif ($role === 'doctor') {
            $stmt_doctor = $conn->prepare("INSERT INTO doctors (user_id, first_name, last_name, specialization_id, license_number, phone_number, office_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt_doctor) {
                throw new Exception("Error preparing doctor statement: " . $conn->error);
            }
            // Assign to variables first for bind_param to work correctly (pass by reference)
            $d_phone = $_POST['d_phone'] ?? NULL;
            $d_office_address = $_POST['d_office_address'] ?? NULL;

            $stmt_doctor->bind_param("issssss",
                $user_id,
                $d_first_name,
                $d_last_name,
                $d_specialization_id, // This should be an int from the select
                $d_license_number,
                $d_phone,
                $d_office_address
            );
            if (!$stmt_doctor->execute()) {
                throw new Exception("Error executing doctor insertion: " . $stmt_doctor->error);
            }
            $stmt_doctor->close();
        }

        $conn->commit(); // Commit the transaction if all inserts succeed
        $registration_successful = true;

    } catch (Exception $e) {
        $conn->rollback(); // Rollback on any error
        set_session_message('error', 'Registration failed: ' . $e->getMessage());
        error_log("Registration Error: " . $e->getMessage()); // Log error for debugging
        redirect('register.php');
        exit();
    }

    if ($registration_successful) {
        set_session_message('success', 'Registration successful! You can now log in.');
        redirect('login.php'); // Redirect to login page on success
        exit();
    }

} else {
    // If accessed directly without POST request, redirect to register page
    redirect('register.php');
    exit();
}

$conn->close(); // Close database connection
?>