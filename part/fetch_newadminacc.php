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

// Your page content goes here...

// Read archived accounts from the JSON file
$archivedAccountsFile = __DIR__ . '/../archived_accounts.json'; // Adjust the path to your file
$archivedAccounts = [];
if (file_exists($archivedAccountsFile)) {
    $archivedAccounts = json_decode(file_get_contents($archivedAccountsFile), true);
}

// Create a string to use in the SQL query for excluding archived accounts
$archivedIds = implode("', '", $archivedAccounts);  // Create a comma-separated string of IDs
$archivedCondition = empty($archivedIds) ? "" : "AND id NOT IN ('$archivedIds')";

// Sanitize and validate the sorting parameters
$sort_by = isset($_GET['sort_by4']) ? $_GET['sort_by4'] : 'id';
$order = isset($_GET['order4']) ? $_GET['order4'] : 'desc';

$allowed_columns = ['id', 'last_name', 'first_name', 'middle_name', 'suffix', 'birthday'];
$allowed_orders = ['asc', 'desc'];

if (!in_array($sort_by, $allowed_columns)) {
    $sort_by = 'id';
}
if (!in_array($order, $allowed_orders)) {
    $order = 'desc';
}

// Modify the query to exclude archived accounts
$sql = "SELECT * FROM accounts WHERE type = 'administrator' $archivedCondition ORDER BY $sort_by $order";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['id']);
        $last_name = htmlspecialchars($row['last_name']);
        $first_name = htmlspecialchars($row['first_name']);
        $middle_name = htmlspecialchars($row['middle_name']);
        $suffix = htmlspecialchars($row['suffix']);
        $birthday = htmlspecialchars($row['birthday']);
        $type = htmlspecialchars($row['type']);

        // Output the row
        echo "<tr data-id='{$id}'>
                <td>{$row['id']}</td>
                <td>{$row['last_name']}</td>
                <td>{$row['first_name']}</td>
                <td>{$row['middle_name']}</td>
                <td>{$row['suffix']}</td>
                <td>{$row['birthday']}</td>
                <td>
                    <i class='bi bi-archive archive-icon'
                       style='margin: 5px; cursor: pointer; color: blue !important; font-size: 20px !important;'
                       data-bs-toggle='modal' 
                       data-bs-target='#archiveModal' 
                       data-id='{$id}'
                       data-lastname='{$last_name}'
                       data-firstname='{$first_name}'
                       data-middlename='{$middle_name}'>
                    </i>

                    <i class='bi bi-pencil-square' 
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

                    <i class='bi bi-trash'
                       data-bs-toggle='modal' 
                       data-bs-target='#deleteModal'
                       data-id='{$id}' 
                       data-last_name='{$last_name}' 
                       data-first_name='{$first_name}'
                       style='margin:5px; color: red !important; font-size:20px !important;'>
                    </i>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No records found</td></tr>";
}
?>
