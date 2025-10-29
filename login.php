<?php
session_start(); // Ensure session is started
require_once 'includes/header.php'; // Includes session_start() and functions.php
?>

<div class="auth-container">
    <div class="auth-image-section" style="background-image: url('assets/images/background-login.jpg');">
        </div>
    <div class="auth-form-section">
        <div class="auth-form">
            <h2>Login to Your Account</h2>
            <?php
            // display_session_message() is called in header.php, which is good.
            // You can leave this commented or remove it if you prefer not to call it twice.
            // display_session_message();
            ?>

            <?php
            // Retrieve and clear form data and errors from session if redirected back due to validation issues
            $form_data = $_SESSION['form_data'] ?? [];
            $form_errors = $_SESSION['form_errors'] ?? [];
            unset($_SESSION['form_data']);
            unset($_SESSION['form_errors']);
            ?>

            <form action="login_process.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required autocomplete="email">
                    <span class="error-message"><?php echo $form_errors['email'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <span class="error-message"><?php echo $form_errors['password'] ?? ''; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="form-link">Don't have an account? <a href="register.php">Register here</a></p>
                <p class="form-link"><a href="#">Forgot Password?</a></p>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>