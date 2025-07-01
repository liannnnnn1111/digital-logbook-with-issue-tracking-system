<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../connect.php';
require_once "log_action.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log the received POST data
    error_log(print_r($_POST, true));

    // Get POST data from AJAX
    $last_name = $_POST['last_name'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $suffix = $_POST['suffix'] ?? '';
    $id = $_POST['id'] ?? '';



     $query = "SELECT type FROM accounts WHERE id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $id);  // Assuming user_id is an integer
        $stmt->execute();
        $stmt->bind_result($type);
        $stmt->fetch();
        $stmt->close();

        // If user type is found, use it, else fallback to a default value
        $type = $type ?: 'default';  //

    }

    // Validate required fields
    if (empty($id) || empty($last_name) || empty($first_name) || empty($birthday)) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit();
    }

    // Update query
    $query = "UPDATE accounts 
              SET last_name = ?, first_name = ?, middle_name = ?, birthday = ?, suffix = ?
              WHERE id = ?";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("ssssss", $last_name, $first_name, $middle_name, $birthday, $suffix, $id);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Student updated successfully.']);
        $user_id = $_SESSION['secured']; // Get the logged-in admin's ID
        logAction($user_id, "Successfully updating an account of $id from $type table"); // Log the creation action

        } else {
            echo json_encode(['error' => 'Error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error: ' . $conn->error]);
    }

    $conn->close();
}
?>

<style>
 .modal {
            display: none; /* Hide by default */
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            width: 100%;
            max-width: 100%; /* Maximum width for the modal */
            padding: 20px;
            border-radius: 8px;
        }

        .modal-dialog {
            width: 100%; /* Take full width of modal container */
            max-width: none;
        }

        /* Modal Content */
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
                  }




.modal-backdrop {
    display: none !important;
}

        /* Close Button */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 30px;
            cursor: pointer;

        }

        /* Form inputs */
        .modal input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Success/Error Messages */
           #updateResponse {
            display: block !important;
            visibility: visible !important;
        }


        /* Basic Button Styling */
        button {
            background-color: #006735; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }

        #updateModal .modal-content form input{
        padding: 5px;
            border: solid  1px gray;
            border-radius: 5px;
            margin-top: 5px;
            width: 100%;
            box-sizing: border-box;
        }
    </style>


<!-- Update Modal -->
<div id="updateModal" class="modal fade" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="close" id="closeUpdateModal" style="background-color:white; color: black;">&times;</button>
            <h2 style="text-align: center; color: black;">Update Student Information</h2>
            <p id="updateResponse" style="text-align: center;"></p> <!-- Response message area -->
            <form id="updateForm" class="update-form" method="POST" action="part/update_button.php">
                <input type="text" id="update-id" name="id" hidden>
                <label for="last_name">Last Name<span style="color:darkred;" class="required2">*</span></label>
                <input type="text" id="update-lastname" name="last_name" required>
                <label for="first_name">First Name<span style="color:darkred;" class="required2">*</span> </label>
                <input type="text" id="update-firstname" name="first_name" required>
                <label for="middle_name">Middle Name</label>
                <input type="text" id="update-middlename" name="middle_name">
                <label for="suffix">Suffix</label>
                <input type="text" id="update-suffix" name="suffix">
                <input type="hidden" id="update-type" name="type">
                <label for="birthday">Birthday (MM/DD/YYYY)<span style="color:darkred;" class="required2">*</span></label>
                <input type="text" id="update-birthday" name="birthday" required pattern="\d{2}/\d{2}/\d{4}">
                <input type="hidden" id="data-type" name="type">
                <button type="submit">Submit</button>

                <!-- Change Password Link -->
                <a href="javascript:void(0);" id="changePasswordLink" style="display: block; margin-top: 10px; text-align: center; color: #006735;">Change Password</a>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" id="closeChangePasswordModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <input type="hidden" id="change-password-id" name="id">
                    <label for="current-password">Current Password<span style="color:darkred;" class="required2">*</span></label>
                    <input type="password" id="current-password" name="current_password"  required >

                    <label for="new-password">New Password<span style="color:darkred;" class="required2">*</span></label>
                    <input type="password" id="new-password" name="new_password" required>

                    <label for="confirm-password">Confirm New Password<span style="color:darkred;" class="required2">*</span></label>
                    <input type="password" id="confirm-password" name="confirm_password" required>

                    <button type="submit">Change Password</button>
                </form>
                <p id="changePasswordResponse" style="text-align: center; color: red;"></p>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Update -->
<div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUpdateModalLabel">Confirm Update</h5>
                <button type="button" class="btn-close" id="closeConfirmModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update the student information?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelUpdate">Cancel</button>
                <button type="button" id="confirmUpdateBtn" class="btn btn-success"style= "background-color: #006735 !important;">Yes, Update</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Update Modal - Populate fields on show
  


    // Hide the "Change Password" link for student or faculty
  $(document).ready(function () {
    // Update Modal - Populate fields on show
    const updateModal = document.getElementById('updateModal');
    updateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const id = button.getAttribute('data-id');
        const lastname = button.getAttribute('data-lastname');
        const firstname = button.getAttribute('data-firstname');
        const middlename = button.getAttribute('data-middlename');
        const suffix = button.getAttribute('data-suffix');
        const birthday = button.getAttribute('data-birthday');
         const type = button.getAttribute('data-type');

if (type !== 'administrator' && type !== 'staff') {
    document.getElementById('changePasswordLink').style.display = 'none';
}


        // Populate the modal with the data
        document.getElementById('update-id').value = id;
        document.getElementById('update-lastname').value = lastname;
        document.getElementById('update-firstname').value = firstname;
        document.getElementById('update-middlename').value = middlename;
        document.getElementById('update-suffix').value = suffix;
        document.getElementById('update-birthday').value = birthday;
        document.getElementById('update-type').value = update;

        
    });

    // Handle the form submission with AJAX
    $('#updateForm').submit(function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Serialize the form data
        var formData = $(this).serialize();

        // Display the confirmation modal
        $('#confirmUpdateModal').modal('show');
    });

    // On confirming the update
    $('#confirmUpdateBtn').click(function () {
        // Close the main update modal
        $('#updateModal').modal('hide');
        // Close the confirmation modal
        $('#confirmUpdateModal').modal('hide');
         location.reload();

        // Serialize the form data
        var formData = $('#updateForm').serialize();

        // Send the data using AJAX to update the student
        $.ajax({
            type: 'POST',
            url: 'part/update_button.php', // URL to the PHP file that processes the request
            data: formData,
            success: function (response) {
                let responseObj = JSON.parse(response); // Parse the JSON response
                
                // Show success or error message in the modal
                if (responseObj.success) {
                    $('#updateResponse').html('<span style="color: green;">' + responseObj.success + '</span>').fadeIn();
                    setTimeout(function () {
                        $('#updateResponse').fadeOut();
                    }, 3000); // Hide after 3 seconds

          

                } else {
                    // Display error message in the modal
                    $('#updateResponse').html('<span style="color: red;">' + responseObj.error + '</span>').fadeIn();
                    setTimeout(function () {
                        $('#updateResponse').fadeOut();
                    }, 3000); // Hide after 3 seconds
                }
            },
            error: function (xhr, status, error) {
                $('#updateResponse').html('<span style="color: red;">An error occurred. Please try again later.</span>').fadeIn();
                setTimeout(function () {
                    $('#updateResponse').fadeOut();
                }, 3000); // Hide after 3 seconds
            }
        });
    });

    // On canceling the update, just close the confirmation modal
    $('#cancelUpdate').click(function () {
        $('#confirmUpdateModal').modal('hide');
    });

    // Attach the close function to the close button for the Update Modal
    $('#closeUpdateModal').click(function () {
        $('#updateModal').modal('hide');
    });
});

  $('#changePasswordLink').click(function () {
        $('#changePasswordModal').modal('show');
        const userId = $('#update-id').val();
        $('#change-password-id').val(userId);
    });

    // Handle Change Password form submission with AJAX (no confirmation modal)
    $('#changePasswordForm').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize(); // Serialize form data
        var newPassword = $('#new-password').val();
        var confirmPassword = $('#confirm-password').val();

        // Check if new password and confirm password match
        if (newPassword !== confirmPassword) {
            $('#changePasswordResponse').text('Passwords do not match!');
            return;
        }

        // Show loading message
        $('#changePasswordResponse').html('<span>Loading...</span>').show();

        $.ajax({
            type: 'POST',
            url: 'part/change_password.php', // URL to handle password change logic
            data: formData, // Send serialized form data
            success: function (response) {
                console.log(response);  // Debugging the raw response

                try {
                    let responseObj = JSON.parse(response);  // Parse the response

                    if (responseObj.success) {
                        alert('Change password successfully.');
                        $('#changePasswordResponse').html('<span style="color: green;">' + responseObj.success + '</span>');
                        $('#changePasswordModal').modal('hide');
                        $('#updateModal').modal('hide');
                        location.reload(); // Reload the page on success
                    } else {
                        $('#changePasswordResponse').html('<span style="color: red;">' + responseObj.error + '</span>');
                    }
                } catch (error) {
                    console.error("Error parsing JSON: ", error);
                    $('#changePasswordResponse').html('<span style="color: red;">An error occurred. Please try again.</span>');
                }
            },
            error: function () {
                $('#changePasswordResponse').html('<span style="color: red;">An error occurred. Please try again.</span>');
            }
        });
    });

    // Close Change Password Modal
    $('#closeChangePasswordModal').click(function () {
        $('#changePasswordModal').modal('hide');
    });


</script>




