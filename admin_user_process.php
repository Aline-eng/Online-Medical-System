<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only admins can use this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$action = $_GET['action'] ?? '';
$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    set_session_message('error', 'Invalid user ID.');
    redirect('admin_users.php');
    exit();
}

$is_active_value = null;
$message = '';
$redirect_to = 'admin_users.php';

switch ($action) {
    case 'enable':
        $is_active_value = 1;
        $message = 'User account enabled successfully!';
        break;
    case 'disable':
        // Prevent disabling the admin's own account
        if ($user_id == $_SESSION['user_id']) {
            set_session_message('error', 'You cannot disable your own account.');
            redirect($redirect_to);
            exit();
        }
        $is_active_value = 0;
        $message = 'User account disabled successfully!';
        break;
    default:
        set_session_message('error', 'Invalid action specified.');
        redirect($redirect_to);
        exit();
}

// Update the user's status in the database
$stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("ii", $is_active_value, $user_id);
    if ($stmt->execute()) {
        set_session_message('success', $message);
    } else {
        set_session_message('error', 'Error updating user status: ' . $stmt->error);
    }
    $stmt->close();
} else {
    set_session_message('error', 'Database query preparation failed: ' . $conn->error);
}

$conn->close();
redirect($redirect_to);
exit();