<?php
// Function to redirect the user to a different page
function redirect($page) {
    header('Location: ' . $page);
    exit();
}

// Function to set a session message
function set_session_message($type, $message, $field = null) {
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }

    if ($field) {
        $_SESSION['messages'][$type][$field] = $message;
    } else {
        $_SESSION['messages'][$type][] = $message;
    }
}

// Function to get and clear a session message
function get_session_message($type, $field = null) {
    if (isset($_SESSION['messages'][$type])) {
        if ($field) {
            $message = $_SESSION['messages'][$type][$field] ?? null;
            unset($_SESSION['messages'][$type][$field]);
            return $message;
        } else {
            $messages = $_SESSION['messages'][$type];
            unset($_SESSION['messages'][$type]);
            return implode('<br>', $messages);
        }
    }
    return null;
}

// Function to check if a user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to get the logged-in user's role
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

// Function to display all session messages in a consistent way
function display_session_message() {
    $success_message = get_session_message('success');
    $error_message = get_session_message('error');

    if ($success_message) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $success_message . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }

    if ($error_message) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $error_message . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
}
?>