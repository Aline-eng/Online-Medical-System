register.php

<?php
session_start(); // Ensure session is started for messages and form data
require_once 'includes/header.php'; // Includes session_start() and functions.php
?>
<section class="auth-container">
    <div class="auth-image-section" style="background-image: url('assets/images/register-image.webp');"></div>
<div class="auth-form-section">
    <div class="auth-form">
        <h2>Register for an Account</h2>
        <?php
        // display_session_message() is called in header.php, but if you need to display
        // messages specific to this page AFTER the header, you can call it again here.
        // For simplicity, we'll rely on the call in header.php for initial page load messages.
        ?>

        <?php
        // Retrieve and clear form data and errors from session if redirected back due to validation issues
        $form_data = $_SESSION['form_data'] ?? [];
        $form_errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_data']);
        unset($_SESSION['form_errors']);
        ?>

        <form action="register_process.php" method="POST" class="auth-form" id="registrationForm">
            <div class="form-group">
                <label for="role">Register As:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="patient" <?php echo (isset($form_data['role']) && $form_data['role'] == 'patient') ? 'selected' : ''; ?>>Patient</option>
                    <option value="doctor" <?php echo (isset($form_data['role']) && $form_data['role'] == 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                </select>
                <span class="error-message"><?php echo $form_errors['role'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required autocomplete="username">
                <span class="error-message"><?php echo $form_errors['username'] ?? ''; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required autocomplete="email">
                <span class="error-message"><?php echo $form_errors['email'] ?? ''; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                <span class="error-message"><?php echo $form_errors['password'] ?? ''; ?></span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                <span class="error-message"><?php echo $form_errors['confirm_password'] ?? ''; ?></span>
            </div>

            <div id="patient_fields" style="display: none;">
                <h3>Patient Details</h3>
                <div class="form-group">
                    <label for="p_first_name">First Name:</label>
                    <input type="text" id="p_first_name" name="p_first_name" value="<?php echo htmlspecialchars($form_data['p_first_name'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['p_first_name'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="p_last_name">Last Name:</label>
                    <input type="text" id="p_last_name" name="p_last_name" value="<?php echo htmlspecialchars($form_data['p_last_name'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['p_last_name'] ?? ''; ?></span>
                </div>
                 <div class="form-group">
                    <label for="p_dob">Date of Birth:</label>
                    <input type="date" id="p_dob" name="p_dob" value="<?php echo htmlspecialchars($form_data['p_dob'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['p_dob'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="p_gender">Gender:</label>
                    <select id="p_gender" name="p_gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (isset($form_data['p_gender']) && $form_data['p_gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($form_data['p_gender']) && $form_data['p_gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($form_data['p_gender']) && $form_data['p_gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <span class="error-message"><?php echo $form_errors['p_gender'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="p_phone">Phone Number:</label>
                    <input type="tel" id="p_phone" name="p_phone" value="<?php echo htmlspecialchars($form_data['p_phone'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['p_phone'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="p_address">Address:</label>
                    <input type="text" id="p_address" name="p_address" value="<?php echo htmlspecialchars($form_data['p_address'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['p_address'] ?? ''; ?></span>
                </div>
            </div>

            <div id="doctor_fields" style="display: none;">
                <h3>Doctor Details</h3>
                <div class="form-group">
                    <label for="d_first_name">First Name:</label>
                    <input type="text" id="d_first_name" name="d_first_name" value="<?php echo htmlspecialchars($form_data['d_first_name'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['d_first_name'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="d_last_name">Last Name:</label>
                    <input type="text" id="d_last_name" name="d_last_name" value="<?php echo htmlspecialchars($form_data['d_last_name'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['d_last_name'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="d_specialization">Specialization:</label>
                    <select id="d_specialization" name="d_specialization" class="form-control">
                        <option value="">Select Specialization</option>
                        <?php
                        // Dynamically load specializations from the database
                        require_once 'includes/db_connect.php'; // Re-connect if not already
                        $sql = "SELECT specialization_id, name FROM specializations ORDER BY name ASC";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $selected = (isset($form_data['d_specialization']) && $form_data['d_specialization'] == $row['specialization_id']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['specialization_id']) . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                            }
                        }
                        $conn->close(); // Close connection if opened here
                        ?>
                    </select>
                    <span class="error-message"><?php echo $form_errors['d_specialization'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="d_license">License Number:</label>
                    <input type="text" id="d_license" name="d_license" value="<?php echo htmlspecialchars($form_data['d_license'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['d_license'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="d_phone">Phone Number:</label>
                    <input type="tel" id="d_phone" name="d_phone" value="<?php echo htmlspecialchars($form_data['d_phone'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['d_phone'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="d_office_address">Office Address:</label>
                    <input type="text" id="d_office_address" name="d_office_address" value="<?php echo htmlspecialchars($form_data['d_office_address'] ?? ''); ?>">
                    <span class="error-message"><?php echo $form_errors['d_office_address'] ?? ''; ?></span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
            <p class="form-link">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const patientFields = document.getElementById('patient_fields');
    const doctorFields = document.getElementById('doctor_fields');

    function toggleFields() {
        const selectedRole = roleSelect.value;
        if (selectedRole === 'patient') {
            patientFields.style.display = 'block';
            doctorFields.style.display = 'none';
            // Make patient fields required
            patientFields.querySelectorAll('input, select').forEach(field => field.setAttribute('required', 'required'));
            doctorFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
        } else if (selectedRole === 'doctor') {
            patientFields.style.display = 'none';
            doctorFields.style.display = 'block';
            // Make doctor fields required
            doctorFields.querySelectorAll('input, select').forEach(field => field.setAttribute('required', 'required'));
            patientFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
        } else {
            patientFields.style.display = 'none';
            doctorFields.style.display = 'none';
            // Remove required from all specific fields if no role selected
            patientFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
            doctorFields.querySelectorAll('input, select').forEach(field => field.removeAttribute('required'));
        }
    }

    // Initial call to set correct fields based on pre-filled data (if any)
    toggleFields();

    // Listen for changes on the role select
    roleSelect.addEventListener('change', toggleFields);
});
</script>

<?php include_once 'includes/footer.php'; ?>