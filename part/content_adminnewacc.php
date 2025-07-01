<?php
// Connect to the database
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>



      <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .newstud-log-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            text-align: center;
        }
        .newstud-log-table th, .newstud-log-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        .register-btn {
            margin: 20px;
            padding: 10px 20px;
            background-color: #00712D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color:  white !important;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
            color: black !important;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }
        .close {
            color: #fff;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .login-form label {
            color: black;
            margin-top: 2px;
        }
       #registerModal .modal-content #registrationForm input, select {
            padding: 5px;
            border: solid  1px gray;
            border-radius: 5px;
            margin-top: 5px;
            width: 100%;
            box-sizing: border-box;
            

        }
        button[type="submit"] {
            background-color:#006735 ;
            border: 1px solid white;
            color: white;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            width: 100%;
        }
        #newstud_log_table th, #newfac_log_table th, #newstaff_log_table th, #newadmin_log_table th{
            background-color:white;
            color: black;
        }
      .modal .login-form label .required2 {
    color: #AA4A44 !important; 
}

 
 @media print {
                .no-print {
                    display: none;
                }
            }

    .button-container .button-container2 {
        display: flex;
        justify-content: space-between;
        margin: 20px;
    }

  .button-container .button {
    padding: 5px 20px;
    color: white;
    background-color: #006735;
    border: solid;
    border-radius: 7px;
    height: 40px;
    margin: 20px;
}
.button-container .button-container2 .register-btn{
    padding: 5px 20px;
    color: white;
    background-color: #006735;
    border: solid;
    border-radius: 7px;
    height: 40px;
    margin: 20px;
}
 @media print {
                .no-print {
                    display: none;
                }
            }




    </style>

<?php include 'update_button.php'; ?>
<?php include 'archive_frontend.php'; ?>






<?php if ($section === 'student_acc') : ?>
<section id="student_acc">
      <div class="button-container">
        <div class="button-container2">
        <button class="register-btn" id="registerBtnStudent">Register Account</button>
         <button class="button" onclick="printPage()">Print This Page</button>
    </div>
</div>


   <table class='newstud-log-table' id='newstud_log_table'>
    <thead>
        <tr>
            <th onclick="sortTable6('student', 'id', 'sort_by6', 'order6')">User ID <i class="fas fa-sort"></i></th>
            <th onclick="sortTable6('student', 'last_name', 'sort_by6', 'order6')">Last Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable6('student', 'first_name', 'sort_by6', 'order6')">First Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable6('student', 'middle_name', 'sort_by6', 'order6')">Middle Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable6('student', 'suffix', 'sort_by6', 'order6')">Suffix <i class="fas fa-sort"></i></th>
            <th onclick="sortTable6('student', 'birthday', 'sort_by6', 'order6')">Birthday <i class="fas fa-sort"></i></th>
            <th>Action</th>
       



        </tr>
    </thead>
    <tbody>
        <!-- Student table rows will be inserted here by AJAX -->
     
    </tbody>
</table>




</section>
<?php endif; ?>
<?php if ($section === 'faculty_acc') : ?>
<section id="faculty_acc">
      <div class="button-container">
    <div class="button-container2">
    <button class="register-btn" id="registerBtnFaculty">Register Account</button>
    <button class="button" onclick="printPage()">Print This Page</button>
</div>
</div>
    <table class='newstud-log-table' id='newfac_log_table'>
        <thead>
        <tr>
            <th onclick="sortTable5('faculty', 'user_id', 'sort_by5', 'order5')">User ID <i class="fas fa-sort"></i></th>
            <th onclick="sortTable5('faculty', 'last_name', 'sort_by5', 'order5')">Last Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable5('faculty', 'first_name', 'sort_by5', 'order5')">First Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable5('faculty', 'middle_name', 'sort_by5', 'order5')">Middle Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable5('faculty', 'suffix', 'sort_by5', 'order5')">Suffix <i class="fas fa-sort"></i></th>
            <th onclick="sortTable5('faculty', 'birthday', 'sort_by5', 'order5')">Birthday <i class="fas fa-sort"></i></th>
            <th>Action</th>
           
        </tr>
        </thead>
        <tbody>
            <!-- Faculty table rows will be inserted here by AJAX -->
         
        </tbody>
    </table>
<!-- Delete Modal -->


</section>
<?php endif; ?>
<?php if ($section === 'staff_acc') : ?>
<section id="staff_acc">
    <div class="button-container">
    <div class="button-container2">
    <button class="register-btn" id="registerBtnStaff">Register Account</button>
    <button class="button" onclick="printPage()">Print This Page</button>
</div>
</div>

    <table class='newstud-log-table' id='newstaff_log_table'>
        <thead>
        <tr>
            <th onclick="sortTable3('staff', 'user_id', 'sort_by3', 'order3')">User ID <i class="fas fa-sort"></i></th>
            <th onclick="sortTable3('staff', 'last_name', 'sort_by3', 'order3')">Last Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable3('staff', 'first_name', 'sort_by3', 'order3')">First Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable3('staff', 'middle_name', 'sort_by3', 'order3')">Middle Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable3('staff', 'suffix', 'sort_by3', 'order3')">Suffix <i class="fas fa-sort"></i></th>
            <th onclick="sortTable3('staff', 'birthday', 'sort_by3', 'order3')">Birthday <i class="fas fa-sort"></i></th>
            <th>Action</th>
         
        </tr>
        </thead>
        <tbody>
            <!-- Staff table rows will be inserted here by AJAX -->
             
        </tbody>
    </table>



</section>
<?php endif; ?>
<?php if ($section === 'admin_acc') : ?>
<section id="admin_acc">
     <div class="button-container">
    <div class="button-container2">
    <button class="register-btn" id="registerBtnAdmin">Register Account</button>
    <button class="button" onclick="printPage()">Print This Page</button>
</div>
</div>


    <table class='newstud-log-table' id='newadmin_log_table'>
        <thead>
        <tr>
            <th onclick="sortTable4('administrator', 'user_id', 'sort_by4', 'order4')">User ID <i class="fas fa-sort"></i></th>
            <th onclick="sortTable4('administrator', 'last_name', 'sort_by4', 'order4')">Last Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable4('administrator', 'first_name', 'sort_by4', 'order4')">First Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable4('administrator', 'middle_name', 'sort_by4', 'order4')">Middle Name <i class="fas fa-sort"></i></th>
            <th onclick="sortTable4('administrator', 'suffix', 'sort_by4', 'order4')">Suffix <i class="fas fa-sort"></i></th>
            <th onclick="sortTable4('administrator', 'birthday', 'sort_by4', 'order4')">Birthday <i class="fas fa-sort"></i></th>
            <th>Action</th>
          
        </tr>
        </thead>
        <tbody>
            <!-- Admin table rows will be inserted here by AJAX -->

        
        </tbody>
    </table>
   <!-- Modal for confirmation -->



</section>
<?php endif; ?>

<!-- Registration Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 style="text-align: center; color: black;">Registration Form</h2>
        <p style="text-align: center;" id="responseMessage"></p>
        <form id="registrationForm" class="login-form" method="POST" action="part/register_accounts.php">
            <label for="id">User ID <span class="required2"> *</span></label>
            <input type="text" id="id" name="id" required>
            <label for="last_name">Last Name <span class="required2">*</span></label>
            <input type="text" id="last_name" name="last_name" required>
            <label for="first_name">First Name <span class="required2">*</span></label>
            <input type="text" id="first_name" name="first_name" required>
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name">
            <label for="suffix">Suffix</label>
            <input type="text" id="suffix" name="suffix">
            <label for="birthday">Birthday (MM/DD/YYYY) <span class="required2">*</span></label>
            <input type="text" id="birthday" name="birthday" required pattern="\d{2}/\d{2}/\d{4}">
            <label for="options">User Type <span class="required2">*</span></label>
            <select id="options" name="options" required>
                <option value="">Choose</option>
                <option value="faculty">Faculty</option>
                <option value="administrator">Administrator</option>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
            </select>
            <div id="passwordFields" style="display: none;">
                <label for="password">Password <span class="required2">*</span></label>
                <input type="password" id="password" name="password" required>
                <label for="confirm_password">Password Confirmation <span class="required2">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="text-align:center;">Are you sure you want to delete this account?</p>
                <div id="accountDetails" style="display:none !important">
                    <p id="accountlastname"></p>
                    <p id="accountfirstname"></p>
                    <p id="type"></p>
                </div>
                <form id="deleteForm" method="POST" style="display: none;">
                    <input type="hidden" name="id" id="delete-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="passwordVerificationModal" tabindex="-1" aria-labelledby="passwordVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordVerificationModalLabel">Confirm Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please enter your password to confirm the deletion of this account.</p>

                <!-- Account Details (Visible now) -->
                <div id="accountDetails" style="display:none;">
                    <p><strong>Last Name:</strong> <span id="accountlastname"></span></p>
                    <p><strong>First Name:</strong> <span id="accountfirstname"></span></p>
                    <p><strong>Type:</strong> <span id="type"></span></p>
                </div>

                <!-- Password Input -->
                <input style=" padding: 5px;  border: solid  1px gray;  border-radius: 5px;  margin-top: 5px; width: 100%;
            box-sizing: border-box;" type="password" class="form-control" id="userPassword" placeholder="Enter your password">
                <div id="passwordError" class="text-danger" style="display: none;">Incorrect password. Please try again.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmPassword" class="btn btn-danger">Verify Password</button>
            </div>
        </div>
    </div>
</div>









<script>
$(document).ready(function() {
    const loadTableData = (url, tableId, sortBy, order) => {
        $.ajax({
            url: url,
            type: 'GET',
            data: { 
                [`sort_by${getSortIndex(tableId)}`]: sortBy,
                [`order${getSortIndex(tableId)}`]: order
            },
            success: function(data) {
                $(tableId + ' tbody').html(data);
                updateSortIcons(tableId, sortBy, order);
            }
        });
    };



    function getSortIndex(tableId) {
        switch(tableId) {
            case '#newstud_log_table': return 6; 
            case '#newfac_log_table': return 5; 
            case '#newstaff_log_table': return 3; 
            case '#newadmin_log_table': return 4; 
            default: return 1; 
        }
    }

    const sortStates = {
        student: { column: 'id', order: 'desc' },
        faculty: { column: 'id', order: 'desc' },
        staff: { column: 'id', order: 'desc' },
        administrator: { column: 'id', order: 'desc' },
    };

    loadTableData('part/fetch_newstudacc.php', '#newstud_log_table', sortStates.student.column, sortStates.student.order);
    loadTableData('part/fetch_newfacultyacc.php', '#newfac_log_table', sortStates.faculty.column, sortStates.faculty.order);
    loadTableData('part/fetch_newstaffacc.php', '#newstaff_log_table', sortStates.staff.column, sortStates.staff.order);
    loadTableData('part/fetch_newadminacc.php', '#newadmin_log_table', sortStates.administrator.column, sortStates.administrator.order);

    window.sortTable6 = function(type, column) {
        const sortData = sortStates[type];
        sortData.order = (sortData.column === column && sortData.order === 'desc') ? 'desc' : 'asc';
        sortData.column = column;
        loadTableData('part/fetch_newstudacc.php', '#newstud_log_table', sortData.column, sortData.order);
    };

    window.sortTable5 = function(type, column) {
        const sortData = sortStates[type];
        sortData.order = (sortData.column === column && sortData.order === 'desc') ? 'desc' : 'asc';
        sortData.column = column;
        loadTableData('part/fetch_newfacultyacc.php', '#newfac_log_table', sortData.column, sortData.order);
    };

    window.sortTable3 = function(type, column) {
        const sortData = sortStates[type];
        sortData.order = (sortData.column === column && sortData.order === 'desc') ? 'desc' : 'asc';
        sortData.column = column;
        loadTableData('part/fetch_newstaffacc.php', '#newstaff_log_table', sortData.column, sortData.order);
    };

    window.sortTable4 = function(type, column) {
        const sortData = sortStates[type];
        sortData.order = (sortData.column === column && sortData.order === 'desc') ? 'desc' : 'asc';
        sortData.column = column;
        loadTableData('part/fetch_newadminacc.php', '#newadmin_log_table', sortData.column, sortData.order);
    };

    function updateSortIcons(tableId, sortBy, order) {
        $(tableId + ' th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        const header = $(tableId + ' th').filter((_, el) => $(el).text().trim().toLowerCase() === sortBy.toLowerCase());
        header.find('i').removeClass('fa-sort').addClass(order === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
    }

 $(document).ready(function() {
    const modal = document.getElementById("registerModal");
    const buttons = [];

    // Push button IDs into the array based on the section
    <?php if ($section === 'student_acc') : ?>
        buttons.push("registerBtnStudent");
    <?php endif; ?>
    <?php if ($section === 'faculty_acc') : ?>
        buttons.push("registerBtnFaculty");
    <?php endif; ?>
    <?php if ($section === 'staff_acc') : ?>
        buttons.push("registerBtnStaff");
    <?php endif; ?>
    <?php if ($section === 'admin_acc') : ?>
        buttons.push("registerBtnAdmin");
    <?php endif; ?>

    buttons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.onclick = function() {
                modal.style.display = "block";
            };
        } else {
            console.error(`Button with ID ${buttonId} not found.`);
        }
    });

    const span = document.getElementsByClassName("close")[0];
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        };
    } else {
        console.error("Close button not found.");
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});

$(document).ready(function() {
    // Toggle password fields visibility based on user type selection
    $('#options').on('change', function() {
        var userType = this.value;
        var passwordFields = $('#passwordFields');
        
        // Show password fields for 'administrator' and 'staff', hide otherwise
        if (userType === 'administrator' || userType === 'staff') {
            passwordFields.show();
            // Make password fields required for 'administrator' and 'staff'
            $('#password').attr('required', 'required');
            $('#confirm_password').attr('required', 'required');
        } else {
            passwordFields.hide();
            // Remove the required attribute when hidden (for 'student' and 'faculty')
            $('#password').removeAttr('required');
            $('#confirm_password').removeAttr('required');
        }
    });

    // Handle form submission
   $('#registrationForm').on('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    const userType = $('#options').val();
    
    // If the user type is 'administrator' or 'staff', validate password
    if (userType === 'administrator' || userType === 'staff') {
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        // Check if both password fields are filled out
        if (!password || !confirmPassword) {
            $('#responseMessage').text('Please fill out both password fields.').css('color', 'red');
            return; // Don't submit the form if password fields are empty
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            $('#responseMessage').text('Passwords do not match.').css('color', 'red');
            return; // Don't submit the form if passwords don't match
        }

        // Check if password meets length requirement
        if (password.length < 8) {
            $('#responseMessage').text('Password must be at least 8 characters long.').css('color', 'red');
            return; // Don't submit the form if password is too short
        }

        // Check if password has at least one uppercase letter, one number, and one special character
        const hasUppercase = /[A-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecialChar = /[\W_]/.test(password);
        
        if (!hasUppercase || !hasNumber || !hasSpecialChar) {
            $('#responseMessage').text('Password must contain at least one uppercase letter, one number, and one special character.').css('color', 'red');
            return; // Don't submit the form if password doesn't meet the criteria
        }
    }

    // Serialize the form data
    const formData = $(this).serialize();

    // Send AJAX request
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formData,
        success: function(response) {
            try {
                const jsonResponse = JSON.parse(response); // Parse JSON response

                $('#responseMessage').text(jsonResponse.message);
                if (jsonResponse.success) {
                    $('#responseMessage').css('color', 'green');
                    alert('Account created successfully.');
                    location.reload();
                } else {
                    $('#responseMessage').css('color', 'red');
                }

                // Reset form after submission
                $('#registrationForm')[0].reset();

                // Load different tables based on user type
                let tableId = '';
                if (userType === 'faculty') {
                    tableId = '#newfac_log_table';
                } else if (userType === 'stud') {
                    tableId = '#newstud_log_table';
                } else if (userType === 'staff') {
                    tableId = '#newstaff_log_table';
                } else if (userType === 'admin') {
                    tableId = '#newadmin_log_table';
                }

                if (tableId) {
                    loadTableData(`part/fetch_new${userType}acc.php`, tableId, 'desc', 'asc');
                }

            } catch (error) {
                $('#responseMessage').text('Error parsing response: ' + error.message).css('color', 'red');
            }
        },
        error: function(xhr, status, error) {
            $('#responseMessage').css('color', 'red');
            $('#responseMessage').text('An error occurred: ' + error);
        }
    });
});


});

});




 function printPage() {
        window.print();
    }
// Listen for when the modal is opened (when the user clicks "Delete" button)
$(document).ready(function () {
        // When the delete confirmation modal is triggered, get the account details
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var accountId = button.data('id'); // Get account ID
            var accountlastname = button.data('last_name'); // Get account last name
            var accountfirstname = button.data('first_name');
            var type = button.data('type');  // Get account first name

            // Populate the modal with account details
            $('#accountlastname').text(accountlastname);
            $('#accountfirstname').text(accountfirstname);
            $('#type').text(type);
            $('#delete-id').val(accountId); // Set the account ID in the hidden form
        });

        // When the user clicks on Confirm Delete
        $('#confirmDelete').on('click', function () {
            var accountId = $('#delete-id').val(); // Get account ID from hidden input

            if (!accountId) {
                alert("Missing account details. Please check.");
                return;
            }

            // Close the first modal and show the password verification modal
            $('#deleteModal').modal('hide');
            $('#passwordVerificationModal').modal('show');
        });

        // Handle password verification logic
        $('#confirmPassword').on('click', function () {
            var enteredPassword = $('#userPassword').val(); // Get the entered password

            // Send AJAX request to verify password
            $.ajax({
                url: 'part/verify_password.php', // PHP file to verify password
                type: 'POST',
                data: {
                    password: enteredPassword // Send the entered password for verification
                },
                success: function (response) {
                    if (response === 'success') {
                        // If password is correct, proceed to delete the account
                        var accountId = $('#delete-id').val(); // Get account ID from hidden input

                        $.ajax({
                            url: 'part/delete_account.php', // PHP file to delete account
                            type: 'POST',
                            data: {
                                id: accountId // Send account ID to delete
                            },
                            success: function (response) {
                                if (response === 'success') {
                                    $('#passwordVerificationModal').modal('hide'); // Close the password modal
                                    alert('Account deleted successfully.');
                                    location.reload(); // Reload the page to reflect changes
                                } else {
                                    alert('Error deleting account: ' + response);
                                }
                            }
                        });
                    } else {
                        // Show error if password is incorrect
                        $('#passwordError').show();
                    }
                }
            });
        });

    });





</script>



