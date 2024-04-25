<?php

function availableUsername($conn, $username){

    if (stripos($username, 'teacher') === 0) {
        $tableName = 'Teachers';
    } elseif (stripos($username, 'admin') === 0) {
        $tableName = 'Admin';
    } else {
        $tableName = 'Students';
    }

    $sql = "select id from $tableName where username=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        return $_SESSION['ERRORS']['scripterror'] = 'SQL error';
    } 
    else {

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck > 0) {
            
            return false;
        } else {

            return true;
        }
    }
}

// function availableIC($conn, $IC){

//     $sql = "select id from Students where icNo=?;";
//     $stmt = mysqli_stmt_init($conn);
//     if (!mysqli_stmt_prepare($stmt, $sql)) {

//         return $_SESSION['ERRORS']['scripterror'] = 'SQL error';
//     } 
//     else {

//         mysqli_stmt_bind_param($stmt, "s", $IC);
//         mysqli_stmt_execute($stmt);
//         mysqli_stmt_store_result($stmt);
//         $resultCheck = mysqli_stmt_num_rows($stmt);

//         if ($resultCheck > 0) {
            
//             return false;
//         } else {

//             return true;
//         }
//     }
// }

function availableEmail($conn, $email){


    $sql = "select id from Teachers where email=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        return $_SESSION['ERRORS']['scripterror'] = 'SQL error';
    } 
    else {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck > 0) {
            
            return false;
        } else {

            return true;
        }
    }
}

function availableStuEmail($conn, $email){


    $sql = "select id from Students where email=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        return $_SESSION['ERRORS']['scripterror'] = 'SQL error';
    } 
    else {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck > 0) {
            
            return false;
        } else {

            return true;
        }
    }
}

function availableClass($conn, $name, $standard){


    $sql = "select id from Class where name=? and standard=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {

        return $_SESSION['ERRORS']['scripterror'] = 'SQL error';
    } 
    else {

        mysqli_stmt_bind_param($stmt, "ss", $name, $standard);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck > 0) {
            
            return false;
        } else {

            return true;
        }
    }
}