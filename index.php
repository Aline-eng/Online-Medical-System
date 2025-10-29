<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php'; 

// Redirect logged-in users to their specific dashboards
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role'] ?? '') { // Use null coalescing for safety
        case 'patient':
            redirect('patient_dashboard.php');
            break;
        case 'doctor':
            redirect('doctor_dashboard.php');
            break;
        case 'admin':
            redirect('admin_dashboard.php');
            break;
        default:
            // Stay on index.php if role is not recognized or not set (though role should be set on login)
            set_session_message('info', 'Welcome! Your role is not recognized. Please contact support.');
            break;
    }
    // If a redirect occurred, exit() would have been called.
    // If not, continue to display index.php content.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediBook</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom CSS for the scrolling image animation -->
    <style>
        @keyframes scroll-left {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        @keyframes scroll-right {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .scrolling-container {
            width: 200%;
            display: flex;
            gap: 1rem;
            animation: scroll-left 40s linear infinite;
        }

        .scrolling-container-right {
            animation: scroll-right 40s linear infinite;
        }

        .scrolling-item {
            width: 200px;
            height: 120px;
            flex-shrink: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .scrolling-item:hover {
            transform: scale(1.05);
        }

        .scrolling-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>


<section class="hero-section text-center py-5" style="background-image: url('assets/images/hero-bg.jpeg'); background-size: cover; background-position: center; min-height: 500px;">
    <div class="container">
        <h1>Welcome to MediBook</h1>
        <p class="lead">Your trusted platform for managing medical appointments and records.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p>
                <a href="login.php" class="hero-btn btn-primary-hero mx-2">Login</a>
                <a href="register.php" class="hero-btn btn-secondary-hero mx-2">Register</a>
            </p>
        <?php else: ?>
            <p class="lead">You are logged in as <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?> (Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'Unknown'); ?>).</p>
            <p>Go to your <a href="<?php
                if (isset($_SESSION['role'])) {
                    if ($_SESSION['role'] === 'patient') echo 'patient_dashboard.php';
                    elseif ($_SESSION['role'] === 'doctor') echo 'doctor_dashboard.php';
                    elseif ($_SESSION['role'] === 'admin') echo 'admin_dashboard.php';
                    else echo 'index.php';
                } else {
                    echo 'index.php';
                }
            ?>" class="btn btn-primary">Dashboard</a></p>
        <?php endif; ?>
    </div>
</section>

<!-- Scrolling image animation section -->
<section class="py-5">
    <div class="container-fluid">
        <!-- Scrolling from right to left -->
        <div class="overflow-hidden mb-4">
            <div class="scrolling-container">
                <!-- Use your actual image paths here. I've added placeholders for demonstration. -->
                <div class="scrolling-item"><img src="assets/images/doctor2.jpeg" alt="Doctor 2"></div>
                <div class="scrolling-item"><img src="assets/images/patientcare.webp" alt="Patient Care"></div>
                <div class="scrolling-item"><img src="assets/images/clinic-view.webp" alt="Clinic View"></div>
                <div class="scrolling-item"><img src="assets/images/medical-team.webp" alt="Medical Team"></div>
                <div class="scrolling-item"><img src="assets/images/equipment.webp" alt="Equipment"></div>
                <!-- Duplicate for seamless loop -->
                <div class="scrolling-item"><img src="assets/images/doctor2.jpeg" alt="Doctor 2"></div>
                <div class="scrolling-item"><img src="assets/images/patientcare.webp" alt="Patient Care"></div>
                <div class="scrolling-item"><img src="assets/images/clinic-view.webp" alt="Clinic View"></div>
                <div class="scrolling-item"><img src="assets/images/medical-team.webp" alt="Medical Team"></div>
                <div class="scrolling-item"><img src="assets/images/equipment.webp" alt="Equipment"></div>
            </div>
        </div>

        <!-- Scrolling from left to right -->
        <div class="overflow-hidden">
            <div class="scrolling-container scrolling-container-right">
                <!-- Use your actual image paths here. I've added placeholders for demonstration. -->
                <div class="scrolling-item"><img src="assets/images/modern-facility.webp" alt="Modern Facility"></div>
                <div class="scrolling-item"><img src="assets/images/smiling-staff.webp" alt="Smiling Staff"></div>
                <div class="scrolling-item"><img src="assets/images/happy-patient.webp" alt="Happy Patient"></div>
                <div class="scrolling-item"><img src="assets/images/lab-work.webp" alt="Lab Work"></div>
                <div class="scrolling-item"><img src="assets/images/consultation.webp" alt="Consultation"></div>
                <!-- Duplicate for seamless loop -->
                <div class="scrolling-item"><img src="assets/images/hero-bg.jpeg" alt="Modern Facility"></div>
                <div class="scrolling-item"><img src="assets/images/smiling-staff.webp" alt="Smiling Staff"></div>
                <div class="scrolling-item"><img src="assets/images/happy-patient.webp" alt="Happy Patient"></div>
                <div class="scrolling-item"><img src="assets/images/lab-work.webp" alt="Lab Work"></div>
                <div class="scrolling-item"><img src="assets/images/consultation.webp" alt="Consultation"></div>
            </div>
        </div>
    </div>
</section>


<section class="features-section">
    <div class="container">
        <h2>Our Features</h2>
        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <i class="fas fa-calendar-alt feature-icon"></i>
                <h3 class="feature-title">Easy Appointments</h3>
                <p class="feature-description">Book and manage your doctor appointments effortlessly with our intuitive online scheduling system.</p>
                <a href="book_appointment.php" class="feature-btn">Learn More</a>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card">
                <i class="fas fa-shield-alt feature-icon"></i>
                <h3 class="feature-title">Secure Records</h3>
                <p class="feature-description">Access your medical records securely anytime, anywhere with our encrypted platform.</p>
                <a href="patient_medical_records.php" class="feature-btn">Learn More</a>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card">
                <i class="fas fa-user-md feature-icon"></i>
                <h3 class="feature-title">Find Doctors</h3>
                <p class="feature-description">Search for specialists and find the perfect doctor for your specific healthcare needs.</p>
                <a href="doctors.php" class="feature-btn">Find Doctors</a>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>