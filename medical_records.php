<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in doctors can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    set_session_message('error', 'You must be logged in as a doctor to manage medical records.');
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

// Fetch all patients for the dropdown
$patients = [];
$sql = "
    SELECT pat.patient_id, u.full_name
    FROM patients pat
    JOIN users u ON pat.user_id = u.user_id
    ORDER BY u.full_name ASC
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    set_session_message('info', 'No patients found in the system.');
}

// Retrieve form data and errors from session for re-population if redirected back
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Variables for displaying records
$selected_patient_id = $_GET['patient_id'] ?? ($form_data['patient_id'] ?? null);
$patient_records = [];
$selected_patient_name = '';

if ($selected_patient_id) {
    // Get the name of the selected patient
    $patient_name_stmt = $conn->prepare("
        SELECT u.full_name
        FROM patients p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.patient_id = ?
    ");
    $patient_name_stmt->bind_param("i", $selected_patient_id);
    $patient_name_stmt->execute();
    $patient_name_result = $patient_name_stmt->get_result();
    if ($patient_name_row = $patient_name_result->fetch_assoc()) {
        $selected_patient_name = $patient_name_row['full_name'];
    }
    $patient_name_stmt->close();

    // Fetch all medical records for this specific doctor and patient
    $stmt = $conn->prepare("
        SELECT
            record_id,
            record_date,
            diagnosis,
            prescription,
            notes
        FROM medical_records
        WHERE patient_id = ? AND doctor_id = ?
        ORDER BY record_date DESC
    ");

    if ($stmt) {
        $stmt->bind_param("ii", $selected_patient_id, $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $patient_records[] = $row;
            }
        }
        $stmt->close();
    }
}
?>

<section class="medical-records-section">
    <div class="container">
        <h2>Manage Patient Medical Records</h2>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Select a Patient</h5>
                <form action="medical_records.php" method="GET" id="patientSelectForm">
                    <div class="form-group">
                        <label for="patient">Patient:</label>
                        <select id="patient" name="patient_id" class="form-control" onchange="document.getElementById('patientSelectForm').submit();" required>
                            <option value="">-- Select a Patient --</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo htmlspecialchars($patient['patient_id']); ?>"
                                    <?php echo ($selected_patient_id == $patient['patient_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patient['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selected_patient_id): ?>
            <!-- Display Existing Records -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4>Previous Medical Records for <?php echo htmlspecialchars($selected_patient_name); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($patient_records)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Prescription</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patient_records as $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($record['prescription'])); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($record['notes'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No previous medical records found for this patient.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form to Add a New Record -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>Add New Patient Record for <?php echo htmlspecialchars($selected_patient_name); ?></h4>
                </div>
                <div class="card-body">
                    <form action="medical_records_process.php" method="POST" id="addRecordForm">
                        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($selected_patient_id); ?>">
                        <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">

                        <div class="form-group mb-3">
                            <label for="diagnosis">Diagnosis:</label>
                            <textarea id="diagnosis" name="diagnosis" rows="3" class="form-control" required><?php echo htmlspecialchars($form_data['diagnosis'] ?? ''); ?></textarea>
                            <span class="error-message"><?php echo $form_errors['diagnosis'] ?? ''; ?></span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="prescription">Prescription:</label>
                            <textarea id="prescription" name="prescription" rows="3" class="form-control" required><?php echo htmlspecialchars($form_data['prescription'] ?? ''); ?></textarea>
                            <span class="error-message"><?php echo $form_errors['prescription'] ?? ''; ?></span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Doctor Notes:</label>
                            <textarea id="notes" name="notes" rows="3" class="form-control" required><?php echo htmlspecialchars($form_data['notes'] ?? ''); ?></textarea>
                            <span class="error-message"><?php echo $form_errors['notes'] ?? ''; ?></span>
                        </div>

                        <button type="submit" class="btn btn-success btn-block mt-4">Add Record</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-muted mt-5">Please select a patient from the list above to view their medical history and add a new record.</p>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>
