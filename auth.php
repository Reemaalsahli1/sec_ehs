<?php
session_start();

// Check if user is logged in
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Get current user information
function get_logged_in_user() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'employee_id' => $_SESSION['employee_id'],
            'name' => $_SESSION['user_name']
        ];
    }
    return null;
}

// Check if user is logged in (returns boolean)
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
?>
