<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in patients can book appointments
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    // Correctly redirect with an error message
    set_session_message('error', 'You must be logged in as a patient to book an appointment.');
    redirect('login.php');
    exit();
}

// Fetch all available doctors and their specializations
$doctors = [];
$sql = "
    SELECT
        d.doctor_id,
        u.full_name,
        s.name AS specialization_name
    FROM doctors d
    LEFT JOIN users u ON d.user_id = u.user_id
    LEFT JOIN specializations s ON d.specialization_id = s.specialization_id
    ORDER BY u.full_name ASC
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
} else {
    // If no doctors, show a message
    set_session_message('info', 'No doctors are currently available for appointments.');
}

// Check for a pre-selected doctor from the `doctors.php` page
$selected_doctor_id = $_GET['doctor_id'] ?? null;

// Retrieve form data and errors from session for re-population if redirected back
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>

<div class="container my-5">
    <div class="booking-form-container">
        <h2>Book a New Appointment</h2>
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="appointment_process.php" method="POST" class="auth-form" id="appointmentForm">
            <!-- This hidden input will hold the patient's ID -->
            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

            <div class="form-group">
                <label for="doctor">Select a Doctor:</label>
                <select id="doctor" name="doctor_id" class="form-control" required>
                    <option value="">-- Please Select a Doctor --</option>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo htmlspecialchars($doctor['doctor_id']); ?>"
                                <?php echo ($selected_doctor_id == $doctor['doctor_id']) ? 'selected' : ''; ?>>
                                Dr. <?php echo htmlspecialchars($doctor['full_name']); ?> (<?php echo htmlspecialchars($doctor['specialization_name'] ?? 'N/A'); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="error-message"><?php echo $form_errors['doctor_id'] ?? ''; ?></span>
            </div>

            <!-- Dynamic date and time selection containers -->
            <div id="dynamic-booking-fields" style="display: none;">
                <div class="form-group mt-4">
                    <label>Available Dates:</label>
                    <div id="date-picker-placeholder" class="d-flex flex-wrap gap-2">
                        <!-- Date buttons will be loaded here dynamically -->
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label>Available Time Slots:</label>
                    <div id="time-slots-placeholder" class="d-flex flex-wrap gap-2">
                        <!-- Time buttons will be loaded here dynamically -->
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <label for="reason">Reason for Appointment:</label>
                <textarea id="reason" name="reason" rows="4" class="form-control" required><?php echo htmlspecialchars($form_data['reason'] ?? ''); ?></textarea>
                <span class="error-message"><?php echo $form_errors['reason'] ?? ''; ?></span>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block mt-4" disabled>Book Appointment</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor');
    const dynamicFields = document.getElementById('dynamic-booking-fields');
    const datePickerPlaceholder = document.getElementById('date-picker-placeholder');
    const timeSlotsPlaceholder = document.getElementById('time-slots-placeholder');
    const confirmButton = document.querySelector('button[type="submit"]');

    function showLoading(element) {
        element.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    }

    function showError(element, message) {
        element.innerHTML = `<div class="alert alert-danger" role="alert">${message}</div>`;
    }

    // This function fetches available dates for the selected doctor
    async function fetchAvailableDates(doctorId) {
        if (!doctorId) {
            dynamicFields.style.display = 'none';
            return;
        }
        
        dynamicFields.style.display = 'block';
        showLoading(datePickerPlaceholder);
        timeSlotsPlaceholder.innerHTML = '';
        confirmButton.disabled = true;

        try {
            // Note: The path is relative to the current file
            const response = await fetch(`get_doctor_availability.php?doctor_id=${doctorId}`);
            const data = await response.json();

            datePickerPlaceholder.innerHTML = '';
            if (data.success && data.dates.length > 0) {
                data.dates.forEach(date => {
                    const dateBtn = document.createElement('button');
                    dateBtn.type = 'button';
                    dateBtn.classList.add('btn', 'btn-outline-primary', 'm-1');
                    dateBtn.textContent = date;
                    dateBtn.dataset.date = date;
                    dateBtn.onclick = () => fetchAvailableTimeSlots(doctorId, date);
                    datePickerPlaceholder.appendChild(dateBtn);
                });
            } else {
                datePickerPlaceholder.innerHTML = '<div class="alert alert-warning">No available dates found.</div>';
            }
        } catch (error) {
            console.error('Error fetching dates:', error);
            showError(datePickerPlaceholder, 'Failed to fetch dates. Please try again.');
        }
    }

    // This function fetches available time slots for a specific date
    async function fetchAvailableTimeSlots(doctorId, date) {
        showLoading(timeSlotsPlaceholder);

        // Reset all date button styles and highlight the selected one
        document.querySelectorAll('#date-picker-placeholder .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
        });
        document.querySelector(`[data-date="${date}"]`).classList.add('active', 'btn-primary');
        
        // Remove old hidden inputs
        if (document.querySelector('input[name="appointment_date"]')) {
            document.querySelector('input[name="appointment_date"]').remove();
        }
        if (document.querySelector('input[name="appointment_time"]')) {
            document.querySelector('input[name="appointment_time"]').remove();
        }

        try {
            // Note: The path is relative to the current file
            const response = await fetch(`get_doctor_availability.php?doctor_id=${doctorId}&date=${date}`);
            const data = await response.json();

            timeSlotsPlaceholder.innerHTML = '';
            if (data.success && data.times.length > 0) {
                data.times.forEach(time => {
                    const timeBtn = document.createElement('button');
                    timeBtn.type = 'button';
                    timeBtn.classList.add('btn', 'btn-outline-success', 'm-1');
                    timeBtn.textContent = time;
                    timeBtn.dataset.time = time;
                    timeBtn.onclick = (event) => selectTimeSlot(event, time, date);
                    timeSlotsPlaceholder.appendChild(timeBtn);
                });
            } else {
                timeSlotsPlaceholder.innerHTML = '<div class="alert alert-warning">No available time slots for this date.</div>';
            }
        } catch (error) {
            console.error('Error fetching time slots:', error);
            showError(timeSlotsPlaceholder, 'Failed to fetch time slots. Please try again.');
        } finally {
            confirmButton.disabled = true;
        }
    }

    // This function handles the final time slot selection
    function selectTimeSlot(event, time, date) {
        // Reset all time slot button styles
        document.querySelectorAll('#time-slots-placeholder .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-success');
        });
        
        // Style the selected button
        event.target.classList.add('active', 'btn-success');
        
        // Create hidden input fields to submit the selected values
        const form = document.getElementById('appointmentForm');
        let dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'appointment_date';
        dateInput.value = date;
        
        let timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'appointment_time';
        timeInput.value = time;
        
        form.appendChild(dateInput);
        form.appendChild(timeInput);

        confirmButton.disabled = false;
    }

    // Event listener for doctor selection change
    doctorSelect.addEventListener('change', (event) => {
        const doctorId = event.target.value;
        if (doctorId) {
            fetchAvailableDates(doctorId);
        } else {
            dynamicFields.style.display = 'none';
            confirmButton.disabled = true;
        }
    });

    // Check for a pre-selected doctor on page load
    if (doctorSelect.value) {
        fetchAvailableDates(doctorSelect.value);
    }
});
</script>

<?php 
$conn->close();
include_once 'includes/footer.php'; 
?>
