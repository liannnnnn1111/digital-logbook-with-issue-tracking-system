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
    
    #kt_app_sidebar {
        background-color: #023020; 
    }

    .btn-custom {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #000000;
        transition: background-color 0.3s, color 0.3s; 
    }
    
    .btn-custom:hover {
        background-color: #000000;
        color: #ffffff;
    }

    .h-40px {
        height: 40px;
    }

    .w-100 {
        width: 100%;
    }


    .menu-item .menu-link.active {
        color: white; 
        font-weight: bold; 
           background-color: #3b3b3b;
    }
    .menu-item:hover {
    background-color: #3b3b3b; /* Change to black on hover */

}


    #kt_app_sidebar_footer .btn-custom {
        background-color: white;
        color: black;
    }


    .menu-item .menu-title  {
        color:white !important;
        background-color: transparent; /* Default background color */
    transition: background-color 5s ease; 
    }


    .menu-item .menu-icon {
        color: white;
    }

    #kt_app_sidebar {
        background-color: #006735;
        transition: width 0.3s;
    }

    .app-sidebar {
        width: 280px; 
    }

   
    .menu-item .menu-item {
        padding-left: 40px; 
    }

    .app-sidebar-minimize {
        width: 70px; 
    }

    

     .app-sidebar-minimize h1 {
        display: none;
    }
    .app-sidebar-minimize .app-sidebar-logo{
        justify-content: start;
    }


    .app-sidebar-minimize .menu-item {
        justify-content: center; 
    }

    .menu-item .menu-link.active {
        color: white; 
        font-weight: bold; 
    }

    .app-sidebar-minimize .app-sidebar-footer {
        display: none; 
    }
    .menu-item .menu-arrow{
        color: whitesmoke !important;
    }
    @font-face {
    font-family: 'Conthrax-SemiBold';
    src: url('assets/font/Conthrax-SemiBold.otf') format('truetype'); /* Adjust path if necessary */
    font-weight: normal;
    font-style: normal;
}



</style>
<div> 
<div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container" style="background-color: ghostwhite; border-bottom: 1px solid lightgray;">
    <!--begin::Sidebar mobile toggle. back back back-->
    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
            <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>
</div>
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6 d-flex align-items-center" id="kt_app_sidebar_logo">
    <a href="index.php" class="d-flex align-items-center">
        <img alt="Logo" src="sidebar.png" class="h-70px w-auto app-sidebar-logo-default me-2" />
        <img alt="Logo" src="sidebar.png" class="h-40px app-sidebar-logo-minimize" />
        <h1 style="font-size: 2.1rem; color: #ffffff; margin: 0; font-family: 'Conthrax-SemiBold', sans-serif; letter-spacing: 0.4rem;">LOG IT</h1>

    </a>

    <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize" data-kt-toggle-class="app-sidebar-minimize">
        <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
</div>


    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-visible flex-column-fluid">
        <!--begin::Menu wrapper-->
      <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper"  data-user-type="<?php echo $_SESSION['user_type']; ?>">

         <div id="kt_app_sidebar_menu" class="app-sidebar-menu overflow-visible flex-column-fluid">
          

                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                    <!--begin:Menu item for Log History-->

                    <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'dashboard');">
                                    <span class="menu-icon">
                                        <i class="bi bi-person-fill-down" style="color: white;"></i>
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Dashboard</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'log_records', 'analytics_logs', true);">
                                    <span class="menu-icon">
                                        <i class="bi bi-person-fill-down" style="color: white;"></i>
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Log Records</span>
                                </a>
                            </div>
                            

                    <!--begin:Menu item for Issue Tickets-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="#" onclick="showSection(event, 'issue_ticket', 'analytics_remarks', true);">
                            <span class="menu-icon">
                                <i class="bi bi-file-earmark-excel-fill"style="color: white;"></i>
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Issue Tickets</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    
                    <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'student_report');">
                                    <span class="menu-icon">
                                        <i class="bi bi-person-fill-down" style="color: white;"></i>
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Student Report</span>
                                </a>
                            </div>

                             <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'audit_trail', 'analytics_logs', true);">
                                    <span class="menu-icon">
                                        <i class="bi bi-person-fill-down" style="color: white;"></i>
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Audit Trail</span>
                                </a>
                            </div>
                     

                             <div class="menu-item" id="accounts-menu">
                        <!--begin:Menu link-->
                        <a class="menu-link" href="#" data-bs-toggle="collapse" data-bs-target="#accounts_submenu" aria-expanded="false">
                            <span class="menu-icon">
                               <i class="bi bi-person-fill-add"style="color: white;"></i>
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                            </span>
                            <span class="menu-title">Accounts</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div id="accounts_submenu" class="collapse">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'student_acc');">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2"style="color: white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Student Accounts</span>
                                </a>
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'faculty_acc');">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2"style="color: white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Faculty Accounts</span>
                                </a>
                            </div>
                              <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'staff_acc');">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2"style="color: white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Staff Accounts</span>
                                </a>
                            </div>
                              <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event, 'admin_acc');">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2"style="color:white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Admin Accounts</span>
                                </a>
                            </div>
                             <div class="menu-item">
                                <a class="menu-link" href="#" onclick="showSection(event,'archive');">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-user fs-2"style="color:white;">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Archived Accounts</span>
                                </a>
                                
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
    <!--begin::Footer-->
    <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
        <form action="part/logout.php" method="POST" class="d-flex align-items-center justify-content-center w-100">
                        <button type="submit" name="submit1" class="btn btn-custom btn-primary text-black w-100 h-40px">Logout</button>
        </form>
    </div>
    <!--end::Footer-->
</div>
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('kt_app_sidebar_toggle');
    const sidebar = document.getElementById('kt_app_sidebar');
    const user_type = "<?php echo $_SESSION['user_type']; ?>";

    // Hide accounts menu for staff
    if (user_type === 'staff') {
        const accountsMenu = document.getElementById('accounts-menu');
        if (accountsMenu) {
            accountsMenu.style.display = 'none';
        }
    }

   

    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('app-sidebar-minimize');
        toggleButton.querySelector('i').classList.toggle('rotate-180');
    });
});


    function showSection(event, sectionId, analyticsPage, isLogHistory = false) {
        event.preventDefault(); // Prevent default action

        // Set the URL parameter based on the clicked section
        window.location.href = window.location.pathname + '?section=' + sectionId + '&analytics=' + analyticsPage + '&isLogHistory=' + isLogHistory;

        // Hide all sections
        var sections = document.querySelectorAll('#dashboard, #log_records, #audit_trail, #issue_ticket, #student_acc, #faculty_acc, #staff_acc, #admin_acc, #student_report, #archive');
        sections.forEach(function(section) {
            section.style.display = 'none';
        });

        // Show the selected section
        document.getElementById(sectionId).style.display = 'block';

        // Activate the clicked menu item
        var menuLinks = document.querySelectorAll('.menu-link');
        menuLinks.forEach(function(link) {
            link.classList.remove('active');
        });
        event.target.closest('.menu-link').classList.add('active');
    }


</script>






