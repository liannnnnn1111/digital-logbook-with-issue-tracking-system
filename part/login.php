<?php
session_start();
require_once __DIR__ . '/../connect.php';
require_once "log_action.php"; // Adjust the path as necessary

// Path to the archived accounts file
$archivedAccountsFile = __DIR__ . '/../archived_accounts.json';

// Initialize archived accounts if the file exists
$archived_accounts = [];
if (file_exists($archivedAccountsFile)) {
    $archived_accounts = json_decode(file_get_contents($archivedAccountsFile), true);
}

// Redirect if the user is already logged in
if (isset($_SESSION['secured'])) {
    header('Location: ../index.php');
    exit();
}

$message = "";

// Check for logout message
if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1) {
    $message = "You have been logged out successfully.";
}

if (isset($_POST["submit"])) {
    $id = mysqli_real_escape_string($conn, $_POST["id"]); // Sanitize input
    $password = $_POST["password"];

    // Validate input
    if (empty($id) || empty($password)) {
        $message = "Please enter valid username and password";
    } else {
        // Prepare the query with parameter placeholders
        $query = "SELECT * FROM accounts WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);

        // Check if preparation was successful
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars(mysqli_error($conn)));
        }

        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check if the account is archived
            if (in_array($row["id"], $archived_accounts)) {
                $message = "This account is archived and cannot be used for login.";
            } else {
                // Verify password
                if (password_verify($password, $row["password"])) {
                    $user_type = $row["type"];

                    // Check if user is a student or faculty
                    if ($user_type === 'student' || $user_type === 'faculty') {
                        $message = "Student and Faculty accounts cannot log in.";
                    } else {
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);

                        // Store user information in session
                        $_SESSION['secured'] = $row["id"];
                        $_SESSION['user_type'] = $row["type"]; 
                        $_SESSION['last_activity'] = time(); // Set last activity time

                        // Log the successful login action
                        logAction($_SESSION['secured'], 'Successfully Logged in');
                        
                        // Redirect to dashboard or home page
                        header('Location: ../index.php');
                        exit();
                    }
                } else {
                    $message = "Invalid password";
                }
            }
        } else {
            $message = "Invalid username";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="../assets/css/style.bundle.css" rel="stylesheet">
    <style>
        body {
            background: url('../assets/media/downloads/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-form {
            background-color: rgba(255, 255, 255, 1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            height: 400px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        #buttonw{
            background-color: 00712D;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h3 class="text-center">Login</h3><hr>
        <!-- Display the message here -->
        <?php if (!empty($message)): ?>
            <div class="error-message" id="logoutMessage"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="id" name="id" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button style="background-color: #023020; color: whitesmoke;" type="submit" name="submit" id="buttonw" class="btn btn-success">Login</button>
            </div>
        </form>
    </div>

    <script src="../assets/js/scripts.bundle.js"></script>
    <script>
        window.onload = function() {
            var logoutMessage = document.getElementById('logoutMessage');
            if (logoutMessage) {
                // Hide the message after 3 seconds
                setTimeout(function() {
                    logoutMessage.style.display = 'none';
                }, 3000);  // Adjust the time in milliseconds (3000 ms = 3 seconds)
            }
        };
    </script>
</body>
</html>
