<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveModalLabel">Confirm Archive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive this account?</p>
                <p style="display: none;"><strong>Last Name:</strong> <span id="lastnameacc"></span></p>
                <p style="display: none;"><strong>First Name:</strong> <span id="firstnameacc"></span></p>
                <p style="display: none;"><strong>Middle Name:</strong> <span id="middlenameacc"></span></p>
                <input type="hidden" id="archive-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button style="background-color: #006735 !important" type="button" id="confirmArchive" class="btn btn-primary">Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="successToast" class="toast bg-success text-white" role="alert">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
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
$(document).ready(function () {
    // Populate modal with account data
    $('#archiveModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // The button/icon triggering the modal
        $('#lastnameacc').text(button.data('lastname'));
        $('#firstnameacc').text(button.data('firstname'));
        $('#middlenameacc').text(button.data('middlename'));
        $('#archive-id').val(button.data('id'));
    });

    // Confirm archive action
    $('#confirmArchive').on('click', function () {
        var accountId = $('#archive-id').val();

        if (!accountId) {
            alert("Missing account details.");
            return;
        }

        // AJAX request to archive the account
        $.ajax({
            url: 'part/archive_accounts.php',
            type: 'POST',
            data: { id: accountId },
            success: function (response) {
                var jsonResponse = JSON.parse(response);

                if (jsonResponse.success) {
                    $('#successToast .toast-body').text(jsonResponse.success);
                    $('#successToast').toast('show');
                    setTimeout(() => location.reload(), 1000); // Reload page after 1.5 seconds
                } else {
                    $('#errorMessage').text(jsonResponse.error);
                    $('#errorToast').toast('show');
                }
            },
            error: function (xhr, status, error) {
                $('#errorMessage').text('Failed to process the request.');
                $('#errorToast').toast('show');
            }
        });
    });
});
</script>
