<?php
session_start();
require_once 'includes/admin_header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only admins can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    set_session_message('error', 'You must be logged in as an admin to view this page.');
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
$stmt = $conn->prepare("SELECT message_id, name, email, subject, message, sent_at FROM contact_messages WHERE message_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();
    }
    $stmt->close();
}

if (empty($message)) {
    set_session_message('error', 'Message not found.');
    redirect('admin_contact_messages.php');
    exit();
}

// Update the message status to 'read'
$stmt_update = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE message_id = ?");
$stmt_update->bind_param("i", $message_id);
$stmt_update->execute();
$stmt_update->close();

?>

<section class="admin-dashboard-section">
    <div class="container">
        <h2>View Message</h2>
        <a href="admin_contact_messages.php" class="btn btn-secondary mb-3">Back to Messages</a>

        <div class="card mb-4">
            <div class="card-header">
                From: **<?php echo htmlspecialchars($message['name']); ?>** <br>
                Email: <?php echo htmlspecialchars($message['email']); ?> <br>
                Subject: <?php echo htmlspecialchars($message['subject']); ?> <br>
                Date: <?php echo htmlspecialchars($message['sent_at']); ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Reply to this Message</h5>
                <form action="admin_message_process.php" method="POST">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($message['message_id']); ?>">
                    <input type="hidden" name="recipient_email" value="<?php echo htmlspecialchars($message['email']); ?>">
                    <input type="hidden" name="subject" value="Re: <?php echo htmlspecialchars($message['subject']); ?>">
                    
                    <div class="form-group">
                        <label for="reply_body">Your Reply:</label>
                        <textarea id="reply_body" name="reply_body" rows="6" class="form-control" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>

    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>