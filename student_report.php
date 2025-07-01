<?php

ob_start();


ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['secured']; 

require_once __DIR__ . '/connect.php'; 
require_once "part/log_action.php";

date_default_timezone_set('Asia/Manila');

// Fetch rooms, PC IDs, and issue natures from the database
$rooms = [];
$pc_ids = [];
$issue_natures = [];

// Get distinct rooms
$room_stmt = $conn->prepare("SELECT DISTINCT room FROM records_log ORDER BY room ASC");
$room_stmt->execute();
$room_stmt->bind_result($room);
while ($room_stmt->fetch()) {
    $rooms[] = $room;
}
$room_stmt->close();

// Get distinct PC IDs
$pc_stmt = $conn->prepare("SELECT DISTINCT pc_id FROM records_log ORDER BY pc_id ASC");
$pc_stmt->execute();
$pc_stmt->bind_result($pc_id);
while ($pc_stmt->fetch()) {
    $pc_ids[] = $pc_id;
}
$pc_stmt->close();

// Get issue natures
$nature_stmt = $conn->prepare("SELECT id, nature_of_issue FROM list_issue_nature ORDER BY nature_of_issue");
$nature_stmt->execute();
$nature_stmt->bind_result($issue_id, $nature_of_issue);
while ($nature_stmt->fetch()) {
    $issue_natures[] = ['id' => $issue_id, 'nature' => $nature_of_issue];
}
$nature_stmt->close();

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the response is JSON
    header('Content-Type: application/json');
    
    
    $room = $_POST['room'] ?? '';
    $pc_id = $_POST['pc_id'] ?? '';
    $description = $_POST['description'] ?? '';
    $issue_nature_id = $_POST['issue_nature'] ?? '';

    // Check if all required data is present
    if (empty($room) || empty($pc_id) || empty($description) || empty($issue_nature_id)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Proceed with inserting the issue report
    $current_year = date("Y");
    $current_month = date("F");
    $current_day = date("d");
    $current_time = date("H:i:s");

    $insert_stmt = $conn->prepare("INSERT INTO records_issue (user_id, room, pc_id, issue_nature_id, description, status, issue_year, issue_month, issue_day, issue_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $status = "Not Resolved";

    $insert_stmt->bind_param("ssssssssss", $user_id, $room, $pc_id, $issue_nature_id, $description, $status, $current_year, $current_month, $current_day, $current_time);

    if ($insert_stmt->execute()) {
        logAction($user_id, "Reported a problem from $pc_id with its description as '$description' in $room");  // Log the creation action
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error saving description: " . $conn->error]);
    }

    $insert_stmt->close();
    exit(); // Terminate the script after returning the response
}

// Close the connection to the database
$conn->close();

// End output buffering and send the output
ob_end_flush();
?>



<!-- HTML and CSS code remains the same as you provided -->



<style>
    /* Styling the form */
    .login-form {
        background-color: rgba(255, 255, 255, 1);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 100%;
        z-index: 1000;
        position: absolute; 
        top: 50%; 
        left: 50%; 
        transform: translate(-50%, -50%);
        border: solid black 1px;
    }

    h3 {
        font-family: 'Tahoma', sans-serif;
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    #button {
        background-color: #00712D;
        font-family: 'Tahoma', sans-serif;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }

    #button:hover {
        background-color: #00591B;
    }

    select, input {
        width: 100%;
        padding: 10px;
        margin-bottom: 16px;
        border: 1px solid gray;
        border-radius: 5px;
        font-size: 16px;
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .form-group {
        text-align: left; 
    }

    .form-group label {
        display: block;
        font-size: 16px;
    }
</style>

<div class="student_report" id="student_report">
    <div class="login-form">
        <h3>Report an Issue</h3><hr>
        <form action="student_report.php" method="POST" onsubmit="validateForm(event);" id="reportForm">

            <div class="form-row">
                <div class="form-group">
                    <label for="room" class="form-label">Room</label>
                    <select id="room" name="room" class="form-control" required>
                        <option value="" disabled>Select Room</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?php echo htmlspecialchars($room); ?>"><?php echo htmlspecialchars($room); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pc_id" class="form-label">PC Number</label>
                    <select id="pc_id" name="pc_id" class="form-control" required>
                        <option value="" disabled>Select PC Number</option>
                        <?php foreach ($pc_ids as $pc_id): ?>
                            <option value="<?php echo htmlspecialchars($pc_id); ?>"><?php echo htmlspecialchars($pc_id); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="issue_nature" class="form-label">Nature of Issue</label>
                <select id="issue_nature" name="issue_nature" class="form-control" required>
                    <option value="" disabled>Select Nature of Issue</option>
                    <?php foreach ($issue_natures as $nature): ?>
                        <option value="<?php echo htmlspecialchars($nature['id']); ?>"><?php echo htmlspecialchars($nature['nature']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <input type="text" id="description" name="description" class="form-control" required placeholder="Enter description">
            </div>

            <button type="submit" id="button">Submit</button>
        </form>
    </div>
</div>

<script>
    function validateForm(event) {
        event.preventDefault();

        var form = document.getElementById('reportForm');
        var formData = new FormData(form);

        fetch('student_report.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Report Submitted',
                    text: 'Your report has been successfully submitted!',
                });
                form.reset(); // Reset the form
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred while submitting the report.',
                });
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred.',
            });
        });
    }
</script>

</body>
</html>
