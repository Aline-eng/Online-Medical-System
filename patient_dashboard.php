<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Access control: Only patients can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    set_session_message('error', 'You do not have permission to access the patient dashboard.');
    redirect('login.php'); // Redirect unauthenticated or unauthorized users
    exit();
}

// Fetch patient-specific data
require_once 'includes/db_connect.php';
$patient_data = [];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.first_name, p.last_name, p.date_of_birth, p.gender, p.phone_number, p.address FROM patients p WHERE p.user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $patient_data = $result->fetch_assoc();
    }
    $stmt->close();
} else {
    set_session_message('error', 'Failed to retrieve patient data.');
}
$conn->close();
?>

<section class="dashboard-section">
    <div class="container">
        <h2>Patient Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($patient_data['first_name'] ?? $_SESSION['username']); ?>!</p>

        <h3>Your Details</h3>
        <ul class="list-group mb-4">
            <li class="list-group-item">Username: <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Email: <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></li>
            <li class="list-group-item">First Name: <?php echo htmlspecialchars($patient_data['first_name'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Last Name: <?php echo htmlspecialchars($patient_data['last_name'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Date of Birth: <?php echo htmlspecialchars($patient_data['date_of_birth'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Gender: <?php echo htmlspecialchars($patient_data['gender'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Phone: <?php echo htmlspecialchars($patient_data['phone_number'] ?? 'N/A'); ?></li>
            <li class="list-group-item">Address: <?php echo htmlspecialchars($patient_data['address'] ?? 'N/A'); ?></li>
        </ul>

        <h3>Quick Actions</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="book_appointment.php" class="btn btn-primary btn-block">Book New Appointment</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="view_appointments.php" class="btn btn-secondary btn-block">View Appointments</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="patient_medical_records.php" class="btn btn-info btn-block">View Medical Records</a>
            </div>
        </div>

    </div>
</section>

<?php include_once 'includes/footer.php'; ?>