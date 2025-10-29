<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Access control: Only logged-in patients can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    set_session_message('error', 'You must be logged in as a patient to view your medical records.');
    redirect('login.php');
    exit();
}

$patient_user_id = $_SESSION['user_id'];
$patient_id_stmt = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = ?");
$patient_id_stmt->bind_param("i", $patient_user_id);
$patient_id_stmt->execute();
$patient_id_result = $patient_id_stmt->get_result();
$patient_id_row = $patient_id_result->fetch_assoc();
$patient_id = $patient_id_row['patient_id'];
$patient_id_stmt->close();

$medical_records = [];
$stmt = $conn->prepare("
    SELECT
        mr.record_id,
        mr.record_date,
        mr.diagnosis,
        mr.prescription,
        mr.notes,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name
    FROM medical_records mr
    JOIN doctors d ON mr.doctor_id = d.doctor_id
    WHERE mr.patient_id = ?
    ORDER BY mr.record_date DESC
");

if ($stmt) {
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $medical_records[] = $row;
        }
    }
    $stmt->close();
} else {
    set_session_message('error', 'Failed to retrieve medical records: ' . $conn->error);
}
?>

<section class="medical-records-section">
    <div class="container">
        <h2>Your Medical Records</h2>
        
        <?php if (!empty($medical_records)): ?>
            <?php foreach ($medical_records as $record): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        Record Date: <?php echo htmlspecialchars($record['record_date']); ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Diagnosis: <?php echo htmlspecialchars($record['diagnosis']); ?></h5>
                        <p class="card-text"><strong>Prescription:</strong> <?php echo htmlspecialchars($record['prescription']); ?></p>
                        <p class="card-text"><strong>Doctor's Notes:</strong> <?php echo htmlspecialchars($record['notes']); ?></p>
                    </div>
                    <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                        <div>
                            Recorded by Dr. <?php echo htmlspecialchars($record['doctor_first_name'] . ' ' . $record['doctor_last_name']); ?>
                        </div>
                        <div>
                            <?php if (!empty($record['record_id'])): ?>
                                <a href="download_medical_record.php?record_id=<?php echo htmlspecialchars($record['record_id']); ?>" class="btn btn-sm btn-outline-secondary">Download PDF</a>
                            <?php else: ?>
                                <span class="text-danger">Record ID missing. Cannot download.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">You have no medical records on file.</p>
        <?php endif; ?>
        
    </div>
</section>

<?php
$conn->close();
include_once 'includes/footer.php';
?>