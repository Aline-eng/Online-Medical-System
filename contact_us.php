<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Retrieve form data and errors from session for re-population if redirected back
$form_data = $_SESSION['form_data'] ?? [];
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>

<section class="contact-section">
    <div class="container">
        <h2>Contact Us</h2>
        <p class="lead">Have a question or a problem? Please fill out the form below and we will get back to you as soon as possible.</p>

        <form action="contact_process.php" method="POST" class="contact-form">
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($form_data['name'] ?? ($_SESSION['username'] ?? '')); ?>" required>
                <span class="error-message"><?php echo $form_errors['name'] ?? ''; ?></span>
            </div>
            
            <div class="form-group">
                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($form_data['email'] ?? ($_SESSION['email'] ?? '')); ?>" required>
                <span class="error-message"><?php echo $form_errors['email'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($form_data['subject'] ?? ''); ?>" required>
                <span class="error-message"><?php echo $form_errors['subject'] ?? ''; ?></span>
            </div>

            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="6" class="form-control" required><?php echo htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
                <span class="error-message"><?php echo $form_errors['message'] ?? ''; ?></span>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</section>

<?php 
include_once 'includes/footer.php'; 
?>