<html>

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../../css/bg.css">
</head>

<body>
    <?php

    require '../../../assets/setup/db.inc.php';

    if (isset($_POST['name'])) {
        $table = $_POST['name'];

        try {

            if (isset($_POST['search'])) {
                $query = $_POST['query'];
                $search_query = $_POST['search_query'];
                $sql = "SELECT * FROM $table WHERE $query LIKE '%$search_query%'";
                
            }

            else {
                $sql = "SELECT * FROM $table";
            }

            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo '
                <table id="example" class="table table-light table-hover" style="width:100%">
                    <thead>';

                if (($table !== 'Class' && $table !== 'class')) {
                    echo '
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>';

                    if (($table !== 'Admin' && $table !== 'admin')) {
                        echo '<th>Email</th>';
                        if (($table !== 'Teachers' && $table !== 'teachers')) {
                            echo '<th>Class</th>';
                        }
                    }
                } else {
                    echo '
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Standard</th>';
                }

                if (($table !== 'Class' && $table !== 'class')) {
                    echo '
                        <th>Edit</th>';
                }
                echo '
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>';

                if (mysqli_num_rows($result) > 0) {


                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['name'] . '</td>';
                        if (($table !== 'Class' && $table !== 'class')) {
                            echo '<td>' . $row['username'] . '</td>';
                            if (($table !== 'Admin' && $table !== 'admin')) {
                                echo '<td>' . $row['email'] . '</td>';
                                if (($table === 'Students' || $table === 'students')) {
                                    echo '<td>' . $row['class'] . '</td>';
                                }
                            }
                        } else {
                            echo '<td>' . $row['standard'] . '</td>';
                        }

                        if ($table !== 'Class' && $table !== 'class') {
                            echo '
                                <td>
                                    <form method="POST" action="accEdit.php">
                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                        <input type="hidden" name="table_name" value="' . $table . '">
                                        <button class="btn btn-light rounded-0">
                                        <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </form>
                                </td>';
                        }

                        echo '
                    <td>
                        <form method="POST" action="includes/accManage.del.php" onsubmit="return confirm(\'Are you sure you want to delete this record?\');">
                            <input type="hidden" name="id" value="' . $row['id'] . '">
                            <input type="hidden" name="table_name" value="' . $table . '">
                            <button class="btn btn-sm btn-outline-secondary">
                                <img src="../../assets/icons/trash.png" alt="Delete Icon" width="15" height="15">
                            </button>
                        </form>
                    </td>';
                        echo '</tr>';
                    }

                    echo '
                    </tbody>
                    </table>';
                } else {
                    echo '';
                }
            } else {
                echo 'Database query error: ' . mysqli_error($conn);
            }
        } catch (Exception $e) {
            echo 'Search not found';
        }
    } else {
        echo 'No table name provided.';
    }
    ?>

</body>

</html>