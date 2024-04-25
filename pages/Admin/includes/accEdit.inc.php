<?php
require '../../../assets/setup/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['table_name'];
    $id = $_POST['id'];

    switch ($role) {
        case 'Students':
            $name = $_POST['name'];
            $email = $_POST['email'];
            $class = $_POST['class']; 
            $sql = "UPDATE Students SET name='$name', email='$email', class='$class' WHERE id=$id";
            break;

        case 'Teachers':
            $name = $_POST['name'];
            $email = $_POST['email'];
            $sql = "UPDATE Teachers SET name='$name', email='$email' WHERE id=$id";
            break;

        case 'Admin':
            $username = $_POST['username'];
            $name = $_POST['name'];
            $sql = "UPDATE Admin SET name='$name', username='$username' WHERE id=$id";
            break;

        case 'Class':
            $name = $_POST['name'];
            $class = $_POST['class'];
            $sql = "UPDATE Class SET name='$name', standard='$class' WHERE id=$id";
            break;
        default:
            break;
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: ../accManage.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    header("Location: ../accManage.php");
    exit();
}
?>
