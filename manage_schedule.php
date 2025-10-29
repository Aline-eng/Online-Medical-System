<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in doctors can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    set_session_message('error', 'You must be logged in as a doctor to manage your schedule.');
    redirect('login.php');
    exit();
}

$doctor_user_id = $_SESSION['user_id'];
$doctor_id_stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
$doctor_id_stmt->bind_param("i", $doctor_user_id);
$doctor_id_stmt->execute();
$doctor_id_result = $doctor_id_stmt->get_result();
$doctor_id_row = $doctor_id_result->fetch_assoc();
$doctor_id = $doctor_id_row['doctor_id'];
$doctor_id_stmt->close();

// Fetch all available schedules for this doctor
$schedules = [];
$stmt = $conn->prepare("
    SELECT
        schedule_id,
        available_date,
        available_time
    FROM doctor_schedules
    WHERE doctor_id = ? AND available_date >= CURDATE()
    ORDER BY available_date ASC, available_time ASC
");
if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $schedules[] = $row;
        }
    }
    $stmt->close();
}

// Retrieve form data and errors from session for re-population if redirected back
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>

<section class="schedule-section">
    <div class="container">
        <h2>Manage Your Schedule</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Available Time Slot</h5>
                <form action="schedule_process.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">

                    <div class="form-group">
                        <label for="available_date">Date:</label>
                        <input type="date" id="available_date" name="available_date" class="form-control"
                               value="<?php echo htmlspecialchars($form_data['available_date'] ?? ''); ?>" required>
                        <span class="error-message"><?php echo $form_errors['available_date'] ?? ''; ?></span>
                    </div>

                    <div class="form-group">
                        <label for="available_time">Time:</label>
                        <input type="time" id="available_time" name="available_time" class="form-control"
                               value="<?php echo htmlspecialchars($form_data['available_time'] ?? ''); ?>" required>
                        <span class="error-message"><?php echo $form_errors['available_time'] ?? ''; ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Time Slot</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upcoming Available Time Slots</h5>
                <?php if (!empty($schedules)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($schedule['available_date']); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['available_time']); ?></td>
                                        <td>
                                            <form action="schedule_process.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule['schedule_id']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this time slot?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">You have no upcoming available time slots.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>