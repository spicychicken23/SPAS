<html lang="en" class="type">
<!DOCTYPE html>

<head>
    <title>Weekly Analysis & Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../js/weekly.js"></script>
</head>

<body class="background">

    <?php
    include '../../assets/includes/header.php';
    check_logged_in();

    require 'includes/weekly.inc.php';
    ?>

    <div class=" container my-5">

        <h1 class="lightText">Weekly Analysis & Report</h1>
        <hr>
        
        <div class="row">
            <div class="col">
                <form action="weekly.php" method="POST">
                    <div class="input-group mb-3">
                        <input id="week_picker" name="week_picker" class="form-control" type="week" />
                        <button type="submit" name="search" id="searchButton" class="btn btn-light rounded-end">
                        <i class="bi bi-search analyseIcons"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <?php if (isset($_POST['week_picker']) && $status) { ?>
            <hr>

        <div id="summaryContainer" class="bg-light m-2 p-2 rounded-2">
            <div class="row p-2">
                <?php if (isset($weeklyStats)) {
                    echo '<h1>Starting Date: ' . date('d/m/y', strtotime($week_start)) . '</h1><hr>';
                    echo '<div class="col">';
                        echo "
                                <table class=\"table table-striped table-hover\">
                                    <tr>
                                        <th colspan=\"2\"> Summary Of Analysis </th>
                                    </tr>";

                        echo "<tr><td> Total Attendees: </td><td> {$weeklyStats['total_attendance']} </td></tr>";
                        echo "<tr><td> Total Absentees: </td><td> {$weeklyStats['total_absentees']} </td></tr>";
                        echo "<tr><td> Percentage of Attendance:  </td><td> {$weeklyStats['percentage_attendance_formatted']} </td></tr>";
                        echo "</table>";
                    echo '</div>';
                } else {
                    echo "Error retrieving weekly statistics.";
                } ?>
            </div>

            <div class="row m-2 p-2">
                <?php if (isset($dailyAttendance)) {
                    echo "
                    <table class=\"table table-striped table-hover\">
                        <tr>
                            <th> Date </th>
                            <th> Total Attendance </th>
                        </tr>";

                    foreach ($dailyAttendance as $day) {
                        echo "<tr>";
                        echo "<td>" . date('d-m-y', strtotime($day['attendance_date'])) . "</td>";
                        echo "<td>" . $day['total_attendance'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "Error retrieving daily attendance statistics.";
                } ?>
            </div>
        </div>

        <div id="summaryVisualisationContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="weeklyStatsHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#weeklyStatsCollapse" aria-expanded="false" aria-controls="weeklyStatsCollapse">
                            Weekly Attendance Visualisation
                        </button>
                    </h2>
                    <div id="weeklyStatsCollapse" class="accordion-collapse collapse" aria-labelledby="weeklyStatsHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <div class="row m-2 p-2">
                                <?php if (isset($weeklyStats)) { ?>
                                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                                    <script>
                                        var ctx = document.getElementById('attendanceChart').getContext('2d');
                                        var myChart = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: <?php echo json_encode($weeklyStats['labels']); ?>,
                                                datasets: [{
                                                    label: 'Total Attendance',
                                                    data: <?php echo json_encode($weeklyStats['data']); ?>,
                                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                    borderColor: 'rgba(75, 192, 192, 1)',
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                scales: {
                                                    y: {
                                                        beginAtZero: true
                                                    }
                                                }
                                            }
                                        });
                                    </script>
                                <?php } else {
                                    echo "Error producing summary visualization.";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="peakHourContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="peakHoursHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#peakHoursCollapse" aria-expanded="false" aria-controls="peakHoursCollapse">
                            Peak Hour Analysis
                        </button>
                    </h2>
                    <div id="peakHoursCollapse" class="accordion-collapse collapse" aria-labelledby="peakHoursHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <div class="row m-2 p-2">
                                <?php if (isset($peakHours)) { ?>
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <th>Date</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Total Attendance</th>
                                        </tr>

                                        <?php foreach ($peakHours as $peakHour) { ?>
                                            <tr>
                                                <td><?php echo date('d/m/y', strtotime($peakHour['attendance_date'])); ?></td>
                                                <td><?php echo $peakHour['start_hour'] . " am"; ?></td>
                                                <td><?php echo $peakHour['end_hour'] . " am"; ?></td>
                                                <td><?php echo $peakHour['total_attendance']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                <?php } else {
                                    echo "Error retrieving peak hour statistics.";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="peakHourVisualisationContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="peakHoursChartHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#peakHoursChartCollapse" aria-expanded="false" aria-controls="peakHoursChartCollapse">
                            Peak Hour Visualisation
                        </button>
                    </h2>
                    <div id="peakHoursChartCollapse" class="accordion-collapse collapse" aria-labelledby="peakHoursChartHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <div class="row m-2 p-2">
                                <?php if (isset($peakHours)) { ?>
                                    <canvas id="peakHoursChart" width="400" height="200"></canvas>

                                    <script>
                                        var ctx = document.getElementById('peakHoursChart').getContext('2d');

                                        var processedData = <?php
                                            $processedData = [];
                                            foreach ($peakHours as $peakHour) {
                                                $processedData[] = [
                                                    'attendance_date' => $peakHour['attendance_date'],
                                                    'total_attendance' => $peakHour['total_attendance'],
                                                    'start_hour' => max(5, min(10, $peakHour['start_hour'])), // limit to 5-10 range
                                                ];
                                            }
                                            echo json_encode($processedData);
                                        ?>;

                                        var peakHoursChart = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: processedData.map(function (hour) {
                                                    return hour['attendance_date'];
                                                }),
                                                datasets: [{
                                                    label: 'Total Attendance',
                                                    data: processedData.map(function (hour) {
                                                        return hour['total_attendance'];
                                                    }),
                                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                    borderColor: 'rgba(75, 192, 192, 1)',
                                                    borderWidth: 1
                                                }, {
                                                    label: 'Start Time',
                                                    data: processedData.map(function (hour) {
                                                        return hour['start_hour'];
                                                    }),
                                                    type: 'line',
                                                    borderColor: 'rgba(255, 99, 132, 1)',
                                                    borderWidth: 2,
                                                    yAxisID: 'y-axis-1'
                                                }]
                                            },
                                            options: {
                                                scales: {
                                                    y: [{
                                                        type: 'linear',
                                                        position: 'left',
                                                        id: 'y-axis-0',
                                                    }, {
                                                        type: 'linear',
                                                        position: 'right',
                                                        id: 'y-axis-1',
                                                        ticks: {
                                                            max: 10,
                                                            min: 5,
                                                            stepSize: 1
                                                        }
                                                    }]
                                                }
                                            }
                                        });
                                    </script>

                                <?php } else {
                                    echo "Error producing peak hour visualisation.";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="DifferenceAnalysisContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="differenceAnalysisHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#differenceAnalysisCollapse" aria-expanded="false" aria-controls="differenceAnalysisCollapse">
                            Difference Analysis
                        </button>
                    </h2>
                    <div id="differenceAnalysisCollapse" class="accordion-collapse collapse" aria-labelledby="differenceAnalysisHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <div class="row m-2 p-2">
                                <?php if (isset($_POST['week_picker']) && $statsDifference) { ?>
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Total Attendees:</td>
                                            <td><?php echo $statsDifference['total_attendance']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Absentees:</td>
                                            <td><?php echo $statsDifference['total_absentees']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Attendance Percentage:</td>
                                            <td><?php echo $statsDifference['percentage_attendance']; ?></td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan='2'>Time</th>
                                                <th>Difference</th>
                                                <th>Last Week Peak</th>
                                                <th>This Week Peak</th>
                                                <th>Peak Difference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($statsDifference['peak_hour_difference'] as $entry) { ?>
                                                <tr>
                                                    <td><?php echo $entry['last_week_start_hour']; ?></td>
                                                    <td><?php echo $entry['this_week_start_hour']; ?></td>
                                                    <td><?php echo $entry['start_hour_difference'] . ' hour(s)'; ?></td>
                                                    <td><?php echo $entry['last_week_peak_students']; ?></td>
                                                    <td><?php echo $entry['this_week_peak_students']; ?></td>
                                                    <td><?php echo $entry['peak_students_difference']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else {
                                    echo "Error displaying difference analysis.";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <?php if (isset($weeklyClassAttendance)) { ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Standard</th>
                                                <th>Class</th>
                                                <th>Total Attendees</th>
                                                <th>Total Absentees</th>
                                                <th>Attendance Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($weeklyClassAttendance as $class) { ?>
                                                <tr>
                                                    <td><?php echo $class['class_standard']; ?></td>
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
                                <?php if (isset($weeklyStandardAttendance)) { ?>
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
                                            <?php foreach ($weeklyStandardAttendance as $standard) { ?>
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
                                    <?php if (isset($weeklyStandardAttendance) && isset($weeklyClassAttendance)) { ?>
                                        
                                            <canvas id="standardAbsenteesChart" width="400" height="200"></canvas>
                                            <script>
                                                var standardLabels = <?php echo json_encode(array_column($weeklyStandardAttendance, 'standard')); ?>;
                                                var standardAbsenteesData = <?php echo json_encode(array_column($weeklyStandardAttendance, 'absentees')); ?>;

                                                var standardAbsenteesCtx = document.getElementById('standardAbsenteesChart').getContext('2d');
                                                var standardAbsenteesChart = new Chart(standardAbsenteesCtx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: standardLabels,
                                                        datasets: [{
                                                            label: 'Total Absentees',
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
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="col-md-6">
                <!-- Accordion Item - Class Attendance Chart -->
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
                                            var classLabels = <?php echo json_encode(array_column($weeklyClassAttendance, 'class_name')); ?>;
                                            var classAbsenteesData = <?php echo json_encode(array_column($weeklyClassAttendance, 'absentees')); ?>;

                                            var classAbsenteesCtx = document.getElementById('classAbsenteesChart').getContext('2d');
                                            var classAbsenteesChart = new Chart(classAbsenteesCtx, {
                                                type: 'pie',
                                                data: {
                                                    labels: classLabels,
                                                    datasets: [{
                                                        label: 'Total Absentees',
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
                                <?php } else {
                                    echo "Error producing attendance by standard.";
                                } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <?php } else { 
        echo $statusMSG;
        echo '<hr>';
    }?>
    </div>

    <script src="/SPAS/js/app.js"></script>
    <?php include '../../assets/includes/footer.php' ?>
    
</body>
</html>