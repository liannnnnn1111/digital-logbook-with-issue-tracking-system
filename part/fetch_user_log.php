<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

require_once '../connect.php';

// Prepare the base query for fetching data
$sql = "
    SELECT 
        r.id,
        r.user_id,
        r.room,
        r.pc_id,
        a.type AS account_status,  -- Account type or NULL if account is deleted
        r.faculty_subject_id,
        f.subject,
        CONCAT(wa.last_name, ' ', wa.first_name, ' ', wa.middle_name) AS faculty,  -- Full name of the faculty
        CONCAT(r.log_day, ' ', r.log_month, ' ', r.log_year, ' ', r.log_time) AS dates
    FROM 
        records_log r
    LEFT JOIN 
        accounts a ON r.user_id = a.id  -- Changed INNER JOIN to LEFT JOIN
    LEFT JOIN 
        list_faculty_subjects f ON r.faculty_subject_id = f.id
    LEFT JOIN 
        accounts wa ON f.faculty_id = wa.id
    WHERE 
        (a.type IS NOT NULL OR r.user_id IS NOT NULL)";  // Include logs even for deleted accounts

// Handle filters if any
if (isset($_GET['filter_by12']) && isset($_GET['filter_value12'])) {
    $filter_by12 = $_GET['filter_by12'];  // This will be an array
    $filter_value12 = $_GET['filter_value12'];  // This will also be an array

    // Make sure both arrays have the same number of elements
    if (count($filter_by12) === count($filter_value12)) {
        $filter_conditions = [];
        foreach ($filter_by12 as $index => $filter) {
            $filter_values = explode('|', urldecode($filter_value12[$index]));
            $escaped_values = array_map([$conn, 'real_escape_string'], $filter_values);

            // Add conditions based on the filter type
            if ($filter === 'type') {
                $filter_conditions[] = "a.type IN ('" . implode("', '", $escaped_values) . "')";
            } elseif ($filter === 'room') {
                $filter_conditions[] = "r.room IN ('" . implode("', '", $escaped_values) . "')";
            } elseif ($filter === 'dates') {
                $filter_conditions[] = "CONCAT(r.log_month, ' ', r.log_year) IN ('" . implode("', '", $escaped_values) . "')";
            } elseif ($filter === 'faculty') {
                $filter_conditions[] = "CONCAT(wa.last_name, ' ', wa.first_name, ' ', wa.middle_name) IN('" . implode("', '", $escaped_values) . "')";
            } elseif ($filter === 'subject') {
                $filter_conditions[] = "f.subject IN ('" . implode("', '", $escaped_values) . "')";
            }
        }

        // Append the filter conditions to the SQL query
        if (!empty($filter_conditions)) {
            $sql .= " AND " . implode(" AND ", $filter_conditions);
        }
    }
}

// Sorting conditions
if (isset($_GET['sort_by12']) && isset($_GET['order12'])) {
    $sort_by12 = $conn->real_escape_string($_GET['sort_by12']);
    $order12 = $conn->real_escape_string($_GET['order12']);
    $sql .= " ORDER BY $sort_by12 $order12";
} else {
    $sql .= " ORDER BY r.id DESC";  // Default sorting
}

// Execute the query
$result = $conn->query($sql);
if (!$result) {
    error_log("Query Error: " . $conn->error);
    echo "Query Error: " . $conn->error;  // Output the error message for debugging
    exit();
}

// Output the results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if the account exists
        if ($row['account_status'] === null) {
            // If account_status is null, meaning no matching account was found, mark it as deleted
            $account_status = 'Deleted Account';
        } else {
            // Otherwise, use the actual account status
            $account_status = $row['account_status'];
        }

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['dates']}</td>
            <td>{$row['pc_id']}</td>
            <td>{$row['room']}</td>
            <td>{$row['user_id']}</td>
            <td>{$row['subject']}</td>
            <td>{$row['faculty']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found</td></tr>";
}

$conn->close();  // Close the database connection

?>
