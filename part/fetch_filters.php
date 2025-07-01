<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

require_once '../connect.php';

$filters = [];

// Fetching distinct types
$result = $conn->query("SELECT DISTINCT a.type FROM accounts a INNER JOIN records_log r ON a.id = r.user_id WHERE a.type IN ('faculty', 'student', 'administrator', 'staff')");
if ($result) {
    $filters['type'] = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query failed for type: " . $conn->error);
}

// Fetching distinct rooms
$result = $conn->query("SELECT DISTINCT r.room FROM records_log r  INNER JOIN accounts a ON a.id = r.user_id WHERE a.type IN ('faculty', 'student', 'administrator', 'staff')");
if ($result) {
    $filters['room'] = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query failed for room: " . $conn->error);
}

// Fetching distinct dates
$result = $conn->query("SELECT DISTINCT CONCAT( r.log_month, ' ', r.log_year) AS dates FROM records_log r INNER JOIN accounts a ON a.id = r.user_id WHERE a.type IN ('faculty', 'student', 'administrator', 'staff')");
if ($result) {
    $filters['dates'] = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query failed for dates: " . $conn->error);
}

// Fetching distinct subjects from list_faculty_subjects
$result = $conn->query("SELECT DISTINCT f.subject FROM list_faculty_subjects f INNER JOIN records_log r ON r.faculty_subject_id = f.id");
if ($result) {
    $filters['subject'] = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query failed for subject: " . $conn->error);
}

// Fetching distinct faculty_ids from list_faculty_subjects
$result = $conn->query("SELECT DISTINCT CONCAT(a.last_name, ' ', a.first_name, ' ', a.middle_name) AS faculty FROM accounts a INNER JOIN list_faculty_subjects f ON a.id = f.faculty_id");
if ($result) {
    $filters['faculty'] = $result->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Query failed for faculty_id: " . $conn->error);
}

// Output the filters as JSON
echo json_encode($filters);
?>
