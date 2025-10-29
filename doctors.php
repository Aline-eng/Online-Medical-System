<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch all doctors, their specialization names, and full names from the users table
$doctors = [];
$sql = "SELECT d.doctor_id, d.image_url, u.full_name, s.name 
        FROM doctors d 
        JOIN users u ON d.user_id = u.user_id
        JOIN specializations s ON d.specialization_id = s.specialization_id
        WHERE u.is_active = 1
        ORDER BY u.full_name ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Our Doctors</h2>
    <p class="text-center lead">Meet our team of experienced and dedicated medical professionals.</p>

    <?php if (!empty($doctors)): ?>
        <div class="row">
            <?php foreach ($doctors as $doctor): ?>
                <div class="col-md-4 mb-4">
                    <div class="card doctor-card">
                        <?php if (!empty($doctor['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($doctor['image_url']); ?>" class="card-img-top" alt="Dr. <?php echo htmlspecialchars($doctor['full_name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200.png?text=Dr.+<?php echo urlencode($doctor['full_name']); ?>" class="card-img-top" alt="Dr. <?php echo htmlspecialchars($doctor['full_name']); ?>">
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title">Dr. <?php echo htmlspecialchars($doctor['full_name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($doctor['name']); ?></p>
                            <a href="book_appointment.php?doctor_id=<?php echo $doctor['doctor_id']; ?>" class="btn btn-primary mt-3">Book Appointment</a>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">No doctors found at this time.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
include_once 'includes/footer.php';
?>