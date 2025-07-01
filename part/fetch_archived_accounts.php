<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../connect.php';

// Path to the JSON file containing archived account IDs
$archivedAccountsFile = __DIR__ . '/../archived_accounts.json';
$archivedAccounts = [];

// Check if the file exists
if (file_exists($archivedAccountsFile)) {
    $archivedAccounts = json_decode(file_get_contents($archivedAccountsFile), true);
}

if (!empty($archivedAccounts)) {
    // Create a string of archived IDs for the query
    $archivedIds = implode("', '", $archivedAccounts);

    // Fetch archived accounts from the database
    $sql = "SELECT * FROM accounts WHERE id IN ('$archivedIds')";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
        // Return the results as JSON
        echo json_encode(['status' => 'success', 'data' => $accounts]);
    } else {
        echo json_encode(['status' => 'success', 'data' => []]);
    }
} else {
    // If no archived accounts exist
    echo json_encode(['status' => 'success', 'data' => []]);
}
?>
