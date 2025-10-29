<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Access control: Only logged-in patients can download their own records
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    set_session_message('error', 'Unauthorized access.');
    redirect('login.php');
    exit();
}

$record_id = $_GET['record_id'] ?? null;
if (!$record_id) {
    set_session_message('error', 'Invalid record specified for download.');
    redirect('patient_medical_records.php');
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

// Fetch the medical record for the current patient
$record = [];
$stmt = $conn->prepare("
    SELECT
        mr.record_date,
        mr.diagnosis,
        mr.prescription,
        mr.notes,
        d.first_name AS doctor_first_name,
        d.last_name AS doctor_last_name,
        p.first_name AS patient_first_name,
        p.last_name AS patient_last_name
    FROM medical_records mr
    JOIN doctors d ON mr.doctor_id = d.doctor_id
    JOIN patients p ON mr.patient_id = p.patient_id
    WHERE mr.record_id = ? AND mr.patient_id = ?
");

if ($stmt) {
    $stmt->bind_param("ii", $record_id, $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    }
    $stmt->close();
}

if (empty($record)) {
    set_session_message('error', 'Medical record not found or you do not have permission to download it.');
    redirect('patient_medical_records.php');
    exit();
}

$conn->close();

// --- PDF Generation with Dompdf ---
// We need to require the autoloader to use the library
require_once 'dompdf/autoload.inc.php';

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Create an instance of Dompdf
$options = new Options();
$options->set('defaultFont', 'Courier'); // Set a default font
$dompdf = new Dompdf($options);

// Generate the HTML content to be converted to PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Medical Record</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2 { color: #333; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details p { margin: 5px 0; }
        .content { margin-top: 30px; }
        .section-title { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Online Medical System</h2>
            <h1>Patient Medical Record</h1>
        </div>
        <div class="details">
            <p><strong>Patient Name:</strong> ' . htmlspecialchars($record['patient_first_name'] . ' ' . $record['patient_last_name']) . '</p>
            <p><strong>Doctor Name:</strong> Dr. ' . htmlspecialchars($record['doctor_first_name'] . ' ' . $record['doctor_last_name']) . '</p>
            <p><strong>Record Date:</strong> ' . htmlspecialchars($record['record_date']) . '</p>
        </div>
        <div class="content">
            <h3 class="section-title">Diagnosis</h3>
            <p>' . nl2br(htmlspecialchars($record['diagnosis'])) . '</p>
            <h3 class="section-title">Prescription</h3>
            <p>' . nl2br(htmlspecialchars($record['prescription'])) . '</p>
            <h3 class="section-title">Doctor\'s Notes</h3>
            <p>' . nl2br(htmlspecialchars($record['notes'])) . '</p>
        </div>
    </div>
</body>
</html>';

// Load the HTML into Dompdf
$dompdf->loadHtml($html);

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to the browser
$dompdf->stream("medical_record_" . $record_id . ".pdf", array("Attachment" => true));

exit();
?>