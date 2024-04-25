<?php

session_start();

require '../../../assets/setup/env.php';
require '../../../assets/setup/db.inc.php';
require '../../../assets/includes/auth_functions.php';
require '../../../assets/includes/security_functions.php';
require '../../../assets/includes/datacheck.php';


if (isset($_POST['submit'])) {


foreach($_POST as $key => $value){

    $_POST[$key] = _cleaninjections(trim($value));
}




function input_filter($data) {
    $data= trim($data);
    $data= stripslashes($data);
    $data= htmlspecialchars($data);
    return $data;
}

$table = input_filter($_POST['role']);

if ($table == 'Students') {

    //$icNo = input_filter($_POST['icNo']);
    $email = input_filter($_POST['email']);
    $name = input_filter($_POST["name"]);
    $class  = input_filter($_POST['class']);  

    if (!availableUsername($conn, $email)){

        $_SESSION['ERRORS']['usernameerror'] = 'This user already exist';
        echo '<script>alert("Email is already in use"); window.history.go(-1);</script>';
        exit();
    }

    if (classExists($conn, $class)) {
        
        $sql = "INSERT INTO `$table` (`email`, `name`, `class`, `password`) VALUES (?,?,?,?)";
        $stmt = mysqli_stmt_init($conn);
    
        if (!mysqli_stmt_prepare($stmt, $sql)) {

            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
            header("Location: ../");
            exit();
        } 
        else {

            $p1 = explode(' ',trim($name));
            $p2 = preg_replace('/[^0-9]/', '', $email);
            $password = $p1[0] . $p1[1] . $p2;

            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ssss", $email, $name, $class, $hashedPwd);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            header("Location: ../accManage.php");
            exit();
        }

    } else {
        echo '<script>alert("Class does not exist. Please enter a valid class."); window.history.go(-1);</script>';
        exit();
    }
} 

else if ($table == 'Teachers') {
    $email = input_filter($_POST['email']);
    $name = input_filter($_POST["name"]);
    
    if (!availableEmail($conn, $email)){

        $_SESSION['ERRORS']['usernameerror'] = 'This user already exist';
        echo '<script>alert("Email address is already in use"); window.history.go(-1);</script>';
        exit();
    }

    $sql = "INSERT INTO `$table` (`email`, `name`, `password`) VALUES (?,?,?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
        header("Location: ../");
        exit();
    } 
    else {

        $hashedPwd = password_hash($email, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sss", $email, $name, $hashedPwd);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        header("Location: ../accManage.php");
        exit();
    }
}

else if ($table == 'Admin') {
    $name = input_filter($_POST["name"]);
    $username = input_filter($_POST['username']);
    $password = input_filter($_POST['password']);

    if (!availableUsername($conn, $username)){

        $_SESSION['ERRORS']['usernameerror'] = 'This user already exist';
        echo '<script>alert("Username is already in use"); window.history.go(-1);</script>';
        exit();
    }

    $sql = "INSERT INTO `$table` (`name`, `username`, `password`) VALUES (?,?,?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
        header("Location: ../");
        exit();
    } 
    else {

        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sss", $name, $username, $hashedPwd);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        header("Location: ../accManage.php");
        exit();
    }
}

else{
    $name = input_filter($_POST["name"]);
    $standard = input_filter($_POST['standard']);

    if (!availableClass($conn, $name, $standard)){

        $_SESSION['ERRORS']['usernameerror'] = 'This user already exist';
        echo '<script>alert("Class already exist"); window.history.go(-1);</script>';
        exit();
    }

    $sql = "INSERT INTO `$table` (`name`, `standard`) VALUES (?,?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
        header("Location: ../");
        exit();
    } 
    else {

        mysqli_stmt_bind_param($stmt, "ss", $name, $standard);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        header("Location: ../accManage.php");
        exit();
    }
}

}

function classExists($conn, $class) {
    $sql = "SELECT COUNT(*) AS count FROM `class` WHERE `id_name` = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $class);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            return $row['count'] > 0;
        }
    }

    return false;
}
