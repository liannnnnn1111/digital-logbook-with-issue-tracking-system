<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure no unwanted output before processing the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../connect.php';

    // Set header for JSON response
    header('Content-Type: application/json');

    // Check if the user is logged in
    if (!isset($_SESSION['secured'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit();
    }

    $idToUnarchive = $_POST['id']; // Get ID to unarchive
    $archivedAccountsFile = __DIR__ . '/../archived_accounts.json';

    if (file_exists($archivedAccountsFile)) {
        $archivedAccounts = json_decode(file_get_contents($archivedAccountsFile), true);

        // Check if the ID exists in the archived list
        if (($key = array_search($idToUnarchive, $archivedAccounts)) !== false) {
            unset($archivedAccounts[$key]); // Remove the ID from archived accounts
            file_put_contents($archivedAccountsFile, json_encode(array_values($archivedAccounts)));

            // Send JSON response for success
            echo json_encode(['status' => 'success', 'message' => 'Account successfully unarchived.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Account not found in archive.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Archived accounts file not found.']);
    }
} else {
    // If the request is not POST, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
