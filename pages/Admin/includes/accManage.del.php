<?php
require '../../../assets/setup/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $table = $_POST['table_name'];

    if ($table === 'Class') {

        $selectSql = "SELECT id_name FROM class WHERE id = ?";
        $selectStmt = mysqli_prepare($conn, $selectSql);

        if ($selectStmt) {
            mysqli_stmt_bind_param($selectStmt, 'i', $id);
            if (mysqli_stmt_execute($selectStmt)) {
                mysqli_stmt_bind_result($selectStmt, $classColumn);
                mysqli_stmt_fetch($selectStmt);
            }
        }
        mysqli_stmt_close($selectStmt);

        $checkSql = "SELECT COUNT(*) FROM students WHERE class = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);

        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, 's', $classColumn);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_bind_result($checkStmt, $count);
            mysqli_stmt_fetch($checkStmt);

            if ($count > 0) {
                echo '<script>alert("Student(s) detected in class"); window.history.go(-1);</script>';
                exit();
            }
        } else {
            echo 'Prepared statement error: ' . mysqli_error($conn);
            exit();
        }

        mysqli_stmt_close($checkStmt);
    }

    $sql = "DELETE FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {

            header("Location: ../accManage.php");
            exit();
        } else {

            echo 'Database deletion error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {

        echo 'Prepared statement error: ' . mysqli_error($conn);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteValue'])) {

    $table = $_POST['deleteValue'];

    if ($table == 'class') {

        $checkClassSql = "SELECT COUNT(*) FROM students";
        $checkClassStmt = mysqli_prepare($conn, $checkClassSql);

        if ($checkClassStmt) {
            mysqli_stmt_execute($checkClassStmt);
            mysqli_stmt_bind_result($checkClassStmt, $classCount);
            mysqli_stmt_fetch($checkClassStmt);

            mysqli_stmt_close($checkClassStmt);

            if ($classCount > 0) {
                echo '<script>alert("Cannot bulk delete classes if there are students"); window.history.go(-1);</script>';
                exit();
            }
        }
    } elseif ($table == '') {    
        echo '<script>alert("Please select a role"); window.history.go(-1);</script>';
        exit();
    }
    
    $sql = "DELETE FROM $table";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../accManage.php");
        exit();
    } else {
        header("Location: ../accManage.php");
        exit();
    }
} else {
    echo 'Invalid request.';
}
