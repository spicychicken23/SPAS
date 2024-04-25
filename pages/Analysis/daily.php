<html lang="en" class="type">
<!DOCTYPE html>

<head>
    <title>Daily Analysis & Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../js/daily.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="background">

    <?php
    include '../../assets/includes/header.php';
    check_logged_in();

    require 'includes/daily.inc.php';
    ?>

    <div class=" container my-5">

        <h1 class="lightText">Daily Analysis & Report</h1>
        <hr>
        <div class="row">
            <div class="col">
                <form action="daily.php" method="POST">
                    <div class="input-group mb-3">
                        <input id="date" name="date" class="form-control" type="date" />
                        <button type="submit" name="search" id="searchButton" class="btn btn-light rounded-end">
                        <i class="bi bi-search analyseIcons"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        
        

        <?php if (isset($_POST['date']) && $status)  { ?>

        <hr>
           
        <div id="summaryContainer" class="bg-light m-2 p-2 rounded-2">

            <div class="row p-2">
                <?php if (isset($dailyStats) && isset($peakHour)) {
                    
                    $attendanceData = fetchAttendanceData($date);
                    $labels = array_keys($attendanceData);
                    $data = array_values($attendanceData);

                    echo '<h1>Attendance Analysis On ' . date('d/m/y', strtotime($date)) . '</h1><hr>';
                    echo '<div class="col">';
                    if ($dailyStats) {
                        echo "
                                <table class=\"table table-striped table-hover\">
                                    <tr>
                                        <th colspan=\"2\"> Summary Of Analysis </th>
                                    </tr>";

                        echo "<tr><td> Total Attendees: </td><td> {$dailyStats['total_attendance']} </td></tr>";
                        echo "<tr><td> Total Absentees: </td><td> {$dailyStats['total_absentees']} </td></tr>";
                        echo "<tr><td> Percentage of Attendance:  </td><td> {$dailyStats['percentage_attendance']} </td></tr>";
                        echo "</table>";
                    } else {
                        echo "Error retrieving daily peak hour statistics.";
                    }
                    echo '</div>';
                } ?>

                <div class="col">
                    <?php if ($peakHour) {
                        echo "
                         <table class=\"table table-striped table-hover\">
                            <tr>
                                <th colspan=\"2\"> Peak Hour Analysis </th>
                             </tr>";

                        echo "<tr><td> Start Time: </td><td> {$peakHour['start_hour']} am</td></tr>";
                        echo "<tr><td> End Time: </td><td> {$peakHour['end_hour']} am</td></tr>";
                        echo "<tr><td> Total Attendance: </td><td> {$peakHour['total_attendance']} </td></tr>";
                        echo "</table>";

                    } else {
                        echo "Error retrieving daily attendance statistics.";
                    } ?>
                </div> 
            </div>
        </div>

        
        <div id="summaryVisualisation" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="summaryHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#summaryCollapse" aria-expanded="true" aria-controls="summaryCollapse">
                            Clocked Time Visualisation
                        </button>
                    </h2>
                    <div id="summaryCollapse" class="accordion-collapse collapse" aria-labelledby="summaryHeading" data-bs-parent="#accordion">
                    <div class="accordion-body">
                            <div class="row p-2">
                                <div class="col">
                                    <?php if ($dailyStats) { ?>
                                        <canvas id="attendanceChart" width="400" height="200"></canvas>
                                        <script>
                                            var ctx = document.getElementById('attendanceChart').getContext('2d');
                                            var myChart = new Chart(ctx, {
                                                type: 'line',
                                                data: {
                                                    labels: <?php echo json_encode(array_keys($attendanceData)); ?>,
                                                    datasets: [{
                                                        label: 'Clocked Attendance',
                                                        data: <?php echo json_encode(array_values($attendanceData)); ?>,
                                                        borderColor: 'rgba(75, 192, 192, 1)',
                                                        borderWidth: 2,
                                                        pointRadius: 5,
                                                        pointHoverRadius: 8,
                                                        fill: false
                                                    }]
                                                },
                                                options: {
                                                    scales: {
                                                        x: [{
                                                            type: 'linear',
                                                            position: 'bottom',
                                                            min: 0,
                                                            max: 180,
                                                            ticks: {
                                                                stepSize: 30
                                                            }
                                                        }],
                                                        y: [{
                                                            ticks: {
                                                                beginAtZero: true
                                                            }
                                                        }]
                                                    }
                                                }
                                            });
                                        </script>
                                    <?php } else {
                                        echo "Error retrieving daily peak hour statistics.";
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="analysisContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="differenceHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#differenceCollapse" aria-expanded="false" aria-controls="differenceCollapse">
                            Attendance Difference
                        </button>
                    </h2>
                    <div id="differenceCollapse" class="accordion-collapse collapse" aria-labelledby="differenceHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <?php if (isset($difference)) { ?>
                                <div class="row p-2">
                                    <div class="col">
                                        <?php if ($difference) { ?>
                                            <table class="table table-striped table-hover">
                                                <tr>
                                                    <th colspan="2">Comparison To Previous Attendance</th>
                                                </tr>
                                                <?php
                                                    echo "<tr><td> Date: </td><td> " . date('d/m/y', strtotime($difference['latest_date'])) . " </td></tr>";
                                                    echo "<tr><td> Total attendees: </td><td> {$difference['tot_att_dif']}</td></tr>";
                                                    echo "<tr><td> Total absentees: </td><td> {$difference['tot_abs_dif']}</td></tr>";
                                                    echo "<tr><td> Percentage difference: </td><td> {$difference['per_att_dif']}</td></tr>";
                                                    echo "<tr><td> Peak Hours Difference: </td><td> {$difference['peak_hour_dif']} minutes</td></tr>";
                                                ?>
                                            </table>
                                        <?php } else {
                                            echo "Error retrieving daily difference statistics.";
                                        } ?>
                                    </div>
                                    <div class="col">
                                        <table class="table table-striped table-hover">
                                            <?php sort_byStandard($date); ?>
                                        </table>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="analysisVisualisation" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="visualisationHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#visualisationCollapse" aria-expanded="false" aria-controls="visualisationCollapse">
                            Attendance Difference Visualisation
                        </button>
                    </h2>
                    <div id="visualisationCollapse" class="accordion-collapse collapse" aria-labelledby="visualisationHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <?php
                            $visualisationQuery = "SELECT latest_date, tot_att_dif, tot_abs_dif, per_att_dif, peak_hour_dif FROM diff_visualisation";
                            $visualisationResult = mysqli_query($conn, $visualisationQuery);

                            if ($visualisationResult) {
                                $visualisationData = mysqli_fetch_assoc($visualisationResult);

                                echo '
                                    <canvas id="attendanceDiffChart" width="400" height="200"></canvas>
                                    <script>
                                        var diffCtx = document.getElementById("attendanceDiffChart").getContext("2d");
                                        var diffChart = new Chart(diffCtx, {
                                            type: "bar",
                                            data: {
                                                labels: ["Total Attendance Difference", "Total Absentees Difference", "Percentage Attendance Difference", "Peak Hour Difference"],
                                                datasets: [{
                                                    label: "Latest vs Current Attendance",
                                                    data: [' . $visualisationData['tot_att_dif'] . ', ' . $visualisationData['tot_abs_dif'] . ', ' . $visualisationData['per_att_dif'] . ', ' . $visualisationData['peak_hour_dif'] . '],
                                                    backgroundColor: [
                                                        "rgba(255, 99, 132, 0.2)",
                                                        "rgba(54, 162, 235, 0.2)",
                                                        "rgba(255, 206, 86, 0.2)",
                                                        "rgba(75, 192, 192, 0.2)"
                                                    ],
                                                    borderColor: [
                                                        "rgba(255, 99, 132, 1)",
                                                        "rgba(54, 162, 235, 1)",
                                                        "rgba(255, 206, 86, 1)",
                                                        "rgba(75, 192, 192, 1)"
                                                    ],
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                scales: {
                                                    y: {
                                                        beginAtZero: true
                                                    }
                                                },
                                                tooltips: {
                                                    enabled: false
                                                },
                                                legend: {
                                                    display: false
                                                }
                                            }
                                        });
                                    </script>';
                            } else {
                                echo "Error fetching visualization data.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="analysisClassContainer" class="bg-light m-2 p-2 rounded-2">
            <div id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="classTableHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#classTableCollapse" aria-expanded="false" aria-controls="classTableCollapse">
                            Attendance Analysis By Class
                        </button>
                    </h2>
                    <div id="classTableCollapse" class="accordion-collapse collapse" aria-labelledby="classTableHeading" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <div class="row p-4">
                                <table class="table table-striped table-hover table-wrapper">
                                    <?php sort_byClass($date); ?>
                                </table>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="detailedVisualisation" class="bg-light m-2 p-2 rounded-2">
            <div class="accordion" id="visualisationAccordion">

                <div class = "row p-2">
                    <div class = "col">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="classAttendeesHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#classAttendees" aria-expanded="true" aria-controls="classAttendees">
                                    Class Attendees Pie Chart
                                </button>
                            </h2>
                            <div id="classAttendees" class="accordion-collapse collapse" aria-labelledby="classAttendeesHeading">
                                <div class="accordion-body">
                                    <div class="bg-light m-2 p-2 rounded-2">
                                        <div class="row">
                                            <div class="col">
                                                <?php displayClassAttendeesPieChart(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "col">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="classAbsenteesHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#classAbsentees" aria-expanded="true" aria-controls="classAbsentees">
                                    Class Absentees Pie Chart
                                </button>
                            </h2>
                            <div id="classAbsentees" class="accordion-collapse collapse" aria-labelledby="classAbsenteesHeading">
                                <div class="accordion-body">
                                    <div class="bg-light m-2 p-2 rounded-2">
                                        <div class="row">
                                            <div class="col">
                                                <?php displayClassAbsenteesPieChart(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class = "row p-2">
                    <div class = "col">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="standardAttendeesHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#standardAttendees" aria-expanded="true" aria-controls="standardAttendees">
                                    Standard Attendees Pie Chart
                                </button>
                            </h2>
                            <div id="standardAttendees" class="accordion-collapse collapse" aria-labelledby="standardAttendeesHeading">
                                <div class="accordion-body">
                                    <div class="bg-light m-2 p-2 rounded-2">
                                        <div class="row">
                                            <div class="col">
                                                <?php displayStandardAttendeesPieChart(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "col">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="standardAbsenteesHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#standardAbsentees" aria-expanded="true" aria-controls="standardAbsentees">
                                    Standard Absentees Pie Chart
                                </button>
                            </h2>
                            <div id="standardAbsentees" class="accordion-collapse collapse" aria-labelledby="standardAbsenteesHeading">
                                <div class="accordion-body">
                                    <div class="bg-light m-2 p-2 rounded-2">
                                        <div class="row">
                                            <div class="col">
                                                <?php displayStandardAbsenteesPieChart(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    
    <?php } else { 
        echo $statusMSG;
        echo '<hr';
    }?>
    </div>

    <?php include '../../assets/includes/footer.php' ?>

</body>

</html>
