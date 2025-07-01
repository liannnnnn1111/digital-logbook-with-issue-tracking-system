<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

include('../connect.php');
require_once "log_action.php";



// Handle POST request for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'], $_POST['remarks'], $_POST['timestamp'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $timestamp = $conn->real_escape_string($_POST['timestamp']);

    // Check if status is "Resolved" and remarks are provided
    if ($status === 'Resolved' && empty($remarks)) {
        echo json_encode(['message' => 'Remarks are required when resolving an issue.']);
        exit();
    }

    // Set the timestamp to NULL if status is not "Resolved"
    $status_remarks_timestamp = ($status === 'Resolved') ? $timestamp : NULL;

    // Update query
    $updateSql = "UPDATE records_issue SET status = '$status', remarks = '$remarks', status_remarks_timestamp = " . ($status_remarks_timestamp ? "'$status_remarks_timestamp'" : 'NULL') . " WHERE id = '$id'";

    if ($conn->query($updateSql) === TRUE) {
        // Get the description and ID for logging
        $user_id = $_SESSION['secured'];  // Get the logged-in admin's ID
        $result = $conn->query("SELECT description, room, pc_id FROM records_issue WHERE id = '$id'");
        $row = $result->fetch_assoc();
        $description = $row['description'];
        $room =$row['room'];
        $pc_id =$row['pc_id'];

        // Log the action
        logAction($user_id, "Changed status as $status for the issue  description: $description in $room, $pc_id  ");

        // Return success message
        echo json_encode(['message' => 'Status updated successfully']);
    } else {
        echo json_encode(['message' => 'Error updating status: ' . $conn->error]);
    }
    exit();
}


// Base SQL query for fetching issues
$sql = "
    SELECT 
        r.id,
        r.user_id,
        r.room,
        r.pc_id,
        r.issue_nature_id,
        l.nature_of_issue,
        r.description,
        r.status_remarks_timestamp,
        r.remarks,
        r.status,
        a.type AS account_status,
        CONCAT(r.issue_day, ' ', r.issue_month, ' ', r.issue_year, ' ', r.issue_time) AS issue_date
    FROM 
        records_issue r
    LEFT JOIN 
        accounts a ON r.user_id = a.id
    LEFT JOIN 
        list_issue_nature l ON r.issue_nature_id = l.id
    WHERE 
          (a.type IS NOT NULL OR r.user_id IS NOT NULL)";

// Validate filter input
if (isset($_GET['filter_by'], $_GET['filter_value'])) {
    $valid_columns = ['user_id', 'room', 'pc_id', 'issue_nature_id', 'status' ,'remarks'];
    $filter_by = $conn->real_escape_string($_GET['filter_by']);
    $filter_value = $conn->real_escape_string($_GET['filter_value']);

    if ($filter_value !== 'All' && in_array($filter_by, $valid_columns)) {
        $sql .= " AND $filter_by = '$filter_value'";
    }
}

// Validate sort input
if (isset($_GET['sort_by11'], $_GET['order11'])) {
    $valid_sort_columns = [ 'id','issue_date', 'user_id', 'room', 'pc_id', 'nature_of_issue', 'description', 'status', 'remarks','status_remarks_timestamp'];
    $sort_by11 = $conn->real_escape_string($_GET['sort_by11']);
    $order11 = $conn->real_escape_string($_GET['order11']);

    if (in_array($sort_by11, $valid_sort_columns) && in_array(strtoupper($order11), ['ASC', 'DESC'])) {
        $sql .= " ORDER BY $sort_by11 $order11";
    }
}
    else {
    $sql .= " ORDER BY id DESC";  // Default sorting by log_time
}

// Execute query
$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Generate table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
         if ($row['account_status'] === null) {
            // If account_status is null, meaning no matching account was found, mark it as deleted
            $account_status = 'Deleted Account';
        } else {
            // Otherwise, use the actual account status
            $account_status = $row['account_status'];
        }
        echo "<tr>
             <td>" . htmlspecialchars($row["id"]) . "</td>
            <td>" . htmlspecialchars($row["issue_date"]) . "</td>
            <td>" . htmlspecialchars($row["user_id"]) . "</td>
            <td>" . htmlspecialchars($row["room"]) . "</td>
            <td>" . htmlspecialchars($row["pc_id"]) . "</td>
            <td>" . htmlspecialchars($row["nature_of_issue"]) . "</td>
            <td>" . htmlspecialchars($row["description"]) . "</td>
            <td>
                <select class='selects' name='status' id='statusSelect_" . $row['id'] . "' data-previous='" . htmlspecialchars($row["status"]) . "'>
                    <option value='Not Resolved' " . ($row["status"] == 'Not Resolved' ? 'selected' : '') . ">Not Resolved</option>
                    <option value='Ongoing' " . ($row["status"] == 'Ongoing' ? 'selected' : '') . ">Ongoing</option>
                    <option value='Resolved' " . ($row["status"] == 'Resolved' ? 'selected' : '') . ">Resolved</option>
                </select>
            </td>
              <td>" . htmlspecialchars($row["remarks"]) . "</td>
            <td>" . htmlspecialchars($row["status_remarks_timestamp"]) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='10'>0 results</td></tr>";
}

$conn->close();
?>
