<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../connect.php';

// Check if the user is logged in
if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}



// Sanitize and validate the sorting parameters
$sort_by = isset($_GET['sort_by6']) ? $_GET['sort_by6'] : 'id';
$order = isset($_GET['order6']) ? $_GET['order6'] : 'asc';

// Allowed columns for sorting
$allowed_columns = ['id', 'last_name', 'first_name', 'middle_name', 'suffix', 'birthday'];
$allowed_orders = ['asc', 'desc'];

// Validate the sort column
if (!in_array($sort_by, $allowed_columns)) {
    $sort_by = 'id';
}

// Validate the sort order
if (!in_array($order, $allowed_orders)) {
    $order = 'asc';
}

// Prepare the SQL query
$sql = "SELECT * FROM accounts WHERE type = 'student' ORDER BY $sort_by $order";
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Use htmlspecialchars to escape output for safety
            $id = htmlspecialchars($row['id']);
            $last_name = htmlspecialchars($row['last_name']);
            $first_name = htmlspecialchars($row['first_name']);
            $middle_name = htmlspecialchars($row['middle_name']);
            $suffix = htmlspecialchars($row['suffix']);
            $birthday = htmlspecialchars($row['birthday']);
             $type = htmlspecialchars($row['type']);
             $hashed_password = htmlspecialchars($row['password']);
            
            echo "<tr data-id='<?php echo $id; ?>' >
                <td>{$id}</td>
                <td>{$last_name}</td>
                <td>{$first_name}</td>
                <td>{$middle_name}</td>
                <td>{$suffix}</td>
                <td>{$birthday}</td>
                <td>
                    <!-- Update Button -->

<i 
    class='bi bi-pencil-square' 
    data-bs-toggle='modal' 
    data-bs-target='#updateModal' 
    data-id='{$id}' 
    data-lastname='{$last_name}' 
    data-firstname='{$first_name}' 
    data-middlename='{$middle_name}' 
    data-suffix='{$suffix}' 
    data-birthday='{$birthday}' 
    data-password='{$password}' 
    data-type='{$type}' 
    style='cursor: pointer; margin: 5px; color: black !important; font-size:20px !important;'>
</i>


<!-- Delete Button -->
                <i class='bi bi-trash'
                   data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$id}' data-last_name='{$last_name}' data-first_name='{$first_name}'
                    style='margin:5px; !important; color: red !important; font-size:20px !important;'>
                    </i>

                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No records found</td></tr>";
    }
} else {
    echo "<tr><td colspan='7'>Error fetching data from database.</td></tr>";
}

// Close the database connection

?>
