<?php
// Connect to the database
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../connect.php';
require_once "log_action.php";

$user_id = $_SESSION['secured']; 

if (!isset($_SESSION['secured'])) {
    header('Location: ../login.php');
    exit();
}

// Handle GET request to fetch account details (for confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];



    // Sanitize the input (important for security)
    $id = $conn->real_escape_string($id);

    // Query to fetch the account details
    $query = "SELECT last_name, first_name FROM accounts WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($last_name, $first_name);

        if ($stmt->fetch()) {
            // Return the account details as JSON
         echo json_encode(['last_name' => $last_name, 'first_name' => $first_name]);
        } else {
            echo json_encode(['error' => 'Account not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing query']);
    }

    $conn->close();
    exit();
}

// Handle POST request to delete account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Sanitize and retrieve the id
    $id = $_POST['id'];

  $query = "SELECT type FROM accounts WHERE id = ?";
    $type = null;  // Initialize type variable
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $id);  // Bind the user id
        $stmt->execute();
        $stmt->bind_result($type);   // Fetch the type value
        $stmt->fetch();  // Execute the fetch

        // Check if we retrieved the type, otherwise use 'default'
        $type = $type ?: 'default';  // Fallback to 'default' if type is null or empty
        $stmt->close();
    }
    // Debugging: Log the id to check the value
    error_log("Attempting to delete account with id: " . $id);

    // Start a transaction

    // Prepare the DELETE query
    $deleteSql = "DELETE FROM accounts WHERE id = ?";

    if ($stmt = $conn->prepare($deleteSql)) {
        $stmt->bind_param('s', $id);
        if ($stmt->execute()) {
            // Commit the transaction
        logAction($user_id, "Successfully deletion of  $id's account from $type table");

            echo 'success';
        } else {
            $conn->rollback();
            echo 'Error executing delete query: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $conn->rollback();
        echo 'Error preparing query: ' . $conn->error;
    }

    // Re-enable foreign key checks


    $conn->close();
    exit();
}
?>
