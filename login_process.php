<?php
session_start(); // MUST be the very first line
require_once 'includes/db_connect.php';
require_once 'includes/functions.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    // Store form data in session for re-population if there are errors
    $_SESSION['form_data'] = $_POST;

    // Basic server-side validation for login
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    if (!empty($errors)) {
        // Store errors in session to display on login page
        $_SESSION['form_errors'] = $errors;
        set_session_message('error', 'Please correct the errors in the form.');
        redirect('login.php');
        exit();
    }

    // Prepare a select statement to fetch user by email
    $stmt = $conn->prepare("SELECT user_id, username, email, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        // Handle prepare error gracefully
        set_session_message('error', 'Database query preparation failed: ' . $conn->error);
        redirect('login.php');
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); // Use get_result for direct fetching

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc(); // Fetch as associative array

        $user_id = $user['user_id'];
        $username = $user['username'];
        $db_email = $user['email'];
        $hashed_password = $user['password']; // This is the hashed password from the DB
        $role = $user['role'];

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            session_regenerate_id(true); // Regenerate session ID for security

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $db_email;
            $_SESSION['role'] = $role; // Set the user's role in session

            set_session_message('success', 'Login successful! Welcome, ' . htmlspecialchars($username) . '.');

            // Redirect based on user role
            switch ($role) {
                case 'patient':
                    redirect('patient_dashboard.php');
                    break;
                case 'doctor':
                    redirect('doctor_dashboard.php');
                    break;
                case 'admin':
                    redirect('admin_dashboard.php');
                    break;
                default:
                    set_session_message('error', 'Login successful, but your role is not recognized. Please contact support.');
                    redirect('index.php'); // Fallback
                    break;
            }
            exit();

        } else {
            // Invalid password
            set_session_message('error', 'Invalid email or password.');
            // Important: Do not specify if it was email or password for security reasons
            redirect('login.php');
            exit();
        }
    } else {
        // No user found with that email
        set_session_message('error', 'Invalid email or password.');
        redirect('login.php');
        exit();
    }

    $stmt->close();
} else {
    // If accessed directly without POST, redirect to login page
    redirect('login.php');
    exit();
}

// Close connection only if it was opened successfully
if (isset($conn) && $conn) {
    $conn->close();
}
?>