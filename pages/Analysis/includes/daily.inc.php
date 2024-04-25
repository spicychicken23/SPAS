<?php
require '../../assets/setup/db.inc.php';

    if (isset($_POST['date']) ) {
    $date = $_POST['date'];

    function checkAvailability ($date) {
        global $conn;
        $checkDateQuery = "SELECT COUNT(*) AS date_count FROM attendance WHERE DATE(date) = '$date'";
        $checkDateResult = mysqli_query($conn, $checkDateQuery);
        $dateCount = mysqli_fetch_assoc($checkDateResult)['date_count'];

        if ($dateCount > 0) {
            return true;
        } else {
            return false;
        }
    }

    $status = checkAvailability($date);


    if ($status) {
        

        function fetchData($date, $query) {
            global $conn;

            $result = mysqli_query($conn, $query);

            if ($result) {
                return  mysqli_fetch_assoc($result);
            } else {
                return false;
            }
        }
        
        function peakHour($date) {
            $query = "SELECT 
                        COUNT(*) AS total_attendance,
                        COUNT(DISTINCT barcodeId) AS peak_students,
                        HOUR(date) AS start_hour,
                        HOUR(date) + 1 AS end_hour
                    FROM attendance
                    WHERE DATE(date) = '$date'
                    GROUP BY start_hour, end_hour
                    ORDER BY COUNT(*) DESC
                    LIMIT 5";
        
            return fetchData($date, $query);
        }
        
        function dailyStats($date) {
            global $conn;

            $totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM Students";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
            $totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total_students'];

            $query = "SELECT 
                        COUNT(*) AS total_attendance,
                        COUNT(DISTINCT barcodeId) AS daily_students,
                        DATE_FORMAT(date, '%H:%i') AS attendance_time
                    FROM attendance
                    WHERE DATE(date) = '$date'";

            $attendanceData = fetchData($date, $query);

            if ($attendanceData) {
                $dailyStats['total_attendance'] = $attendanceData['total_attendance'];
                $dailyStats['daily_students'] = $attendanceData['daily_students'];
                $dailyStats['attendance_time'] = $attendanceData['attendance_time'];

                $dailyStats['percentage_attendance'] = number_format(($attendanceData['daily_students'] / $totalStudents) * 100, 2) . '%';
                $dailyStats['total_absentees'] = $totalStudents - $attendanceData['daily_students'];
            } else {
                $dailyStats = false;
            }

            return $dailyStats;
        }

        function calculateDifference($date) {
            global $conn;
        
            $latestDateQuery = "SELECT MAX(DATE(DATE)) AS latest_date FROM attendance WHERE DATE(DATE) < '$date'";
            $latestDateResult = mysqli_query($conn, $latestDateQuery);
            $latestDate = mysqli_fetch_assoc($latestDateResult)['latest_date'];
        
            if ($latestDate) {
                $latestPeakHour = peakHour($latestDate);
                $latestDailyStats = dailyStats($latestDate);
        
                $peakHour = peakHour($date);
                $dailyStats = dailyStats($date);
        
                if ($latestPeakHour && $latestDailyStats) {
                    $difference['tot_att_dif'] = $dailyStats['total_attendance'] - $latestDailyStats['total_attendance'];
                    $difference['tot_abs_dif'] = $dailyStats['total_absentees'] - $latestDailyStats['total_absentees'];
        
                    $daily_percentage = floatval(str_replace('%', '', $dailyStats['percentage_attendance']));
                    $latest_percentage = floatval(str_replace('%', '', $latestDailyStats['percentage_attendance']));
                    $difference['per_att_dif'] = $daily_percentage - $latest_percentage;
        
                    function timeToMinutes($hour, $minutes, $endHour = false)
                    {
                        $hourValue = $endHour ? $hour + 1 : $hour;
                        return ($hourValue * 60) + $minutes;
                    }
        
                    $peakHourDifference = timeToMinutes($peakHour['start_hour'], 0, true) - timeToMinutes($latestPeakHour['start_hour'], 0, true);
                    $difference['peak_hour_dif'] = $peakHourDifference;
        
                    $difference['latest_date'] = $latestDate;
        
                    $insertQuery = "INSERT INTO diff_visualisation (latest_date, tot_att_dif, tot_abs_dif, per_att_dif, peak_hour_dif)
                                    VALUES ('$latestDate', {$difference['tot_att_dif']}, {$difference['tot_abs_dif']}, {$difference['per_att_dif']}, {$difference['peak_hour_dif']})";
                    mysqli_query($conn, $insertQuery);
        
                    return $difference;
                }
            }
        
            return false;
        }
        
        function sort_byClass($date) {
            global $conn;
        
            $attendanceQuery = "SELECT barcodeId, DATE_FORMAT(date, '%H:%i') AS attendance_time FROM attendance WHERE DATE(date) = '$date'";
            $attendanceResult = mysqli_query($conn, $attendanceQuery);
        
            if ($attendanceResult) {
                $attendanceByClass = array();
                $absenteesByClass = array();
        
                $studentsQuery = "SELECT username, class FROM Students";
                $studentsResult = mysqli_query($conn, $studentsQuery);
        
                if ($studentsResult) {
                    while ($student = mysqli_fetch_assoc($studentsResult)) {
                        $username = $student['username'];
                        $class = $student['class'];
        
                        if (!isset($attendanceByClass[$class])) {
                            $attendanceByClass[$class] = array();
                            $absenteesByClass[$class] = 0;
                        }
        
                        $attended = false;
                        mysqli_data_seek($attendanceResult, 0);
                        while ($row = mysqli_fetch_assoc($attendanceResult)) {
                            if ($row['barcodeId'] === $username) {
                                $attended = true;
                                break;
                            }
                        }
        
                        $attendanceByClass[$class][] = $attended;
                        if (!$attended) {
                            $absenteesByClass[$class]++;
                        }
                    }
        
                    echo '
                            <tr>
                                <th> Classes </th>
                                <th> Attendees </th>
                                <th> Absentees </th>
                            </tr>';
                    foreach ($attendanceByClass as $class => $attendance) {
                        $numAttendees = count(array_filter($attendance, function ($attended) {
                            return $attended;
                        }));
                        $numAbsentees = $absenteesByClass[$class];
        
                        // Check if a record already exists for the given date and class
                        $existingRecordQuery = "SELECT * FROM detailedClass_Visualisation WHERE date = '$date' AND class = '$class'";
                        $existingRecordResult = mysqli_query($conn, $existingRecordQuery);
        
                        if (mysqli_num_rows($existingRecordResult) > 0) {
                            // Update existing record
                            $updateQuery = "UPDATE detailedClass_Visualisation SET attendees = $numAttendees, absentees = $numAbsentees WHERE date = '$date' AND class = '$class'";
                            mysqli_query($conn, $updateQuery);
                        } else {
                            // Insert data into detailedClass_Visualisation table
                            $insertQuery = "INSERT INTO detailedClass_Visualisation (class, attendees, absentees, date)
                                            VALUES ('$class', $numAttendees, $numAbsentees, '$date')";
                            mysqli_query($conn, $insertQuery);
                        }
        
                        echo "
                            <tr>
                                <td> $class </td>
                                <td> $numAttendees </td>
                                <td> $numAbsentees </td>
                            </tr>";
                    }
                } else {
                    echo "Error fetching students' data.";
                }
            } else {
                echo "Error fetching attendance data.";
            }
        }
        
        function sort_byStandard($date) {
            global $conn;
        
            $attendanceQuery = "SELECT barcodeId, DATE_FORMAT(date, '%H:%i') AS attendance_time FROM attendance WHERE DATE(date) = '$date'";
            $attendanceResult = mysqli_query($conn, $attendanceQuery);
        
            if ($attendanceResult) {
                $attendanceByStandard = array();
                $absenteesByStandard = array();
        
                $studentsQuery = "SELECT username, class FROM Students";
                $studentsResult = mysqli_query($conn, $studentsQuery);
        
                if ($studentsResult) {
                    while ($student = mysqli_fetch_assoc($studentsResult)) {
                        $username = $student['username'];
                        $class = $student['class'];
        
                        $standard = explode(" ", $class)[0];
        
                        if (!isset($attendanceByStandard[$standard])) {
                            $attendanceByStandard[$standard] = array();
                            $absenteesByStandard[$standard] = 0;
                        }
        
                        $attended = false;
                        mysqli_data_seek($attendanceResult, 0);
                        while ($row = mysqli_fetch_assoc($attendanceResult)) {
                            if ($row['barcodeId'] === $username) {
                                $attended = true;
                                break;
                            }
                        }
        
                        $attendanceByStandard[$standard][] = $attended;
                        if (!$attended) {
                            $absenteesByStandard[$standard]++;
                        }
                    }
        
                    echo '
                            <tr>
                                <th> Standard </th>
                                <th> Attendees </th>
                                <th> Absentees </th>
                            </tr>';
        
                    foreach ($attendanceByStandard as $standard => $attendance) {
                        $numAttendees = count(array_filter($attendance, function ($attended) {
                            return $attended;
                        }));
                        $numAbsentees = $absenteesByStandard[$standard];
        
                        // Check if a record already exists for the given date and standard
                        $existingRecordQuery = "SELECT * FROM detailedStandard_Visualisation WHERE date = '$date' AND standard = '$standard'";
                        $existingRecordResult = mysqli_query($conn, $existingRecordQuery);
        
                        if (mysqli_num_rows($existingRecordResult) > 0) {
                            // Update existing record
                            $updateQuery = "UPDATE detailedStandard_Visualisation SET attendees = $numAttendees, absentees = $numAbsentees WHERE date = '$date' AND standard = '$standard'";
                            mysqli_query($conn, $updateQuery);
                        } else {
                            // Insert data into detailedStandard_Visualisation table
                            $insertQuery = "INSERT INTO detailedStandard_Visualisation (standard, attendees, absentees, date)
                                            VALUES ('$standard', $numAttendees, $numAbsentees, '$date')";
                            mysqli_query($conn, $insertQuery);
                        }
        
                        echo "
                            <tr>
                                <td> $standard </td>
                                <td> $numAttendees </td>
                                <td> $numAbsentees </td>
                            </tr>";
                    }
                } else {
                    echo "Error fetching students' data.";
                }
            } else {
                echo "Error fetching attendance data.";
            }
        }
        

        function fetchAttendanceData($date) {
            global $conn;
        
            $query = "SELECT DATE_FORMAT(date, '%H:%i') AS attendance_time, COUNT(DISTINCT barcodeId) AS total_attendance
                    FROM attendance
                    WHERE DATE(date) = '$date'
                    GROUP BY HOUR(date), MINUTE(date)
                    ORDER BY date";
        
            $result = mysqli_query($conn, $query);
        
            $attendanceData = array();
        
            $allTimeIntervals = array_map(
                function ($hour) {
                    return sprintf('%02d:00', $hour);
                },
                range(6, 9)
            );
        
            foreach ($allTimeIntervals as $interval) {
                $attendanceData[$interval] = 0;
            }
        
            while ($row = mysqli_fetch_assoc($result)) {
                $attendanceData[$row['attendance_time']] = $row['total_attendance'];
            }
        
            return $attendanceData;
        }

        function displayClassAttendeesPieChart() {
            global $conn;
        
            $classQuery = "SELECT class, SUM(attendees) as total_attendees FROM detailedClass_Visualisation GROUP BY class";
            $classResult = mysqli_query($conn, $classQuery);
        
            if ($classResult) {
                $classData = mysqli_fetch_all($classResult, MYSQLI_ASSOC);
        
                echo '
                    <canvas id="classAttendeesPieChart" width="400" height="200"></canvas>
                    <script>
                        var classAttendeesCtx = document.getElementById("classAttendeesPieChart").getContext("2d");
                        var classAttendeesPieChart = new Chart(classAttendeesCtx, {
                            type: "pie",
                            data: {
                                labels: ' . json_encode(array_column($classData, 'class')) . ',
                                datasets: [{
                                    data: ' . json_encode(array_column($classData, 'total_attendees')) . ',
                                    backgroundColor: [
                                        "rgba(255, 99, 132, 0.2)",
                                        "rgba(54, 162, 235, 0.2)",
                                        "rgba(255, 206, 86, 0.2)"
                                    ],
                                    borderColor: [
                                        "rgba(255, 99, 132, 1)",
                                        "rgba(54, 162, 235, 1)",
                                        "rgba(255, 206, 86, 1)"
                                    ],
                                    borderWidth: 1
                                }]
                            }
                        });
                    </script>';
            } else {
                echo "Error fetching data for class attendees pie chart.";
            }
        }
        
        function displayClassAbsenteesPieChart() {
            global $conn;
        
            $classQuery = "SELECT class, SUM(absentees) as total_absentees FROM detailedClass_Visualisation GROUP BY class";
            $classResult = mysqli_query($conn, $classQuery);
        
            if ($classResult) {
                $classData = mysqli_fetch_all($classResult, MYSQLI_ASSOC);
        
                echo '
                    <canvas id="classAbsenteesPieChart" width="400" height="200"></canvas>
                    <script>
                        var classAbsenteesCtx = document.getElementById("classAbsenteesPieChart").getContext("2d");
                        var classAbsenteesPieChart = new Chart(classAbsenteesCtx, {
                            type: "pie",
                            data: {
                                labels: ' . json_encode(array_column($classData, 'class')) . ',
                                datasets: [{
                                    data: ' . json_encode(array_column($classData, 'total_absentees')) . ',
                                    backgroundColor: [
                                        "rgba(255, 99, 132, 0.2)",
                                        "rgba(54, 162, 235, 0.2)",
                                        "rgba(255, 206, 86, 0.2)"
                                    ],
                                    borderColor: [
                                        "rgba(255, 99, 132, 1)",
                                        "rgba(54, 162, 235, 1)",
                                        "rgba(255, 206, 86, 1)"
                                    ],
                                    borderWidth: 1
                                }]
                            }
                        });
                    </script>';
            } else {
                echo "Error fetching data for class absentees pie chart.";
            }
        }
        
        function displayStandardAttendeesPieChart() {
            global $conn;
        
            $standardQuery = "SELECT standard, SUM(attendees) as total_attendees FROM detailedStandard_Visualisation GROUP BY standard";
            $standardResult = mysqli_query($conn, $standardQuery);
        
            if ($standardResult) {
                $standardData = mysqli_fetch_all($standardResult, MYSQLI_ASSOC);
        
                echo '
                    <canvas id="standardAttendeesPieChart" width="400" height="200"></canvas>
                    <script>
                        var standardAttendeesCtx = document.getElementById("standardAttendeesPieChart").getContext("2d");
                        var standardAttendeesPieChart = new Chart(standardAttendeesCtx, {
                            type: "pie",
                            data: {
                                labels: ' . json_encode(array_column($standardData, 'standard')) . ',
                                datasets: [{
                                    data: ' . json_encode(array_column($standardData, 'total_attendees')) . ',
                                    backgroundColor: [
                                        "rgba(255, 99, 132, 0.2)",
                                        "rgba(54, 162, 235, 0.2)",
                                        "rgba(255, 206, 86, 0.2)"
                                    ],
                                    borderColor: [
                                        "rgba(255, 99, 132, 1)",
                                        "rgba(54, 162, 235, 1)",
                                        "rgba(255, 206, 86, 1)"
                                    ],
                                    borderWidth: 1
                                }]
                            }
                        });
                    </script>';
            } else {
                echo "Error fetching data for standard attendees pie chart.";
            }
        }
        
        function displayStandardAbsenteesPieChart() {
            global $conn;
        
            $standardQuery = "SELECT standard, SUM(absentees) as total_absentees FROM detailedStandard_Visualisation GROUP BY standard";
            $standardResult = mysqli_query($conn, $standardQuery);
        
            if ($standardResult) {
                $standardData = mysqli_fetch_all($standardResult, MYSQLI_ASSOC);
        
                echo '
                    <canvas id="standardAbsenteesPieChart" width="400" height="200"></canvas>
                    <script>
                        var standardAbsenteesCtx = document.getElementById("standardAbsenteesPieChart").getContext("2d");
                        var standardAbsenteesPieChart = new Chart(standardAbsenteesCtx, {
                            type: "pie",
                            data: {
                                labels: ' . json_encode(array_column($standardData, 'standard')) . ',
                                datasets: [{
                                    data: ' . json_encode(array_column($standardData, 'total_absentees')) . ',
                                    backgroundColor: [
                                        "rgba(255, 99, 132, 0.2)",
                                        "rgba(54, 162, 235, 0.2)",
                                        "rgba(255, 206, 86, 0.2)"
                                    ],
                                    borderColor: [
                                        "rgba(255, 99, 132, 1)",
                                        "rgba(54, 162, 235, 1)",
                                        "rgba(255, 206, 86, 1)"
                                    ],
                                    borderWidth: 1
                                }]
                            }
                        });
                    </script>';
            } else {
                echo "Error fetching data for standard absentees pie chart.";
            }
        }
        
        
        
        $dailyStats = dailyStats($date);
        $peakHour = peakHour($date);

        $difference = calculateDifference($date);
        $attendanceData = fetchAttendanceData($date);
    } else {
        $statusMSG = "Data for the choosen date is unavailable.";
    }
} else {
    $statusMSG = "Please select a date.";
}
?>
