<?php
ob_start();

// Ensure the session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection and logging
require_once __DIR__ . '/../connect.php';
require_once "log_action.php";

// Ensure the user is authenticated
if (!isset($_SESSION['secured'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['secured']; // Get the logged-in admin's ID

// Path to the archived accounts file
$archivedAccountsFile = __DIR__ . '/../archived_accounts.json';

// Initialize archived accounts if the file exists
$archived_accounts = [];
if (file_exists($archivedAccountsFile)) {
    $archived_accounts = json_decode(file_get_contents($archivedAccountsFile), true);
}

// Handle archiving request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $conn->real_escape_string($_POST['id']);

        // Check if the account exists in the database
        $check_query = "SELECT id FROM accounts WHERE id = ?";
        if ($stmt = $conn->prepare($check_query)) {
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Archive account by adding to session and updating file
                if (!in_array($id, $archived_accounts)) {
                    $archived_accounts[] = $id; // Add to archived accounts array
                    file_put_contents($archivedAccountsFile, json_encode($archived_accounts)); // Save to file
                    logAction($user_id, "Archived account with ID: $id");
                    echo json_encode(['success' => 'Account archived successfully.']);
                } else {
                    echo json_encode(['error' => 'Account is already archived.']);
                }
            } else {
                echo json_encode(['error' => 'Account does not exist.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error preparing query.']);
        }

        $conn->close();
        exit();
    }

    // Handle unarchiving request
    if (isset($_POST['unarchive_id'])) {
        $id = $_POST['unarchive_id'];
        if (($key = array_search($id, $archived_accounts)) !== false) {
            unset($archived_accounts[$key]);
            file_put_contents($archivedAccountsFile, json_encode($archived_accounts)); // Save to file
            logAction($user_id, "Unarchived account with ID: $id");
            echo json_encode(['success' => 'Account unarchived successfully.']);
        } else {
            echo json_encode(['error' => 'Account is not archived.']);
        }
        exit();
    }
}

echo json_encode(['error' => 'Invalid request']);
exit();
