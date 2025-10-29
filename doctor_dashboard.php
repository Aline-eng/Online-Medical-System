<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Access control: Only doctors can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    set_session_message('error', 'You do not have permission to access the doctor dashboard.');
    redirect('login.php'); // Redirect unauthenticated or unauthorized users
    exit();
}

// Fetch doctor-specific data
require_once 'includes/db_connect.php';
$doctor_data = [];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT d.first_name, d.last_name, s.name AS specialization_name, d.license_number, d.phone_number, d.office_address
    FROM doctors d
    LEFT JOIN specializations s ON d.specialization_id = s.specialization_id
    WHERE d.user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $doctor_data = $result->fetch_assoc();
    }
    $stmt->close();
} else {
    set_session_message('error', 'Failed to retrieve doctor data.');
}
$conn->close();
?>

<section class="dashboard-section">
    <div class="container">
        <h2>Doctor Dashboard</h2>
        <p>Welcome, Dr. <?php echo htmlspecialchars($doctor_data['last_name'] ?? $_SESSION['username']); ?>!</p>

        <h3>Your Details</h3>
        <ul class="list-group mb-4">
            <li class="list-group-item">Username: <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Email: <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></li>
            <li class="list-group-item">First Name: <?php echo htmlspecialchars($doctor_data['first_name'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Last Name: <?php echo htmlspecialchars($doctor_data['last_name'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Specialization: <?php echo htmlspecialchars($doctor_data['specialization_name'] ?? 'N/A'); ?></li>
            <li class="list-group-item">License No.: <?php echo htmlspecialchars($doctor_data['license_number'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Phone: <?php echo htmlspecialchars($doctor_data['phone_number'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Office Address: <?php echo htmlspecialchars($doctor_data['office_address'] ?? 'N/A'); ?></li>
        </ul>

        <h3>Quick Actions</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="doctor_appointments.php" class="btn btn-primary btn-block">View New Appointments</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="manage_schedule.php" class="btn btn-secondary btn-block">Manage Schedule</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="medical_records.php" class="btn btn-info btn-block">View Patient Records</a>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>