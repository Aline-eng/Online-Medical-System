<?php
session_start();
require_once 'includes/admin_header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only admins can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('login.php');
    exit();
}

// Fetch key statistics
$stats = [
    'patients' => 0,
    'doctors' => 0,
    'appointments' => 0,
    'messages' => 0
];

// Query for total number of patients
$sql_patients = "SELECT COUNT(*) AS total_patients FROM patients";
$result_patients = $conn->query($sql_patients);
if ($result_patients && $result_patients->num_rows > 0) {
    $row = $result_patients->fetch_assoc();
    $stats['patients'] = $row['total_patients'];
}

// Query for total number of doctors
$sql_doctors = "SELECT COUNT(*) AS total_doctors FROM doctors";
$result_doctors = $conn->query($sql_doctors);
if ($result_doctors && $result_doctors->num_rows > 0) {
    $row = $result_doctors->fetch_assoc();
    $stats['doctors'] = $row['total_doctors'];
}

// Query for total number of appointments
$sql_appointments = "SELECT COUNT(*) AS total_appointments FROM appointments";
$result_appointments = $conn->query($sql_appointments);
if ($result_appointments && $result_appointments->num_rows > 0) {
    $row = $result_appointments->fetch_assoc();
    $stats['appointments'] = $row['total_appointments'];
}

// Query for total number of contact messages
$sql_messages = "SELECT COUNT(*) AS total_messages FROM contact_messages";
$result_messages = $conn->query($sql_messages);
if ($result_messages && $result_messages->num_rows > 0) {
    $row = $result_messages->fetch_assoc();
    $stats['messages'] = $row['total_messages'];
}
?>

<section class="admin-dashboard-section">
    <div class="container">
        <h2 style="margin-top: 20px;">Admin Dashboard</h2>
        <p class="lead">Welcome, Admin! This is your control center for the system.</p>

        <?php
        // Display a success message if it exists, without using a function.
        if (isset($_SESSION['messages']['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo implode('<br>', $_SESSION['messages']['success']);
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            echo '</div>';
            unset($_SESSION['messages']['success']);
        }
        ?>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="card-title h5">Patients</div>
                        <div class="card-text display-4"><?php echo htmlspecialchars($stats['patients']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="card-title h5">Doctors</div>
                        <div class="card-text display-4"><?php echo htmlspecialchars($stats['doctors']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="card-title h5">Appointments</div>
                        <div class="card-text display-4"><?php echo htmlspecialchars($stats['appointments']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="card-title h5">Messages</div>
                        <div class="card-text display-4"><?php echo htmlspecialchars($stats['messages']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <a href="admin_contact_messages.php" class="btn btn-info btn-block" style="background-color: green;">View All Contact Messages</a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="admin_appointments.php" class="btn btn-primary btn-block">View All Appointments</a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="specializations.php" class="btn btn-secondary btn-block">Manage Specializations</a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="admin_users.php" class="btn btn-warning btn-block">Manage Users</a>
            </div>
        </div>

    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>