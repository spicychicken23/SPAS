<?php
session_start();

require __DIR__ . '/../setup/env.php';
require __DIR__ . '/../setup/db.inc.php';
require __DIR__ . '/auth_functions.php';
require __DIR__ . '/security_functions.php';

if (isset($_SESSION['auth'])) {
    $_SESSION['expire'] = ALLOWED_INACTIVITY_TIME;

generate_csrf_token();
check_remember_me();
check_logged_in();


}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <link rel="manifest" href="/SPAS/manifest.json" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/bg.css">
    <meta name="theme-color" content="#000000">

</head>

<body>
        <?php if (isset($_SESSION['auth'])) { ?>
    <div class="shadow-lg d-flex justify-content-between align-items-center header">
        <div class="logo-container">
            <img class="logo" src="../../assets\School Logo.png" alt="SMK SUNGAI PUSU">
            <span class="smk align-text-left">SMK SUNGAI PUSU</span>
        </div>
        
            <div class="naviBar">
                <nav class="navbar navbar-expand-md">
                    <div class="container-fluid">
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                                <li class="nav-item mr-3">
                                    <a href="/SPAS/pages/homepage/dynamic-full-calendar.php" class="nav-link">
                                        <i class="bi bi-house-door align-text-center"></i> HOME
                                    </a>
                                </li>

                                <?php if ($_SESSION['role'] == 'Admin'): ?>
                                    <li class="nav-item mr-3">
                                        <a href="/SPAS/pages/attendance/generateBarcode.php" class="nav-link">
                                            <i class="bi bi-upc-scan align-text-center"></i> CREATE BARCODE
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (!is_homepage() && $_SESSION['role'] !== 'Student'): ?>
                                    <li class="nav-item mr-3">
                                        <a href="/SPAS/pages/attendance/readBarcode.php" class="nav-link">
                                            <i class="bi bi-pencil-square align-text-center"></i> RECORD ATTENDANCE
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (!is_homepage() && $_SESSION['role'] == 'Teacher'): ?>    
                                    <li class="nav-item mr-3">
                                        <a href="/SPAS/pages/attendance/teacher attendance.php" class="nav-link">
                                            <i class="bi bi-search align-text-center"></i> VIEW ATTENDANCE
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                                    
                                <img class="logoSys d-inline-block" src="../../assets/icons/TransInv.png" alt="Logo">
                            
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0"> 

                                <?php if (!is_homepage() && $_SESSION['role'] == 'Admin'): ?>    
                                    <li class="nav-item mr-3">
                                        <a href="/SPAS/pages/attendance/teacher attendance.php" class="nav-link">
                                            <i class="bi bi-search align-text-center"></i> VIEW ATTENDANCE
                                        </a>
                                    </li>
                                <?php endif; ?>    

                                <?php if ($_SESSION['role'] !== 'Student'): ?>   
                                    <li class="nav-item dropdown ml-3">
                                    <a class="nav-link dropdown-toggle" href="#" id="analysisDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-graph-down align-text-center"></i> ATTENDANCE ANALYSIS
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="analysisDropdown">
                                        <li><a class="dropdown-item" href="/SPAS/pages/Analysis/individual.php">Individual</a></li>
                                        <li><a class="dropdown-item" href="/SPAS/pages/Analysis/daily.php">Daily</a></li>
                                        <li><a class="dropdown-item" href="/SPAS/pages/Analysis/weekly.php">Weekly</a></li>
                                        <li><a class="dropdown-item" href="/SPAS/pages/Analysis/monthly.php">Monthly</a></li>
                                    </ul>
                                    </li>
                                <?php endif; ?>

                                <?php if (!is_homepage() && $_SESSION['role'] !== 'Admin'): ?>
                                    <li class="nav-item ml-3">
                                        <a href="/SPAS/pages/attendance/myAttendance.php" class="nav-link">
                                            <i class="bi bi-journal-check align-text-center"></i> MY ATTENDANCE
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if ($_SESSION['role'] == 'Admin'): ?>
                                    <li class="nav-item ml-3">
                                        <a href="/SPAS/pages/Admin/accManage.php" class="nav-link">
                                            <i class="bi bi-journal-text align-text-center"></i> MANAGE DATA
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>



            <div class="logout">  
                <a href="/SPAS/pages/Logout/logout.php"><i class="bi bi-box-arrow-right logoutIcon align-middle"></i></a>
            </div>

        <?php } ?>

        <?php
            // Function to check if the current page is the homepage
            function is_homepage() {
                return basename($_SERVER['PHP_SELF']) == 'dynamic-full-calendar.php';
            }
        ?>

    </div>

</body>
</html>
