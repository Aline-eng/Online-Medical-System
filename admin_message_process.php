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

if ($action === 'reply') {
    $message_id = $_POST['message_id'] ?? null;
    $recipient_email = $_POST['recipient_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $reply_body = $_POST['reply_body'] ?? '';

    if (empty($message_id) || empty($recipient_email) || empty($subject) || empty($reply_body)) {
        set_session_message('error', 'Missing required fields for reply.');
        redirect('admin_view_message.php?id=' . $message_id);
        exit();
    }

    // Prepare email headers
    $headers = 'From: noreply@onlinemedicalsystem.com' . "\r\n" .
               'Reply-To: noreply@onlinemedicalsystem.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    // Attempt to send the email
    if (mail($recipient_email, $subject, $reply_body, $headers)) {
        // Optionally, update the status of the message in the database
        $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE message_id = ?");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $stmt->close();
        
        set_session_message('success', 'Reply sent successfully!');
    } else {
        set_session_message('error', 'Failed to send reply. Mail server error.');
    }

    redirect('admin_view_message.php?id=' . $message_id);
    exit();
} else {
    set_session_message('error', 'Invalid action specified.');
    redirect('admin_contact_messages.php');
    exit();
}

$conn->close();