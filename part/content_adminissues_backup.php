<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}
?>

<style>
    .issue-log-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 13px;
        text-align: center;
    }
    .issue-log-table th, .issue-log-table td {
        border: 1px solid #ddd;
        padding: 12px;
    }
    .issue-log-table th {
        background-color: white;
        color: black;
        cursor: pointer;
    }
    .issue-log-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .issue-log-table tr:hover {
        background-color: #f1f1f1;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .confirm-button {
        background-color: #006735; 
        color: white;             
        border: none;              
        padding: 7px 7px;       
        text-align: center;        
        text-decoration: none;     
        display: inline-block;     
        margin: 4px 2px;         
        cursor: pointer;          
        border-radius: 5px;    
    }
    .confirm-button:hover {
        background-color: #45a049; 
    }
    .selects {
        border: solid black;
        padding: 5px 5px;
        border-radius: 5px;
    }

    #cancelUpdate{
        background-color: white !important;
        color: black;
         padding: 7px 7px;       
        text-align: center;        
        text-decoration: none;     
        display: inline-block;     
        margin: 4px 2px;         
        cursor: pointer;          
        border-radius: 5px;    
        border: solid gray 1px;

    }
    #status-filter{
        background-color: white;
        color: black;
        padding: 5px;
        text-align: center;
        width: 150px;
    }
    #status-filter option{
        background-color: white;
        color: black;
    }

     .button-container2 {
        display: flex;
        justify-content: space-between;
        margin: 20px;

    }
     .button-container2 .button{
        padding:5px 20px;
        color: white;
        background-color:#006735 ;
        border: solid;
        border-radius: 7px;
        height: 40px;
    }
    .dropdown2-btn{
         padding:5px 20px;
        color: white;
        background-color:#006735 ;
        border: solid;
        border-radius: 7px;
        height: 40px;
    }
     @media print {
                .no-print {
                    display: none;
                }
            }
/* Initially hide the dropdown */


.button-container2 {
    display: flex;
    align-items: center;
    justify-content: space-between; 
    margin: 20px;
}

.filter-search-container {
    display: flex;
    align-items: center;
    gap: 15px; /* Minimal space between the filter icon and search bar */
}

.search-bar-container input {
    padding: 5px;
    font-size: 14px;
    width: 200px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.dropdown2 {
    position: relative; /* Needed for dropdown positioning */
    display: flex;
    align-items: center;
}

.dropdown2-btn {
    background-color: white;
    border: 1px solid white;
    border-radius: 4px;
    padding: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 36px; /* Matches the height of the search bar */
    width: 36px; /* Square button for the icon */
}

.dropdown2-content {
    position: absolute;
    top: 110%; /* Dropdown below the button */
    left: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 5px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); 
    display: none; /* Initially hidden */
    z-index: 10;
    border-radius: 4px;
}

.dropdown2:hover .dropdown2-content {
    display: block; /* Show dropdown on hover */
}

.dropdown2-content select {
    width: 150px;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.button {
    background-color: #007bff;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.button:hover {
    background-color: #0056b3;
}
</style>

<div id="issue_ticket">
    <div class="button-container2">
        <!-- Filter and Search Section -->
        <div class="filter-search-container">
            <!-- Filtering Dropdown with Clickable Button -->
            <div class="search-bar-container">
                <input type="text" id="search-bar" placeholder="Search" onkeyup="searchTable()">
            </div>
            <div class="dropdown2">
                <button class="dropdown2-btn">
                    <i style="color: black;" class="bi bi-funnel-fill"></i>
                </button>
                <div class="dropdown2-content">
                    <select id="status-filter" onchange="applyFilter()">
                        <option value="All">All</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Not Resolved">Not Resolved</option>
                    </select>
                </div>
            </div>

            <!-- Search bar -->
            
        </div>

        <!-- Print button -->
        <button class="button" onclick="printPage()">Print This Page</button>
    </div>
</div>






<!-- Modal Structure -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h5>Confirm Status Update</h5>
        <p id="modalMessage"></p>
        <label for="remarksInput">Remarks</label>
        <textarea id="remarksInput" placeholder="Enter remarks here..." required></textarea>
        <br>
        <button id="confirmUpdate" class="confirm-button">Confirm</button>
        <button id="cancelUpdate" class="confirm-button">Cancel</button>
    </div>
</div>


    <table class='issue-log-table' id='issue_log_table'>
        <thead>
            <tr>
                <th onclick="sortTable11('id')">ID <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('issue_date')">Timestamp <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('user_id')">User ID <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('room')">Room <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('pc_id')">PC ID <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('nature_of_issue')">Nature of Issue <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('description')">Description <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('status')">Status <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('remarks')">Remarks <i class='fas fa-sort'></i></th>
                <th onclick="sortTable11('status_remarks_timestamp')">Remarks Timestamp <i class='fas fa-sort'></i></th>
            </tr>
        </thead>
        <tbody>
         
            <?php
          
            ?>
        </tbody>
    </table>
</div>

<script>
let selectedId;
let selectedStatus;
let previousStatus;
let filterBy = 'status'; // Default filter by 'status'
let filterValue = 'All'; // Default filter value

$(document).ready(function() {
    loadTableData(); // Initially load all data when the page loads

    // Show the modal when the status is changed
    $(document).on('change', '.selects', function() {
        selectedId = $(this).attr('id').split('_')[1]; // Get the ID from the dropdown ID
        selectedStatus = $(this).val(); // Get the selected status
        previousStatus = $(this).data('previous'); // Store the previous status

        const message = `Are you sure you want to change the status to "${selectedStatus}"?`;
        $('#modalMessage').text(message);
        $('#statusModal').show();

        // Prevent immediate change
        $(this).val(previousStatus);
    });

    // Confirm the update on modal button click
    $('#confirmUpdate').click(function() {
        const remarks = $('#remarksInput').val().trim();
        if (selectedStatus === 'Resolved' && remarks === '') {
            alert('Please enter remarks before confirming your actions.');
            return;
        }

        const currentTimestamp = getCurrentManilaTime(); // Get current time in Manila
        updateStatus(selectedId, selectedStatus, remarks, currentTimestamp); // Include timestamp

        closeModal();
    });

    // Close the modal on cancel or close button click
    $('#cancelUpdate, #closeModal').click(function() {
        closeModal();
    });

    // Apply filter when the status dropdown is changed
    $('#status-filter').change(function() {
        filterValue = $(this).val(); // Get the selected status (Resolved, Not Resolved, All)
        filterData11('status', filterValue); // Call the filtering function
    });
});

// Function to update the status
function updateStatus(id, newStatus, remarks, timestamp) {
    $.ajax({
        url: 'part/update_status.php',
        type: 'POST',
        data: { id: id, status: newStatus, remarks: remarks, timestamp: timestamp }, // Send timestamp
        dataType: 'json',
        success: function(response) {
            alert(response.message);
            loadTableData(); // Reload the table data after update
        },
        error: function(xhr, status, error) {
            console.error('AJAX error: ' + error);
            alert('Error updating status');
        }
    });
}

// Function to get current time in Manila
function getCurrentManilaTime() {
    const options = {
        timeZone: 'Asia/Manila',
        year: 'numeric',
        month: 'long', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false 
    };

    const formatter = new Intl.DateTimeFormat('en-US', options);
    const dateParts = formatter.formatToParts(new Date());
    
    // Extract the parts we want
    const year = dateParts.find(part => part.type === 'year').value;
    const month = dateParts.find(part => part.type === 'month').value; 
    const day = dateParts.find(part => part.type === 'day').value;
    const hour = dateParts.find(part => part.type === 'hour').value;
    const minute = dateParts.find(part => part.type === 'minute').value;
    const second = dateParts.find(part => part.type === 'second').value;

    return `${day} ${month} ${year} ${hour}:${minute}:${second}`;
}

// Function to load table data with optional filter and sort
function loadTableData() {
    $.ajax({
        url: 'part/update_status.php',
        type: 'GET',
        data: {
            filter_by: filterBy,
            filter_value: filterValue
        },
        success: function(data) {
            $('#issue_log_table tbody').html(data); // Inject the returned data into the table body
        }
    });
}

// Function to filter data based on column and value
function filterData11(column, value) {
    filterBy = column; // Set filter column (default is 'status')
    filterValue = value || 'All'; // Set filter value (default is 'All')

    // Reload data with filter parameters
    $.ajax({
        url: 'part/update_status.php',
        type: 'GET',
        data: {
            filter_by: filterBy, // 'status'
            filter_value: filterValue || 'All' // 'Resolved', 'Not Resolved', 'All'
        },
        success: function(data) {
            $('#issue_log_table tbody').html(data); // Update the table body with filtered data
        }
    });
}

// Function to sort the table data by column after applying filter
function sortTable11(column) {
    const currentOrder = $('#issue_log_table').data('order11') || 'asc'; // Default to ascending order
    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc'; // Toggle sorting order

    // Store sorting details in the table
    $('#issue_log_table').data('order11', newOrder);
    $('#issue_log_table').data('sort_by11', column);

    // Reload data with sorting, filter, and order
    $.ajax({
        url: 'part/update_status.php',
        type: 'GET',
        data: {
            sort_by11: column,  // Column to sort by
            order11: newOrder,  // Sorting order: 'asc' or 'desc'
            filter_by: filterBy, // Current filter column
            filter_value: filterValue // Current filter value
        },
        success: function(data) {
            $('#issue_log_table tbody').html(data); // Inject the sorted data into the table body
        }
    });
}

// Close modal function
function closeModal() {
    $('#statusModal').hide();
    $('#remarksInput').val(''); // Reset remarks input field
}
 function printPage() {
        window.print();
    }
    // Function to toggle the visibility of the dropdown
function toggleDropdown() {
    const dropdown = document.getElementById("status-filter");
    // Check if the dropdown is already visible
    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";  // Show the dropdown
    } else {
        dropdown.style.display = "none";   // Hide the dropdown
    }
}


// Function to search through the table columns (case-insensitive and partial match)
function searchTable() {
    const input = document.getElementById("search-bar");          // Get the search input
    const filter = input.value.trim().toLowerCase();              // Trim spaces and convert to lowercase
    const table = document.getElementById("issue_log_table");      // Get the table
    const rows = table.getElementsByTagName("tr");                 // Get all rows in the table

    // Loop through all table rows (skip the header row, which is at index 0)
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName("td");          // Get all cells in the current row
        let matchFound = false;

        // Loop through each cell in the row and check if it contains the search term
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            // Trim cell content and compare it with the search term
            if (cell && cell.innerText.trim().toLowerCase().includes(filter)) {
                matchFound = true;  // If any cell contains the search term, mark as found
                break;               // Stop checking other cells if one match is found
            }
        }

        // If a match is found, show the row; otherwise, hide it
        if (matchFound) {
            rows[i].style.display = "";  // Show row
        } else {
            rows[i].style.display = "none";  // Hide row
        }
    }
}



</script>
