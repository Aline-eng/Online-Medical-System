<?php
session_start();
require_once 'includes/admin_header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Fetch all users
$users = [];
$sql = "SELECT user_id, email, role, is_active FROM users ORDER BY user_id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<section class="admin-dashboard-section">
    <div class="container">
        <h2>Manage Users</h2>
        <p class="lead">View and manage all user accounts in the system.</p>

        <?php echo get_session_message('success'); ?>
        <?php echo get_session_message('error'); ?>

        <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <a href="admin_user_process.php?action=disable&id=<?php echo htmlspecialchars($user['user_id']); ?>"
                                           class="btn btn-warning btn-sm"
                                           onclick="return confirm('Are you sure you want to disable this user?');">Disable</a>
                                    <?php else: ?>
                                        <a href="admin_user_process.php?action=enable&id=<?php echo htmlspecialchars($user['user_id']); ?>"
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('Are you sure you want to enable this user?');">Enable</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No users found.</p>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>