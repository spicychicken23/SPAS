<?php

session_start();

require '../../../assets/setup/env.php';
require '../../../assets/setup/db.inc.php';
require '../../../assets/includes/auth_functions.php';
require '../../../assets/includes/security_functions.php';
require '../../../assets/includes/datacheck.php';

if (isset($_POST['inputRole'])) {

    function input_filter($d)
    {
        $d = trim($d);
        $d = stripslashes($d);
        $d = htmlspecialchars($d);
        return $d;
    }

    function classExists($conn, $class)
    {
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

    if (isset($_FILES['inputCsv']) && $_FILES['inputCsv']['error'] == 0) {
        $file = $_FILES['inputCsv']['tmp_name'];

        $handle = fopen($file, "r");
        $table = $_POST['inputRole'];

        if ($handle !== FALSE) {
            if ($table == 'students') {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) != 4) {
                        echo '<script>alert("Invalid CSV file format for students. Please check the file and try again."); window.history.go(-1);</script>';
                        exit();
                    }

                    //$icNo = input_filter($data[0]);
                    $email = input_filter($data[0]);
                    $name = input_filter($data[1]);
                    $class_name = input_filter($data[2] . ' ' . $data[3]);


                    if (!availableStuEmail($conn, $email)) {
                        continue;
                    }

                    if (classExists($conn, $class_name)) {

                        $sql = "INSERT INTO `$table` (`email`, `name`, `class`, `password`) VALUES (?,?,?,?)";
                        $stmt = mysqli_stmt_init($conn);

                        if (!mysqli_stmt_prepare($stmt, $sql)) {

                            $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                            header("Location: ../");
                            exit();
                        } else {

                            $p1 = explode(' ',trim($name));
                            $p2 = preg_replace('/[^0-9]/', '', $email);
                            $password = $p1[0] . $p1[1] . $p2;
                
                            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ssss", $email, $name, $class_name, $hashedPwd);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_store_result($stmt);
                        }
                    } else {
                        echo '<script>alert("Class \'' . $class_name . '\' does not exist."); window.history.go(-1);</script>';
                        exit();
                    }
                    fseek($handle, 0);
                }
            } else if ($table == 'teachers') {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if (count($data) != 2) {
                        echo '<script>alert("Invalid CSV file format for teachers. Please check the file and try again."); window.history.go(-1);</script>';
                        exit();
                    }

                    $email = input_filter($data[0]);
                    $name = input_filter($data[1]);

                    if (!availableEmail($conn, $email)) {
                        continue;
                    }

                    $sql = "INSERT INTO `$table` (`email`, `name`, `password`) VALUES (?,?,?)";
                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $sql)) {

                        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                        header("Location: ../");
                        exit();
                    } else {

                        $hashedPwd = password_hash($email, PASSWORD_DEFAULT);
                        mysqli_stmt_bind_param($stmt, "sss", $email, $name, $hashedPwd);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                    }
                    fseek($handle, 0);
                }
            } else if ($table == 'admin') {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if (count($data) != 3) {
                        echo '<script>alert("Invalid CSV file format for admin. Please check the file and try again."); window.history.go(-1);</script>';
                        exit();
                    }

                    $name = input_filter($data[0]);
                    $username = input_filter($data[1]);
                    $password = input_filter($data[2]);

                    if (!availableUsername($conn, $username)) {
                        continue;
                    }

                    $sql = "INSERT INTO `$table` (`name`, `username`, `password`) VALUES (?,?,?)";
                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $sql)) {

                        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                        header("Location: ../");
                        exit();
                    } else {

                        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_stmt_bind_param($stmt, "sss", $name, $username, $hashedPwd);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                    }
                    fseek($handle, 0);
                }
            } else if ($table == 'class') {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if (count($data) != 2) {
                        echo '<script>alert("Invalid CSV file format for class. Please check the file and try again."); window.history.go(-1);</script>';
                        exit();
                    }

                    $name = input_filter($data[0]);
                    $standard = input_filter($data[1]);

                    if (!availableClass($conn, $name, $standard)) {
                        continue;
                    }

                    $sql = "INSERT INTO `$table` (`name`, `standard`) VALUES (?,?)";
                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $sql)) {

                        $_SESSION['ERRORS']['scripterror'] = 'SQL ERROR';
                        header("Location: ../");
                        exit();
                    } else {

                        mysqli_stmt_bind_param($stmt, "ss", $name, $standard);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                    }
                    fseek($handle, 0);
                }
            } else {
                echo '<script>alert("Please choose a role."); window.history.go(-1);</script>';
                exit();
            }

            fclose($handle);
        } else {
            echo '<script>alert("Unable to open file."); window.history.go(-1);</script>';
            exit();
        }
    } else {
        echo '<script>alert("No file is uploaded."); window.history.go(-1);</script>';
        exit();
    }
    header("Location: ../accManage.php");
    exit();
}
