<?php
session_start();
require_once __DIR__ . '/../connect.php';  // Ensure correct path to your database connection

// Ensure the user is logged in
if (!isset($_SESSION['secured']) || !$_SESSION['secured']) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

// Get the filter parameter from the query string
$filter = isset($_GET['filter']) ? "%" . trim($_GET['filter']) . "%" : '';

$limit = 20; // Set to 20 records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit; // Calculate the offset for the query

// Query to fetch the logs with filtering
$query = "SELECT id, user_id, action, timestamp
          FROM audit_trails
          WHERE LOWER(action) LIKE LOWER(?) 
          ORDER BY id DESC
          LIMIT ? OFFSET ?";

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bind_param("sii", $filter, $limit, $offset);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $logs = [];

    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }

    echo json_encode($logs); // Return the logs as JSON
} else {
    echo json_encode(['error' => 'Failed to fetch logs']);
}

// Close the connection
$stmt->close();
$conn->close();
?>
