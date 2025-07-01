<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';  // Set default section to 'dashboard'

// Include the appropriate analytics file based on the 'analytics' query parameter
if (isset($_GET['analytics'])) {
    if ($_GET['analytics'] === 'analytics_logs') {
        include 'analytics_logs.php';
    } elseif ($_GET['analytics'] === 'analytics_remarks') {
        include 'analytics_remarks.php';
    }
}
?>



<div class="d-flex flex-column flex-column-fluid" style="background-color: whitesmoke; height: auto;">
    <!-- Begin: Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!-- Begin: Content Container -->
        <div id="kt_app_content_container" class="app-container container-fluid content-area">
            <!-- Show content conditionally based on the clicked sidebar link -->
            <?php if ($section === 'log_records') : ?>
                <?php include "content_user_logs.php"; ?>
            <?php elseif ($section === 'audit_trail') : ?>
                <?php include "audit_trail.php"; ?>
            <?php elseif ($section === 'dashboard') : ?>
                <?php include "dashboard.php"; ?>

            <?php elseif ($section === 'issue_ticket') : ?>
                <?php include "content_adminissues.php"; ?>
            <?php elseif (in_array($section, ['student_acc', 'faculty_acc', 'staff_acc', 'admin_acc'])) : ?>
                <?php include "content_adminnewacc.php"; ?>
            <?php elseif ($section === 'student_report') : ?>
                <?php include "student_report.php"; ?>
            <?php elseif ($section === 'archive') : ?>
                <?php include "unarchive_frontend.php";?>
            <?php else : ?>
                <p>Invalid section. Please select a valid section from the sidebar.</p>
            <?php endif; ?>
        </div>
        <!-- End: Content Container -->
    </div>
    <!-- End: Content -->
</div>
