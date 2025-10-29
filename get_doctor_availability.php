<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Include your database connection file
require_once 'includes/db_connect.php';

// Get parameters from the request
$doctorId = $_GET['doctor_id'] ?? null;
$date = $_GET['date'] ?? null;

if (!$doctorId) {
    echo json_encode(['success' => false, 'message' => 'Doctor ID is required.']);
    exit;
}

// Case 1: A specific date is provided, so we fetch available time slots for that date.
if ($date) {
    $available_times = [];

    // Check for future dates
    if (strtotime($date) < strtotime('today')) {
        echo json_encode(['success' => true, 'times' => []]);
        $conn->close();
        exit;
    }

    // Query for available slots that are NOT already booked as an appointment
    $stmt = $conn->prepare("
        SELECT ds.available_time
        FROM doctor_schedules ds
        LEFT JOIN appointments a
            ON ds.doctor_id = a.doctor_id
            AND ds.available_date = a.appointment_date
            AND ds.available_time = a.appointment_time
        WHERE ds.doctor_id = ?
        AND ds.available_date = ?
        AND a.appointment_id IS NULL
        ORDER BY ds.available_time ASC
    ");
    
    if ($stmt) {
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $available_times[] = $row['available_time'];
        }
        $stmt->close();
    }
    
    echo json_encode(['success' => true, 'times' => $available_times]);
} else {
    // Case 2: No date is provided, so we fetch all available dates for the doctor.
    $available_dates = [];
    $stmt = $conn->prepare("
        SELECT DISTINCT available_date
        FROM doctor_schedules
        WHERE doctor_id = ? AND available_date >= CURDATE()
        ORDER BY available_date ASC
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $available_dates[] = $row['available_date'];
        }
        $stmt->close();
    }

    echo json_encode(['success' => true, 'dates' => $available_dates]);
}

$conn->close();
exit;
