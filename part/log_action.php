<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../connect.php';  // This should go up one level and find connect.php in admin/
// Adjust the path as necessary
require_once "log_action.php"; // Include the file where logAction function is defined

function logAction( $user_id, $action) {
    global $conn;

    // Format timestamp as 'Day Month Year' (e.g., 07 October 2024)
    $stmt = $conn->prepare("INSERT INTO audit_trails ( user_id, action, timestamp) 
                            VALUES ( ?, ?, DATE_FORMAT(NOW(), '%d %M %Y %H:%i:%s'))");

    // Bind parameters
    $stmt->bind_param("ss",  $user_id, $action);  // 'ss' for user_id and action as strings

    // Execute the query
    $stmt->execute();
    $stmt->close();
}



// Function to log user actions
?>
