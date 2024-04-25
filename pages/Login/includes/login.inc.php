<?php

session_start();

require '../../../assets/includes/auth_functions.php';
require '../../../assets/includes/datacheck.php';
require '../../../assets/includes/security_functions.php';

check_logged_out();

if (!isset($_POST['loginsubmit'])){

    header("Location: ../login.php");
    exit();
}
else {

    /*
    * -------------------------------------------------------------------------------
    *   Securing against Header Injection
    * -------------------------------------------------------------------------------
    */

    foreach($_POST as $key => $value){

        $_POST[$key] = _cleaninjections(trim($value));
    }


    /*
    * -------------------------------------------------------------------------------
    *   Verifying CSRF token
    * -------------------------------------------------------------------------------
    */

    if (!verify_csrf_token()){

        $_SESSION['STATUS']['loginstatus'] = 'Request could not be validated';
        header("Location: /SPAS/pages/Login/login.php");
        exit();
    }

    
    require '../../../assets/setup/db.inc.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {

        $_SESSION['STATUS']['loginstatus'] = 'fields cannot be empty';
        header("Location: ../login.php");
        exit();
    } 
    else {

        /*
        * -------------------------------------------------------------------------------
        *   Updating last_login_at
        * -------------------------------------------------------------------------------
        */

        if (strpos($username, 'm-') === 0) {
            $updateTable = 'Students';
        } elseif (strpos($username, 'Admin') === 0) {
            $updateTable = 'Admin';
        } else {
            $updateTable = 'Teachers';
        }

        $sql = "UPDATE $updateTable SET last_login_at=NOW() WHERE username=?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {

            $_SESSION['ERRORS']['sqlerror'] = 'SQL ERROR';
            header("Location: ../login.php");
            exit();
        }
        else {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
        }



        /*
        * -------------------------------------------------------------------------------
        *   Creating SESSION Variables
        * -------------------------------------------------------------------------------
        */
        if (strpos($username, 'm-') === 0) {
            $table = 'Students';
        } elseif (strpos($username, 'Admin') === 0) {
            $table = 'Admin';
        } else {
            $table = 'Teachers';
        }
        
        $sql = "SELECT * FROM $table WHERE username=?;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
            header("Location: ../login.php");
            exit();
        } 
        else {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {

                $pwdCheck = password_verify($password, $row['password']);

                if ($pwdCheck == false) {

                    $_SESSION['ERRORS']['wrongpassword'] = 'wrong password';
                    header("Location: ../login.php");
                    exit();
                } 
                else if ($pwdCheck == true) {

                    session_start();

                    
                    if($row['verified_at'] != NULL){

                        $_SESSION['auth'] = 'verified';
                    } else{

                        $_SESSION['auth'] = 'loggedin';
                    }

                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['first_name'] = $row['name'];
                    $_SESSION['last_login_at'] = $row['last_login_at'];

                    if ($table == 'Teachers') {
                        preg_match('/g-(.*?)@/', $row['email'], $matches);
                        $MOE = isset($matches[1]) ? 'g-' . $matches[1] : '';
                        $_SESSION['MOE'] = $MOE;

                        $_SESSION['phoneNo'] = $row['phoneNo'];
                        $_SESSION['role'] = 'Teacher';

                    }
                    else if ($table == 'Students') {
                        $_SESSION['role'] = 'Student';
                    }
                    else {
                        $_SESSION['role'] = 'Admin';

                    }


                    /*
                    * -------------------------------------------------------------------------------
                    *   Setting rememberme cookie
                    * -------------------------------------------------------------------------------
                    */

                    if (isset($_POST['rememberme'])){

                        $selector = bin2hex(random_bytes(8));
                        $token = random_bytes(32);

                        $sql = "DELETE FROM auth_tokens WHERE username=? AND auth_type='remember_me';";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {

                            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                            header("Location: ../login.php");
                            exit();
                        }
                        else {

                            mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
                            mysqli_stmt_execute($stmt);
                        }

                        setcookie(
                            'rememberme',
                            $selector.':'.bin2hex($token),
                            time() + 864000,
                            '/',
                            NULL,
                            false, // TLS-only
                            true  // http-only
                        );

                        $sql = "INSERT INTO auth_tokens (username, auth_type, selector, token, expires_at) 
                                VALUES (?, 'remember_me', ?, ?, ?);";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {

                            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                            header("Location: ../login.php");
                            exit();
                        }
                        else {
                            
                            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ssss", $_SESSION['username'], $selector, $hashedToken, date('Y-m-d\TH:i:s', time() + 864000));
                            mysqli_stmt_execute($stmt);
                        }
                    }

                    header("Location: ../../homepage/dynamic-full-calendar.php");
                    
                    exit();
                } 
            } 
            else {

                $_SESSION['ERRORS']['nouser'] = 'username does not exist';
                header("Location: ../login.php");
                exit();
            }
        }
    }
}