<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../css/bg.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../../../js/individual.js"></script>
</head>

<body>
    <?php

    require '../../../assets/setup/db.inc.php';
    require 'individual.calc.php';

    if (isset($_POST['name'])) {
        $form = $_POST['name'];

        $formNumber = filter_var($form, FILTER_SANITIZE_NUMBER_INT);

        $classPrefix = "T" . $formNumber;

        if ($form == 'Teachers') {

            $table = "teachers";

            if (isset($_POST['search'])) {
                $query = $_POST['query'];
                $search_query = $_POST['search_query'];
                $sql = "SELECT name, email FROM $table WHERE $query LIKE '%$search_query%'";
            } else {
                $sql = "SELECT name, email FROM $table";
            }
        } else {
            $table = "students";

            if (isset($_POST['search'])) {
                $query = $_POST['query'];
                $search_query = $_POST['search_query'];
                $sql = "SELECT class, name, username FROM $table WHERE $query LIKE '%$search_query%' AND  class LIKE '$classPrefix%'";
            } else {
                $sql = "SELECT class, name, username FROM $table WHERE class LIKE '$classPrefix%'";
            }
        }

    
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo '
            <table id="example" class="table table-light table-hover" style="width:100%">
            <thead>
                <tr>';
            if ($table == 'students') {
                echo '
                    <th>ID</th>
                    <th>Class</th>';
            } else {
                echo '<th>ID</th>';
            }
            echo '
                    <th>Name</th>
                    <th>Attended</th>
                    <th>Absent</th>
                    <th>Percentage</th>         
                    <th>Avg Time</th>
                    <th>Status</th> ';
            }

            echo '
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    if ($table == 'students') {
                        $attendance = "attendance";
                        $attendanceData = countAttendance($conn, $row['username']);
                        $status = checkAttendance($conn, $row['username'], $attendance);
                        echo '<td>' . $row['username'] . '</td>';
                        
                        $classWithoutPrefix = substr($row['class'], 2);
                        echo '<td>' . $classWithoutPrefix . '</td>';
                    } else if ($table == 'teachers') {
                        preg_match('/g-(.*?)@/', $row['email'], $matches);
                        $id = isset($matches[1]) ? 'g-' . $matches[1] : '';
                        $attendanceData = countAttendanceTC($conn, $id);
                        $attendance = "attendanceTC";
                        $status = checkAttendance($conn, $id, $attendance);
                        echo '<td>' . $id . '</td>';
                    }
                    

                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $attendanceData['attended'] . '</td>';
                    echo '<td>' . $attendanceData['absented'] . '</td>';
                    echo '<td>' . number_format($attendanceData['percentage'], 2) . '%</td>';
                    echo '<td>' . ($attendanceData['averageTime'] ? $attendanceData['averageTime'] : 'N/A') . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo 
                        '<td>
                            <form method="POST" action="individualDetails.php">
                                <button type="submit" class="btn btn-light rounded">
                                    <i class="bi bi-zoom-in"></i>';
                                    if ($table == 'students') {
                                        echo '<input type="hidden" name="id" value="' . $row['username'] . '">';
                                    } else {
                                        echo '<input type="hidden" name="id" value="' . $id . '">';    
                                    }
                                    echo '
                                    <input type="hidden" name="name" value="' . $row['name'] . '">
                                    <input type="hidden" name="table_name" value="' . $table . '">
                                </button>
                            </form>                    
                        </td>';

                    echo '</tr>';
                }
            } else {
                echo 'No users found in the selected table.';
            }
        } else {
            echo 'Database query error: ' . mysqli_error($conn);
        }
    ?>

</body>

</html>
