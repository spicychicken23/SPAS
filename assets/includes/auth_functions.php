<?php

function check_logged_in() {

    if (isset($_SESSION['auth'])){

        return true;
    }
    else {

        header("Location: /SPAS/pages/Login/login.php");
        exit();
    }
}

function check_logged_out() {

    if (!isset($_SESSION['auth'])){

        return true;
    }
    else {

        header("Location: /SPAS/pages/homepage/dynamic-full-calendar.php");
        exit();
    }
}

function force_login($username) {
    require '../../assets/setup/db.inc.php';

    if (strpos($username, 'Teacher') === 0) {
        $table = 'Teachers';
    } elseif (strpos($username, 'Admin') === 0) {
        $table = 'Admin';
    } else {
        $table = 'Students';
    }
    
    $sql = "SELECT * FROM $table WHERE username=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return false;
    } 
    else {
        mysqli_stmt_bind_param($stmt, "sss", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (!$row = mysqli_fetch_assoc($result)) {
            return false;
        }
        else {
            if ($row['verified_at'] != NULL) {
                $_SESSION['auth'] = 'verified';
            } else {
                $_SESSION['auth'] = 'loggedin';
            }

            $_SESSION['role'] = $table;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['last_login_at'] = $row['last_login_at'];
            
            return true;
        }
    }
}


function check_remember_me() {

    require '../../assets/setup/db.inc.php';
    
    if (empty($_SESSION['auth']) && !empty($_COOKIE['rememberme'])) {
        
        list($selector, $validator) = explode(':', $_COOKIE['rememberme']);

        $sql = "SELECT * FROM auth_tokens WHERE auth_type='remember_me' AND selector=? AND expires_at >= NOW() LIMIT 1;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            // SQL ERROR
            return false;
        }
        else {
            
            mysqli_stmt_bind_param($stmt, "s", $selector);
            mysqli_stmt_execute($stmt);
            $results = mysqli_stmt_get_result($stmt);

            if (!($row = mysqli_fetch_assoc($results))) {

                // COOKIE VALIDATION FAILURE
                return false;
            }
            else {

                $tokenBin = hex2bin($validator);
                $tokenCheck = password_verify($tokenBin, $row['token']);

                if ($tokenCheck === false) {

                    // COOKIE VALIDATION FAILURE
                    return false;
                }
                else if ($tokenCheck === true) {

                    $username = $row['username'];
                    force_login($username);
                    
                    return true;
                }
            }
        }
    }
}