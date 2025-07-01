<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['secured'])) {
    header('Location: ../login.php');
    exit();
}

require_once "../connect.php"; 
require_once "log_action.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $suffix = $_POST['suffix'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $birthday = $_POST['birthday'];
    $type = $_POST['options'];

    // Validate birthday format
    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $birthday)) {
        echo json_encode(['success' => false, 'message' => 'Invalid birthday format.']);
        exit();
    }

    // Check if user ID already exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM accounts WHERE id = ?");
    $checkStmt->bind_param("s", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'User ID already exists.']);
        exit();
    }

   

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL statement to insert the new account
    $stmt = $conn->prepare("INSERT INTO accounts (id, last_name, first_name, middle_name, suffix, birthday, type, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $id, $last_name, $first_name, $middle_name, $suffix, $birthday, $type, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $_SESSION['secured']; // Get the logged-in admin's ID
        logAction($user_id, "Created an account for $id as $type"); // Log the creation action

        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
