<html lang="en" class="type">
<!DOCTYPE html>

<head>
    <title>Monthly Analysis & Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../js/monthly.js"></script>
</head>

<body class="background">

<?php

    require '../../assets/includes/header.php';
    check_logged_in();
    require 'includes/monthly.inc.php';
    $statusMSG = "The choosen month is unavailable";

?>

<div class="container my-5">
    <h1 class="lightText">Monthly Analysis & Report</h1>
    <hr>
    <div class="row">
        <div class="col">
            <form action="monthly.php" method="POST">
                <div class="input-group mb-3">
                    <input id="month_picker" name="month_picker" class="form-control" type="month" />
                    <button type="submit" name="search" id="searchButton" class="btn btn-light rounded-end">
                        <i class="bi bi-search analyseIcons"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <hr>


<?php if (isset($_POST['month_picker']) && $status) { ?>
    <div id="summaryContainer" class="bg-light m-2 p-2 rounded-2">
        <div class="row p-2">
            <?php if (isset($monthlyStats)) {
                echo '<h1>Selected Month: ' . date('F Y', strtotime($month_start)) . '</h1><hr>';
                echo '<div class="col">';
                echo "
                    <table class=\"table table-striped table-hover\">
                        <tr>
                            <th colspan=\"2\"> Summary Of Analysis </th>
                        </tr>";

                echo "<tr><td> Total Attendees: </td><td> {$monthlyStats['total_attendance']} </td></tr>";
                echo "<tr><td> Total Absentees: </td><td> {$monthlyStats['total_absentees']} </td></tr>";
                echo "<tr><td> Percentage of Attendance:  </td><td> {$monthlyStats['percentage_attendance_formatted']} </td></tr>";
                echo "</table>";
                echo '</div>';
            } else {
                echo "Error retrieving monthly statistics.";
            } ?>
        </div>
    </div> 

<div id="classContainer" class="bg-light m-2 p-2 rounded-2">
    <div id="accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="classAttendanceHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#classAttendanceCollapse" aria-expanded="false" aria-controls="classAttendanceCollapse">
                    Attendance by Class
                </button>
            </h2>
            <div id="classAttendanceCollapse" class="accordion-collapse collapse" aria-labelledby="classAttendanceHeading" data-bs-parent="#accordion">
                <div class="accordion-body">
                    <div class="row m-2 p-2">
                        <?php if (isset($monthlyClassAttendance)) { ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Total Attendees</th>
                                        <th>Total Absentees</th>
                                        <th>Attendance Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monthlyClassAttendance as $class) { ?>
                                        <tr>
                                            <td><?php echo $class['class_name']; ?></td>
                                            <td><?php echo $class['total_attendance']; ?></td>
                                            <td><?php echo $class['absentees']; ?></td>
                                            <td><?php echo $class['attendance_percentage']; ?>%</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else {
                            echo "Error producing attendance by class.";
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="standardContainer" class="bg-light m-2 p-2 rounded-2">
    <div id="accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="standardAttendanceHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#standardAttendanceCollapse" aria-expanded="false" aria-controls="standardAttendanceCollapse">
                    Attendance by Standard
                </button>
            </h2>
            <div id="standardAttendanceCollapse" class="accordion-collapse collapse" aria-labelledby="standardAttendanceHeading" data-bs-parent="#accordion">
                <div class="accordion-body">
                    <div class="row m-2 p-2">
                        <?php if (isset($monthlyStandardAttendance)) { ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Standard</th>
                                        <th>Total Attendance</th>
                                        <th>Total Absentees</th>
                                        <th>Attendance Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monthlyStandardAttendance as $standard) { ?>
                                        <tr>
                                            <td><?php echo $standard['standard']; ?></td>
                                            <td><?php echo $standard['total_attendance']; ?></td>
                                            <td><?php echo $standard['absentees']; ?></td>
                                            <td><?php echo $standard['attendance_percentage']; ?>%</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else {
                            echo "Error producing attendance by standard.";
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="absenteesVisualisationContainer" class="bg-light m-2 p-2 rounded-2">
    <div class="row m-2 p-2">
        <div class="col-md-6">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="standardAttendanceChartHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#standardAttendanceChartCollapse" aria-expanded="false" aria-controls="standardAttendanceChartCollapse">
                            Standard Attendance Chart
                        </button>
                    </h2>
                    <div id="standardAttendanceChartCollapse" class="accordion-collapse collapse" aria-labelledby="standardAttendanceChartHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <?php if (isset($monthlyStandardAttendance) && isset($monthlyClassAttendance)) { ?>
                                <canvas id="standardAbsenteesChart" width="400" height="200"></canvas>
                                <script>
                                    var standardLabels = <?php echo json_encode(array_column($monthlyStandardAttendance, 'standard')); ?>;
                                    var standardAbsenteesData = <?php echo json_encode(array_column($monthlyStandardAttendance, 'total_attendance')); ?>;

                                    var standardAbsenteesCtx = document.getElementById('standardAbsenteesChart').getContext('2d');
                                    var standardAbsenteesChart = new Chart(standardAbsenteesCtx, {
                                        type: 'pie',
                                        data: {
                                            labels: standardLabels,
                                            datasets: [{
                                                label: 'Total Attendace',
                                                data: standardAbsenteesData,
                                                backgroundColor: [
                                                    'rgba(255, 99, 132, 0.2)',
                                                    'rgba(255, 159, 64, 0.2)',
                                                    'rgba(255, 205, 86, 0.2)',
                                                    'rgba(75, 192, 192, 0.2)',
                                                    'rgba(54, 162, 235, 0.2)',
                                                ],
                                                borderColor: [
                                                    'rgba(255, 99, 132, 1)',
                                                    'rgba(255, 159, 64, 1)',
                                                    'rgba(255, 205, 86, 1)',
                                                    'rgba(75, 192, 192, 1)',
                                                    'rgba(54, 162, 235, 1)',
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    display: false
                                                }
                                            }
                                        }
                                    });
                                </script>
                            <?php } else {
                                echo "Error producing attendance by standard.";
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="accordion-item">
                <h2 class="accordion-header" id="classAttendanceChartHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#classAttendanceChartCollapse" aria-expanded="false" aria-controls="classAttendanceChartCollapse">
                        Class Attendance Chart
                    </button>
                </h2>
                <div id="classAttendanceChartCollapse" class="accordion-collapse collapse" aria-labelledby="classAttendanceChartHeading" data-bs-parent="#accordion">
                    <div class="accordion-body">
                        <canvas id="classAbsenteesChart" width="400" height="200"></canvas>
                        <script>
                            var classLabels = <?php echo json_encode(array_column($monthlyClassAttendance, 'class_name')); ?>;
                            var classAbsenteesData = <?php echo json_encode(array_column($monthlyClassAttendance, 'total_attendance')); ?>;

                            var classAbsenteesCtx = document.getElementById('classAbsenteesChart').getContext('2d');
                            var classAbsenteesChart = new Chart(classAbsenteesCtx, {
                                type: 'pie',
                                data: {
                                    labels: classLabels,
                                    datasets: [{
                                        label: 'Total Attendace',
                                        data: classAbsenteesData,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.2)',
                                            'rgba(255, 159, 64, 0.2)',
                                            'rgba(255, 205, 86, 0.2)',
                                            'rgba(75, 192, 192, 0.2)',
                                            'rgba(54, 162, 235, 0.2)',
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(255, 159, 64, 1)',
                                            'rgba(255, 205, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(54, 162, 235, 1)',
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php 
    } else { 
        echo $statusMSG;
    }?>

</div>

    <script src="/SPAS/js/app.js"></script>
    <?php include '../../assets/includes/footer.php' ?>
    
</body>
</html>
