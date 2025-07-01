 <style>
        #archived-accounts-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            text-align: center;
        }

        #archived-accounts-table th, #archived-accounts-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        /* Modal styles */
        .modal-backdrop {
            opacity: 0.5 !important;
        }
    </style>

<div class="container mt-5">
    <!-- Toast Container for Success/Failure Messages -->
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Toast messages will appear here -->
    </div>

    <!-- Table for Archived Accounts -->
    <table class="table table-bordered" id="archived-accounts-table">
        <thead style="background-color: whitesmoke;">
            <tr>
                <th>ID <i class="bi bi-sort" onclick="sortTable(0)"></i></th>
                <th>Last Name <i class="bi bi-sort" onclick="sortTable(1)"></i></th>
                <th>First Name <i class="bi bi-sort" onclick="sortTable(2)"></i></th>
                <th>Middle Name <i class="bi bi-sort" onclick="sortTable(3)"></i></th>
                <th>Suffix <i class="bi bi-sort" onclick="sortTable(4)"></i></th>
                <th>Birthday <i class="bi bi-sort" onclick="sortTable(5)"></i></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="archived-accounts-body">
            <!-- Rows will be populated via JavaScript -->
        </tbody>
    </table>
</div>

<!-- Modal for Unarchive Confirmation -->
<div class="modal fade" id="unarchiveModal" tabindex="-1" aria-labelledby="unarchiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unarchiveModalLabel">Confirm Unarchive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to unarchive this account?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button   style="background-color: #006735 !important" type="button" class="btn btn-primary" id="confirmUnarchiveBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="successToast" class="toast bg-success text-white" role="alert">
        <div class="toast-header">
            <strong style="padding: 5px !important;" class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Action completed successfully.
        </div>
    </div>
    <div id="errorToast" class="toast bg-danger text-white" role="alert">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="errorMessage">
            An error occurred.
        </div>
    </div>
</div>

<script>
    // Fetch archived accounts
    fetch('part/fetch_archived_accounts.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const accounts = data.data;
                const tableBody = document.getElementById('archived-accounts-body');

                if (accounts.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No archived accounts found.</td></tr>';
                } else {
                    accounts.forEach(account => {
                        const row = `
                            <tr>
                                <td>${account.id}</td>
                                <td>${account.last_name}</td>
                                <td>${account.first_name}</td>
                                <td>${account.middle_name}</td>
                                <td>${account.suffix || ''}</td>
                                <td>${account.birthday}</td>
                                <td>
                                    <i class='bi bi-archive archive-icon'
                       style='margin: 5px; cursor: pointer; color: blue !important; font-size: 20px !important;'
                        onclick="openModal('${account.id}')"> </i>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => console.error('Error fetching archived accounts:', error));

    // Modal: Store the account ID to unarchive
    let accountToUnarchive = null;
    function openModal(accountId) {
        accountToUnarchive = accountId;
        const modal = new bootstrap.Modal(document.getElementById('unarchiveModal'));
        modal.show();
    }

    // Handle unarchive action
    document.getElementById('confirmUnarchiveBtn').addEventListener('click', () => {
        if (accountToUnarchive) {
            fetch('part/unarchive_accounts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${accountToUnarchive}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('success', 'Account successfully unarchived!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', data.message);
                }
                const modal = bootstrap.Modal.getInstance(document.getElementById('unarchiveModal'));
                modal.hide();  // Close modal
            })
            .catch(error => {
                console.error('Error unarchiving account:', error);
                showToast('error', 'An error occurred while unarchiving the account.');
            });
        }
    });

    // Show toast notifications
    function showToast(type, message) {
        const toast = document.getElementById(type + 'Toast');
        const toastBody = toast.querySelector('.toast-body');
        toastBody.textContent = message;
        const toastInstance = new bootstrap.Toast(toast);
        toastInstance.show();
    }

    // Sorting function with multi-directional sorting and icon toggling
    let sortDirections = new Array(6).fill(1); // For 6 columns: 1 for ascending, -1 for descending

    function sortTable(columnIndex) {
        const table = document.getElementById('archived-accounts-table');
        const rows = Array.from(table.querySelectorAll('tr:nth-child(n+2)'));  // Skip the header row
        const currentDirection = sortDirections[columnIndex];

        const sortedRows = rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex].textContent.trim();
            const cellB = rowB.cells[columnIndex].textContent.trim();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return (parseFloat(cellA) - parseFloat(cellB)) * currentDirection;
            } else {
                return cellA.localeCompare(cellB) * currentDirection;
            }
        });

        sortedRows.forEach(row => table.appendChild(row));

        // Toggle the sort direction
        sortDirections[columnIndex] = currentDirection === 1 ? -1 : 1;

        // Update icons in headers
        updateSortIcons();
    }

    function updateSortIcons() {
        const headers = document.querySelectorAll('#archived-accounts-table th i');
        headers.forEach(icon => {
            icon.classList.remove('bi-sort-up', 'bi-sort-down');
            icon.classList.add('bi-sort');
        });

        headers.forEach((icon, index) => {
            if (sortDirections[index] === 1) {
                icon.classList.add('bi-sort-up');
            } else if (sortDirections[index] === -1) {
                icon.classList.add('bi-sort-down');
            }
        });
    }
</script>


