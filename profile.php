<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in users can view this page
if (!isset($_SESSION['user_id'])) {
    set_session_message('error', 'You must be logged in to view your profile.');
    redirect('login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch common user data (from the users table)
$user_data = [];
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_data_result = $stmt_user->get_result();
if ($user_data_result->num_rows > 0) {
    $user_data = $user_data_result->fetch_assoc();
}
$stmt_user->close();

// Fetch role-specific data
$role_data = [];
if ($role === 'patient') {
    $stmt_role = $conn->prepare("SELECT * FROM patients WHERE user_id = ?");
} elseif ($role === 'doctor') {
    $stmt_role = $conn->prepare("SELECT d.*, s.name AS specialization_name FROM doctors d LEFT JOIN specializations s ON d.specialization_id = s.specialization_id WHERE d.user_id = ?");
} elseif ($role === 'admin') {
    // Admins don't have a separate table, so no extra data to fetch
}
if (isset($stmt_role)) {
    $stmt_role->bind_param("i", $user_id);
    $stmt_role->execute();
    $role_data_result = $stmt_role->get_result();
    if ($role_data_result->num_rows > 0) {
        $role_data = $role_data_result->fetch_assoc();
    }
    $stmt_role->close();
}

// Fetch all specializations for the doctor dropdown (if role is doctor)
$specializations = [];
if ($role === 'doctor') {
    $sql = "SELECT specialization_id, name FROM specializations ORDER BY name ASC";
    $result_spec = $conn->query($sql);
    if ($result_spec && $result_spec->num_rows > 0) {
        while ($row = $result_spec->fetch_assoc()) {
            $specializations[] = $row;
        }
    }
}
?>

<section class="profile-section">
    <div class="container">
        <h2>Your Profile</h2>
        <p class="lead">View and update your personal information.</p>
        
        <form action="profile_process.php" method="POST" class="auth-form" id="profileForm">
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
            
            <h3>Account Information</h3>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required autocomplete="email">
            </div>
            
            <?php if ($role === 'patient'): ?>
            <h3>Patient Details</h3>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($role_data['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($role_data['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($role_data['date_of_birth'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (isset($role_data['gender']) && $role_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($role_data['gender']) && $role_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($role_data['gender']) && $role_data['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($role_data['phone_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($role_data['address'] ?? ''); ?>">
            </div>
            <?php elseif ($role === 'doctor'): ?>
            <h3>Doctor Details</h3>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($role_data['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($role_data['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="specialization_id">Specialization:</label>
                <select id="specialization_id" name="specialization_id" class="form-control" required>
                    <option value="">Select Specialization</option>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?php echo htmlspecialchars($spec['specialization_id']); ?>" <?php echo (isset($role_data['specialization_id']) && $role_data['specialization_id'] == $spec['specialization_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($spec['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="license_number">License Number:</label>
                <input type="text" id="license_number" name="license_number" class="form-control" value="<?php echo htmlspecialchars($role_data['license_number'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($role_data['phone_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="office_address">Office Address:</label>
                <input type="text" id="office_address" name="office_address" class="form-control" value="<?php echo htmlspecialchars($role_data['office_address'] ?? ''); ?>">
            </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="<?php 
                if ($role === 'patient') echo 'patient_dashboard.php';
                elseif ($role === 'doctor') echo 'doctor_dashboard.php';
                else echo 'admin_dashboard.php';
            ?>" class="btn btn-secondary">Cancel</a>
        </form>

    </div>
</section>

<?php 
$conn->close();
include_once 'includes/footer.php'; 
?>