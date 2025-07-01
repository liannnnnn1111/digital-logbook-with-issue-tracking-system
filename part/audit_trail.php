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
        /* Table Styles */
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            text-align: center;
        }

        .audit-table th, .audit-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .audit-table tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .audit-table th {
            background-color: white;
            color: black;
            cursor: pointer;
        }

        .sort-icon {
            margin-left: 5px;
            font-size: 0.8em;
        }

        /* Button and Input Styles */
        .button-container .inputtype {
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
        }

        .button-container #filter {
            padding: 5px 20px;
            color: black;
            border: solid 1px;
            border-radius: 7px;
            height: 40px;
            border-color: gray;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none;
            }
        }

       
#pagination-controls {
    position: fixed;
    bottom: 20px; 
    right: 20px;   
    z-index: 1000; 
}

#pagination-controls button {
    margin: 5px;
    padding: 8px 15px;
    font-size: 14px;
    background-color: #006735;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

#pagination-controls button:disabled {
    background-color: #ddd; /* Disable color */
    cursor: not-allowed;
    color: black;
}

    </style>

<div id="audit_trail">
    <div class="button-container">
        <div class="inputtype">
            <input type="text" id="filter" placeholder="Search Actions" onkeyup="fetchLogs()">
            <button class="button" onclick="printPage()">Print This Page</button>
        </div>
    </div>

    <table class="audit-table" id="audit_table">
        <thead>
            <tr>
                 <th onclick="sortTable('id')"> ID <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable('user_id')">User ID <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable('action')">Action <i class="fas fa-sort sort-icon"></i></th>
                <th onclick="sortTable('timestamp')">Timestamp <i class="fas fa-sort sort-icon"></i></th>
            </tr>
        </thead>
        <tbody>
            <!-- Table rows will be dynamically inserted here by AJAX -->
        </tbody>
    </table>

    <!-- Pagination controls -->
   <div id="pagination-controls" class="no-print">
    <button id="prevPageBtn" onclick="changePage(-1)" disabled>Previous</button>
    <button id="nextPageBtn" onclick="changePage(1)" disabled>Next</button>
</div>

</div>

<script>
 let currentSort = { column: '', order: 'asc' };
let currentPage = 1; // Initial page is 1
let isLoading = false;
let totalPages = 1;
const limit = 20;  // Set to 20 records per page

// Function to handle fetching logs from the server
function fetchLogs() {
    if (isLoading) return; // Prevent multiple simultaneous fetches
    isLoading = true;

    const filter = document.getElementById('filter').value.trim(); // Get the filter from the input box

    // Fetch logs from the server with the filter and pagination
    fetch(`part/get_logs.php?filter=${encodeURIComponent(filter)}&page=${currentPage}`)
        .then(response => response.json())
        .then(logs => {
            const tbody = document.querySelector('#audit_table tbody');
            tbody.innerHTML = '';  // Clear previous logs

            if (logs.length > 0) {
                logs.forEach(log => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${log.id}</td>
                        <td>${log.user_id}</td>
                        <td>${log.action}</td>
                        <td>${log.timestamp}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="4">No logs found</td>';
                tbody.appendChild(row);
            }

            updatePaginationControls(logs.length);
        })
        .catch(error => console.error('Error fetching logs:', error))
        .finally(() => {
            isLoading = false; // Reset the loading state
        });
}

// Update pagination controls based on the results
function updatePaginationControls(logCount) {
    totalPages = Math.ceil(logCount / limit);  // Adjust the total pages calculation
    document.getElementById('prevPageBtn').disabled = currentPage === 1;
    document.getElementById('nextPageBtn').disabled = currentPage === totalPages;
}

// Change page based on user navigation
function changePage(direction) {
    if (direction === -1 && currentPage > 1) {
        currentPage--;  // Decrease page number for "Previous"
    } else if (direction === 1 && currentPage < totalPages) {
        currentPage++;  // Increase page number for "Next"
    }

    // Fetch new logs for the updated page
    fetchLogs();
}

// Sorting logic for table
function sortTable(column) {
    const rows = Array.from(document.querySelector('#audit_table tbody').rows);
    const index = {id: 0, user_id: 1, action: 2, timestamp: 3 }[column];

    // Toggle sort order if clicking the same column
    if (currentSort.column === column) {
        currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.order = 'asc'; // default to ascending
    }

    rows.sort((a, b) => {
        const cellA = a.cells[index].innerText;
        const cellB = b.cells[index].innerText;

        // If timestamps, compare as dates
        if (column === 'timestamp') {
            const dateA = new Date(cellA);
            const dateB = new Date(cellB);
            return currentSort.order === 'asc' ? dateA - dateB : dateB - dateA;
        }

        // For other columns, compare as strings
        return currentSort.order === 'asc' 
            ? cellA.localeCompare(cellB) 
            : cellB.localeCompare(cellA);
    });

    // Append the sorted rows to the table body
    const tbody = document.querySelector('#audit_table tbody');
    rows.forEach(row => tbody.appendChild(row));
    
    // Update sort icon direction
    updateSortIcons(column);
}

// Update the sort icon direction
function updateSortIcons(column) {
    const ths = document.querySelectorAll('#audit_table th');
    ths.forEach(th => {
        const icon = th.querySelector('.sort-icon');
        if (th.innerText.toLowerCase().includes(column)) {
            icon.innerHTML = currentSort.order === 'asc' 
                ? '&#9650;' // Ascending
                : '&#9660;'; // Descending
        } else {
            icon.innerHTML = '&#8645;'; // Neutral
        }
    });
}

// Print the page
function printPage() {
    window.print();
}

// Initial fetch when page loads
window.onload = fetchLogs;

</script>


