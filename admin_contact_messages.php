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

// Fetch all contact messages
$messages = [];
$sql = "SELECT message_id, name, email, subject, message, sent_at, status FROM contact_messages ORDER BY sent_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<section class="admin-dashboard-section">
    <div class="container">
        <h2>Contact Messages</h2>
        <p class="lead">View all messages sent via the contact form.</p>

        <?php if (!empty($messages)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>From</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo htmlspecialchars($message['sent_at']); ?></td>
                                <td><span class="badge badge-<?php echo ($message['status'] == 'unread') ? 'danger' : 'success'; ?>"><?php echo htmlspecialchars(ucfirst($message['status'])); ?></span></td>
                                <td>
                                    <a href="admin_view_message.php?id=<?php echo htmlspecialchars($message['message_id']); ?>" class="btn btn-primary btn-sm">View</a>
                                    <a href="admin_download_message.php?id=<?php echo htmlspecialchars($message['message_id']); ?>" class="btn btn-secondary btn-sm">Download</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No contact messages found.</p>
        <?php endif; ?>

    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>