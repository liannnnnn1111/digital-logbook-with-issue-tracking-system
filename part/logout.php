<?php
session_start();
require_once "../connect.php"; // Ensure correct path to your database connection
require_once "log_action.php"; // Include the file where logAction function is defined

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log the user out and log the logout action
    if (isset($_SESSION['secured'])) {
        $user_id = $_SESSION['secured'];  // Get user ID from session

        // Log the logout action
        logAction($user_id, 'Logging out of Session');  // Log the action as 'logout'
    }

    // Clear session and destroy
    session_unset();
    session_destroy();
    
    // Redirect to login page
    header("Location: login.php?logged_out=1");
    exit();
} else {
    // If method is not POST, redirect to login or show an error
    header("Location: login.php");
    exit();
}
?>
