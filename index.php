<?php
require __DIR__ . '/assets/setup/env.php';
require __DIR__ . '/assets/setup/db.inc.php';
require __DIR__ . '/assets/includes/auth_functions.php';
require __DIR__ . '/assets/includes/security_functions.php';

if (check_logged_out()) {

    header("Location: /SPAS/pages/homepage/dynamic-full-calendar.php");
    exit();
}
else {

    header("Location: /SPAS/pages/Login/login.php");
    exit();
}