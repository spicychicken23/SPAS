<?php
require '../../assets/setup/db.inc.php';

if (isset($_POST['week_picker']) ) {

    $week_picked = $_POST['week_picker'];
    $week_start = date('Y-m-d', strtotime($week_picked));

    function checkAvailability($week_start) {
        global $conn;

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime($week_start . " +$i day"));

            $checkDateQuery = "SELECT COUNT(*) AS date_count FROM attendance WHERE DATE(date) = '$date'";
            $checkDateResult = mysqli_query($conn, $checkDateQuery);
            $dateCount = mysqli_fetch_assoc($checkDateResult)['date_count'];

            if ($dateCount > 0) {
                return true;
            }
        }

        return false;
    }

    $status = checkAvailability($week_start);

    if ($status) {

        function fetchWeeklyAttendanceData($date, $query) {
            global $conn;
        
            $result = mysqli_query($conn, $query);
        
            if ($result) {
                $data = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return false;
            }
        }
        
        function dailyAttendance($week_start) {
            global $conn;
        
            $week_end = date('Y-m-d', strtotime($week_start . ' + 4 days'));
        
            $query = "SELECT 
                        DATE(date) AS attendance_date,
                        COUNT(*) AS total_attendance
                    FROM attendance
                    WHERE DATE(date) BETWEEN '$week_start' AND '$week_end'
                    GROUP BY DATE(date)
                    ORDER BY DATE(date)";
        
            return fetchWeeklyAttendanceData($week_start, $query);
        }

        function weeklyStats($week_start) {
            global $conn;
        
            $totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM Students";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
            $totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total_students'] * 5;
        
            $week_end = date('Y-m-d', strtotime($week_start . ' + 4 days')); 
            $query = "SELECT 
                        DATE(date) AS attendance_date,
                        COUNT(*) AS total_attendance,
                        COUNT(DISTINCT barcodeId) AS weekly_students
                    FROM attendance
                    WHERE DATE(date) BETWEEN '$week_start' AND '$week_end'
                    GROUP BY DATE(date)
                    HAVING total_attendance > 0
                    ORDER BY DATE(date)";
        
            $attendanceData = fetchWeeklyAttendanceData($week_start, $query);
        
            if ($attendanceData) {
                $weeklyStats = array(
                    'labels' => array(), 
                    'data' => array(), 
                    'total_attendance' => 0,
                    'weekly_students' => 0,
                    'percentage_attendance' => 0,
                    'total_absentees' => 0
                );
        
                foreach ($attendanceData as $row) {
                    $weeklyStats['labels'][] = $row['attendance_date'];
                    $weeklyStats['data'][] = $row['total_attendance'];
                    $weeklyStats['total_attendance'] += $row['total_attendance'];
                }
                $weeklyStats['percentage_attendance'] = ($weeklyStats['total_attendance'] / $totalStudents) * 100;

                $weeklyStats['percentage_attendance_formatted'] = number_format($weeklyStats['percentage_attendance'], 2) . '%';
                $weeklyStats['total_absentees'] = $totalStudents - $weeklyStats['total_attendance'];
            } else {
                $weeklyStats = false;
            }
        
            return $weeklyStats;
        }
        

        function peakHour($week_start) {
            global $conn;
        
            $week_end = date('Y-m-d', strtotime($week_start . ' + 4 days'));
        
            $query = "SELECT 
                        DATE(date) AS attendance_date,
                        COUNT(*) AS total_attendance,
                        COUNT(DISTINCT barcodeId) AS peak_students,
                        HOUR(date) AS start_hour,
                        HOUR(date) + 1 AS end_hour
                    FROM attendance
                    WHERE DATE(date) BETWEEN '$week_start' AND '$week_end'
                    GROUP BY attendance_date, start_hour, end_hour
                    HAVING total_attendance > 0
                    ORDER BY COUNT(*) DESC
                    LIMIT 5";
        
            return fetchWeeklyAttendanceData($week_start, $query);
        }
        
        function timeToMinutes($hour, $minutes, $endHour = false) {
            $hourValue = $endHour ? $hour + 1 : $hour;
            return ($hourValue * 60) + $minutes;
        }
        
        function weeklyDifference($currentWeekStart) {
            $lastWeekStart = date('Y-m-d', strtotime($currentWeekStart . ' - 1 week'));
            $lastWeekStats = weeklyStats($lastWeekStart);
            $lastWeekPeakHr = peakHour($lastWeekStart);
        
            if (!$lastWeekStats || !$lastWeekPeakHr) {
                echo "Error: Last week's data is not available for comparison.";
                return false;
            }
        
            $currentWeekStats = weeklyStats($currentWeekStart);
            $currentWeekPeakHr = peakHour($currentWeekStart);
        
            $difference = array(
                'total_attendance' => $currentWeekStats['total_attendance'] - $lastWeekStats['total_attendance'],
                'percentage_attendance' => $currentWeekStats['percentage_attendance'] - $lastWeekStats['percentage_attendance'],
                'total_absentees' => $currentWeekStats['total_absentees'] - $lastWeekStats['total_absentees'],
                'peak_hour_difference' => array()
            );
        
            foreach (range(1, 5) as $index) {
                $currentPeakHour = $currentWeekPeakHr[$index - 1];
                $lastWeekPeakHour = $lastWeekPeakHr[$index - 1];
        
                $startHourDifference = $currentPeakHour['start_hour'] - $lastWeekPeakHour['start_hour'];
                $peakStudentsDifference = $currentPeakHour['peak_students'] - $lastWeekPeakHour['peak_students'];
        
                $difference['peak_hour_difference'][] = array(
                    'index' => $index,
                    'last_week_start_hour' => $lastWeekPeakHour['start_hour'],
                    'this_week_start_hour' => $currentPeakHour['start_hour'],
                    'start_hour_difference' => $startHourDifference,
                    'last_week_peak_students' => $lastWeekPeakHour['peak_students'],
                    'this_week_peak_students' => $currentPeakHour['peak_students'],
                    'peak_students_difference' => $peakStudentsDifference
                );
            }
        
            return $difference;
        }
        
        
        function weeklyClassAttendance($week_start) {
            global $conn;
        
            $week_end = date('Y-m-d', strtotime($week_start . ' + 4 days'));
        
            $query = "SELECT 
                        Class.id AS Class_id,
                        Class.standard AS class_standard,
                        Class.name AS class_name,
                        COUNT(*) AS total_attendance
                    FROM attendance
                    RIGHT JOIN Students ON attendance.barcodeId = Students.username
                    RIGHT JOIN Class ON Students.class = Class.id_name
                    WHERE DATE(attendance.date) BETWEEN '$week_start' AND '$week_end'
                    AND attendance.barcodeId LIKE 'm%'  
                    GROUP BY Class.id
                    ORDER BY Class.id";
        
            $attendanceData = fetchWeeklyAttendanceData($week_start, $query);
        
            $totalStudentsQuery = "SELECT Class.id AS Class_id, COUNT(*) AS total_students FROM Students JOIN Class ON Students.class = Class.id_name GROUP BY Class.id";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
        
            $weeklyClassAttendance = array();
        
            foreach ($attendanceData as $attendanceRow) {
                $class_id = $attendanceRow['Class_id'];
        
                $total_students = 0;
                while ($totalStudentsRow = mysqli_fetch_assoc($totalStudentsResult)) {
                    if ($totalStudentsRow['Class_id'] == $class_id) {
                        $total_students = $totalStudentsRow['total_students'] * 5;
                        break;
                    }
                }
        
                $absentees = $total_students - $attendanceRow['total_attendance'];
        
                $weeklyClassAttendance[] = array(
                    'Class_id' => $class_id,
                    'class_standard' => $attendanceRow['class_standard'],
                    'class_name' => $attendanceRow['class_name'],
                    'total_attendance' => $attendanceRow['total_attendance'],
                    'total_students' => $total_students,
                    'absentees' => $absentees,
                    'attendance_percentage' => ($attendanceRow['total_attendance'] / $total_students) * 100,
                );
            }
        
            return $weeklyClassAttendance;
        }
        
        
        
        function weeklyStandardAttendance($week_start) {
            global $conn;
        
            $week_end = date('Y-m-d', strtotime($week_start . ' + 4 days'));
        
            $query = "SELECT 
                    Class.standard AS standard,
                    COUNT(*) AS total_attendance
                FROM attendance
                RIGHT JOIN Students ON attendance.barcodeId = Students.username
                RIGHT JOIN Class ON Students.class = Class.id_name
                WHERE DATE(attendance.date) BETWEEN '$week_start' AND '$week_end'
                AND attendance.barcodeId LIKE 'm%'  
                GROUP BY Class.standard
                ORDER BY Class.standard";
        
            $attendanceData = fetchWeeklyAttendanceData($week_start, $query);
        
            $totalStudentsQuery = "SELECT Class.standard AS standard, COUNT(*) AS total_students FROM Students JOIN Class ON Students.class = Class.id_name GROUP BY Class.standard";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
        
            $weeklyStandardAttendance = array();
        
            foreach ($attendanceData as $attendanceRow) {
                $standard = $attendanceRow['standard'];
        
                $total_students = 0;
                while ($totalStudentsRow = mysqli_fetch_assoc($totalStudentsResult)) {
                    if ($totalStudentsRow['standard'] == $standard) {
                        $total_students = $totalStudentsRow['total_students'] * 5;
                        break;
                    }
                }
        
                $absentees = $total_students - $attendanceRow['total_attendance'];
        
                $attendance_percentage = number_format(($attendanceRow['total_attendance'] / $total_students) * 100, 2);

                $weeklyStandardAttendance[] = array(
                    'standard' => $standard,
                    'total_attendance' => $attendanceRow['total_attendance'],
                    'total_students' => $total_students,
                    'absentees' => $absentees,
                    'attendance_percentage' => $attendance_percentage,
                );

            }
        
            return $weeklyStandardAttendance;
        }
        
        
        $weeklyStats = weeklyStats($week_start);
        $peakHours = peakHour($week_start);
        $statsDifference = weeklyDifference($week_start);
        $dailyAttendance = dailyAttendance($week_start);
        $weeklyClassAttendance = weeklyClassAttendance($week_start);
        $weeklyStandardAttendance = weeklyStandardAttendance($week_start);
    } else {
        $statusMSG = "The chosen week is unavailable.";
    }
} else {
    $statusMSG = "Please select a week.";
}
?>
