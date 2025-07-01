<?php
require_once __DIR__ . '/../connect.php';
require_once "log_action.php";

// Check if the user is logged in
if (!isset($_SESSION['secured'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = $_SESSION['secured']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data for password change
    $id = $_POST['id'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate all fields
    if (empty($id) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['error' => 'All fields are required.']);
        exit();
    }

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo json_encode(['error' => 'New password and confirmation do not match.']);
        exit();
    }

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM accounts WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    // If the password is empty in the database, assume the current password is valid (i.e., the user is setting a new password)
 

    // Check if the new password is the same as the current password
    if ($new_password === $current_password) {
        echo json_encode(['error' => 'New password cannot be the same as the current password.']);
        exit();
    }

    // Validate the new password length and strength
    if (strlen($new_password) < 8) {
        echo json_encode(['error' => 'Password must be at least 8 characters long.']);
        exit();
    }
    
    if (!preg_match('/[A-Z]/', $new_password) || !preg_match('/\d/', $new_password) || !preg_match('/[\W_]/', $new_password)) {
        echo json_encode(['error' => 'Password must contain at least one uppercase letter, one number, and one special character.']);
        exit();
    }

    // Hash the new password
    $hashed_password = crypt($new_password, '$2a$12$' . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22));
    // Update the password in the database
    $update_stmt = $conn->prepare("UPDATE accounts SET password = ? WHERE id = ?");
    $update_stmt->bind_param('ss', $hashed_password, $id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => 'Password updated successfully.']);
        logAction($user_id, "Changed password successfully for $id");  // Log the action
    } else {
        echo json_encode(['error' => 'Failed to update the password.']);
    }

    $update_stmt->close();
    $conn->close();
}
?>
