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
    .faculty-log-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 13px;
        text-align: center;
        color: black !important;
    }
    .faculty-log-table th, .faculty-log-table td {
        border: 1px solid #ddd;
        padding: 12px;
    }
    .faculty-log-table tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    .faculty-log-table th {
        background-color: white;
        color: black;
    }
    .dropdown2 { position: relative; display: inline-block; }
    .dropdown2-btn { color: white; padding: 10px 20px; border: solid; border-radius: 7px; cursor: pointer; }
    .dropdown2-content {
        display: none;
        position: absolute;
        background-color: whitesmoke;
        min-width: 200px;
        max-height: 300px; 
        overflow-y: auto; 
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.9);
        z-index: 1;
    }
    .dropdown2-content h3 { margin: 0; padding: 10px; background-color: whitesmoke; text-align:center; }
    .dropdown2-content label { color: black; padding: 12px 16px; display: block; text-align: center;}
    .dropdown2-content input { margin: 0 5px; vertical-align: middle; }
    .dropdown2:hover .dropdown2-content { display: block; }

    @media print {
                .no-print {
                    display: none;
                }
            }

  
    .button-container .button{
        padding:5px 20px;
        color: white;
        background-color:#006735 ;
        border: solid;
        border-radius: 7px;
        height: 40px;
    }

        .button-container .dropdown2-btn{
        padding:5px 20px;
        color: white;
        background-color:#006735 ;
        border: solid;
        border-radius: 7px;
        height: 40px;
    }



.button-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 20px;
}

.search-dropdown-container {
    display: flex;
    align-items: center;
    gap: 5px; /* Smaller gap between search bar and icon */
    position: relative;
}

/* Style the search bar */
.search-bar-container input {
    padding: 5px;
    width: 200px;
    box-sizing: border-box;
    margin: 0;
    border: 1px solid gray;
    border-radius: 4px;
}

/* Filter dropdown button */
.dropdown2-btn {
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 5px;
    cursor: pointer;
}

/* Dropdown menu content */
.dropdown2-content {
    position: absolute;
    top: 110%; /* Slightly below the button */
    left: 0;
    background-color: #f9f9f9;
    min-width: 200px; /* Match the width of the button */
    display: none;
    z-index: 1;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    padding: 10px;
    border-radius: 4px;
}

/* Show dropdown menu on hover */
.dropdown2:hover .dropdown2-content {
    display: block;
}

/* Print button */
.button {
    background-color: #007bff;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: auto; /* Push print button to the far right */
}

.button:hover {
    background-color: #0056b3;
}
</style>

<div id="faculty_log" data-order12="asc" data-sort_by12="log_time" data-filter_by12="">
    <div class="button-container">
        <!-- Search and dropdown icon side by side -->
        <div class="search-dropdown-container">
            <!-- Search bar -->
            <div class="search-bar-container">
                <input type="text" id="search-bar" placeholder="Search" onkeyup="searchTable()">
            </div>

            <!-- Filter dropdown button with icon -->
            <div class="dropdown2">
                <button style="background-color:white !important;" class="dropdown2-btn">
                    <i class="bi bi-funnel-fill" style="color: black;"></i>
                </button>
                <div class="dropdown2-content">
                    <h3>Filter by User</h3>
                    <div id="type_filter12"></div>
                    <h3>Filter by Room</h3>
                    <div id="room_filter12"></div>
                    <h3>Filter by Date</h3>
                    <div id="date_filter12"></div>
                    <h3>Filter by Subject</h3>
                    <div id="subject_filter12"></div>
                    <h3>Filter by Faculty</h3>
                    <div id="faculty_filter12"></div>
                </div>
            </div>
        </div>

        <!-- Print button -->
        <button class="button" onclick="printPage()">Print This Page</button>
    </div>
</div>




    <table class='faculty-log-table' id='faculty_log_table'>
        <thead>
            <tr>
                <th onclick="sortTable12('id')">ID <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('dates')">Timestamp <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('pc_id')">PC ID <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('room')">Room <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('user_id')">User ID <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('subject')">Faculty Subject <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable12('faculty')">Faculty  <i class="fas fa-sort sort-icon"></i></th>

            </tr>
        </thead>
        <tbody>
            <!-- Table rows will be dynamically inserted here by AJAX -->
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    loadTableData12();  
    loadFilters12();    

    $(document).on('change', 'input[type="checkbox"]', function() {
        filterData12();
    });
});

// Function to load table data with selected filters
function loadTableData12(filters = {}) {
    $.ajax({
        url: 'part/fetch_user_log.php',
        type: 'GET',
        data: filters,
        success: function(data) {
            $('#faculty_log_table tbody').html(data);
        },
        error: function() {
            alert('Error fetching data.');
        }
    });
}

function loadFilters12() {
    $.ajax({
        url: 'part/fetch_filters.php',
        type: 'GET',
        success: function(data) {
            const filters = JSON.parse(data);
            filters.type.forEach(type => {
                $('#type_filter12').append(`<label><input type="checkbox" class="type-filter" value="${type.type}">${type.type}</label>`);
            });
            filters.room.forEach(room => {
                $('#room_filter12').append(`<label><input type="checkbox" class="room-filter" value="${room.room}">${room.room}</label>`);
            });
            filters.dates.forEach(date => {
                $('#date_filter12').append(`<label><input type="checkbox" class="dates-filter" value="${date.dates}">${date.dates}</label>`);
            });
            filters.subject.forEach(subject => {
            $('#subject_filter12').append(`<label><input type="checkbox" class="subject-filter" value="${subject.subject}">${subject.subject}</label>`);
        });
             filters.faculty.forEach(faculty => {
            $('#faculty_filter12').append(`<label><input type="checkbox" class="faculty-filter" value="${faculty.faculty}">${faculty.faculty}</label>`);
        });

            
        }
    });
}

function sortTable12(column) {
    var order12 = $('#faculty_log').data('order12') === 'asc' ? 'desc' : 'asc';
    $('#faculty_log').data('order12', order12);
    $('#faculty_log').data('sort_by12', column); 
    filterData12();
}

function filterData12() {
    let currentFilters = {
        type: [],
        room: [],
        dates: [],
        subject: [],
        faculty: [],
        sort_by12: $('#faculty_log').data('sort_by12') || 'log_time',
        order12: $('#faculty_log').data('order12') || 'asc'
    };

    $('input.type-filter:checked').each(function() {
        currentFilters.type.push($(this).val());
    });
    $('input.room-filter:checked').each(function() {
        currentFilters.room.push($(this).val());
    });
    $('input.dates-filter:checked').each(function() {
        currentFilters.dates.push($(this).val());
    });
     $('input.subject-filter:checked').each(function() {
        currentFilters.subject.push($(this).val());
    });
   $('input.faculty-filter:checked').each(function() {
        currentFilters.faculty.push($(this).val());
    });
  
  
    let filters = {};
    const filterKeys = ['type', 'room', 'dates','subject','faculty'];
    filterKeys.forEach(key => {
        if (currentFilters[key].length > 0) {
            filters[`filter_by12[]`] = key; 
            filters[`filter_value12[]`] = currentFilters[key].join('|'); 
        }
    });

    filters['sort_by12'] = currentFilters.sort_by12;
    filters['order12'] = currentFilters.order12;

    loadTableData12(filters);
}
 function printPage() {
        window.print();
    }

    function searchTable() {
    const input = document.getElementById("search-bar");          // Get the search input
    const filter = input.value.trim().toLowerCase();              // Trim spaces and convert to lowercase
    const table = document.getElementById("faculty_log_table");      // Get the table
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
