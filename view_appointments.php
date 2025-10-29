<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in patients can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    set_session_message('error', 'You must be logged in as a patient to view your appointments.');
    redirect('login.php');
    exit();
}

// Fetch the patient_id from the session's user_id
$patient_user_id = $_SESSION['user_id'];
$patient_id_stmt = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = ?");
$patient_id_stmt->bind_param("i", $patient_user_id);
$patient_id_stmt->execute();
$patient_id_result = $patient_id_stmt->get_result();
$patient_id_row = $patient_id_result->fetch_assoc();
$patient_id = $patient_id_row['patient_id'];
$patient_id_stmt->close();

// Fetch all appointments for this patient
$appointments = [];
$stmt = $conn->prepare("
    SELECT
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.reason,
        a.status,
        doc.first_name AS doctor_first_name,
        doc.last_name AS doctor_last_name,
        s.name AS specialization_name
    FROM appointments a
    LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
    LEFT JOIN specializations s ON doc.specialization_id = s.specialization_id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

if ($stmt) {
    $stmt->bind_param("i", $patient_id);
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
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                <td>Dr. <?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['specialization_name']); ?></td>
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