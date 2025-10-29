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

// Fetch all appointments
$appointments = [];
$sql = "
    SELECT
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name,
        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN patients p ON a.patient_id = p.patient_id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>

<section class="admin-dashboard-section">
    <div class="container">
        <h2>All Appointments</h2>
        <p class="lead">Overview of all booked appointments in the system.</p>

        <?php if (!empty($appointments)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                                <td>Dr. <?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td><span class="badge badge-<?php echo ($appointment['status'] == 'pending') ? 'warning' : 'success'; ?>"><?php echo htmlspecialchars(ucfirst($appointment['status'])); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No appointments found in the system.</p>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>