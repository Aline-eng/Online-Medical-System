<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in doctors can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    set_session_message('error', 'You must be logged in as a doctor to view your appointments.');
    redirect('login.php');
    exit();
}

// Fetch the doctor_id from the session's user_id
$doctor_user_id = $_SESSION['user_id'];
$doctor_id_stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
$doctor_id_stmt->bind_param("i", $doctor_user_id);
$doctor_id_stmt->execute();
$doctor_id_result = $doctor_id_stmt->get_result();
$doctor_id_row = $doctor_id_result->fetch_assoc();
$doctor_id = $doctor_id_row['doctor_id'];
$doctor_id_stmt->close();

// Fetch all appointments for this doctor
$appointments = [];
$stmt = $conn->prepare("
    SELECT
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.reason,
        a.status,
        pat.first_name AS patient_first_name,
        pat.last_name AS patient_last_name
    FROM appointments a
    LEFT JOIN patients pat ON a.patient_id = pat.patient_id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
    $stmt->close();
} else {
    set_session_message('error', 'Failed to retrieve appointments: ' . $conn->error);
}
?>

<section class="appointments-section">
    <div class="container">
        <h2>Your Appointments</h2>
        
        <?php if (!empty($appointments)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                <td><span class="badge <?php 
                                    if ($appointment['status'] === 'Scheduled') echo 'badge-primary';
                                    elseif ($appointment['status'] === 'Completed') echo 'badge-success';
                                    else echo 'badge-danger';
                                ?>">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">You have no upcoming or past appointments.</p>
        <?php endif; ?>
        
    </div>
</section>

<?php 
$conn->close();
include_once 'includes/footer.php'; 
?>