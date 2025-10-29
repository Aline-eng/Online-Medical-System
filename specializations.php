<?php
session_start();
require_once 'includes/admin_header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only admins can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    set_session_message('error', 'You do not have permission to access this page.');
    redirect('login.php');
    exit();
}

// Fetch all specializations from the database
$specializations = [];
$sql = "SELECT specialization_id, name FROM specializations ORDER BY name ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $specializations[] = $row;
    }
}
?>

<section class="admin-section">
    <div class="container">
        <h2>Manage Specializations</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Specialization</h5>
                <form action="specialization_process.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="specialization_name">Specialization Name:</label>
                        <input type="text" id="specialization_name" name="specialization_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Specialization</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Specializations</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($specializations)): ?>
                            <?php foreach ($specializations as $specialization): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($specialization['specialization_id']); ?></td>
                                    <td><?php echo htmlspecialchars($specialization['name']); ?></td>
                                    <td>
                                        <form action="specialization_process.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="specialization_id" value="<?php echo htmlspecialchars($specialization['specialization_id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this specialization? This cannot be undone.');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No specializations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<?php
$conn->close(); // Close the database connection
include_once 'includes/footer.php';
?>