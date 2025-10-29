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

$message_id = $_GET['id'] ?? null;
if (!$message_id) {
    set_session_message('error', 'No message ID specified.');
    redirect('admin_contact_messages.php');
    exit();
}

// Fetch the specific message from the database
$message = [];
$stmt = $conn->prepare("SELECT name, email, subject, message, sent_at FROM contact_messages WHERE message_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();

if (empty($message)) {
    set_session_message('error', 'Message not found.');
    redirect('admin_contact_messages.php');
    exit();
}

// Generate file content
$file_content = "--- Contact Message Details ---\n\n";
$file_content .= "Message ID: " . $message_id . "\n";
$file_content .= "Name: " . $message['name'] . "\n";
$file_content .= "Email: " . $message['email'] . "\n";
$file_content .= "Subject: " . $message['subject'] . "\n";
$file_content .= "Sent At: " . $message['sent_at'] . "\n\n";
$file_content .= "--- Message Body ---\n\n";
$file_content .= $message['message'] . "\n";

// Set headers for file download
$filename = "message_" . $message_id . ".txt";
header('Content-Description: File Transfer');
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($file_content));
echo $file_content;
exit;
?>