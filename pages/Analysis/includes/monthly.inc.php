<?php
require '../../assets/setup/db.inc.php';
$month_start = isset($_POST['month_picker']) ? $_POST['month_picker'] : date('Y-m-01');

if (isset($_POST['month_picker'])) {
    $month_picked = $_POST['month_picker'];
    $start_date = date('Y-m-01', strtotime($month_picked)); // First day of the selected month
    $end_date = date('Y-m-t', strtotime($month_picked));   // Last day of the selected month

    function checkAvailability($month_start, $conn) {
        $days_in_month = date('t', strtotime($month_start));
    
        for ($i = 0; $i < $days_in_month; $i++) {
            $date = date('Y-m-d', strtotime($month_start . " +$i day"));
    
            $checkDateQuery = "SELECT COUNT(*) AS date_count FROM attendance WHERE DATE(date) = '$date'";
            $checkDateResult = mysqli_query($conn, $checkDateQuery);
            $dateCount = mysqli_fetch_assoc($checkDateResult)['date_count'];
    
            if ($dateCount > 0) {
                return true;
            }
        }
    
        return false;
    }

    $status = checkAvailability($start_date, $conn);

    if ($status) {
        function fetchMonthlyAttendanceData($month_start, $month_end, $query, $conn) {
            $result = mysqli_query($conn, $query);

            if ($result) {
                $attendanceData = array();

                while ($row = mysqli_fetch_assoc($result)) {
                    $attendanceData[] = $row;
                }

                return $attendanceData;
            } else {
                return false;
            }
        }

        function totalSchoolDay($month_start, $month_end, $conn) {
            $query = "SELECT 
                        COUNT(DISTINCT DATE(date)) AS total_school_days
                    FROM attendance
                    WHERE DATE(date) BETWEEN '$month_start' AND '$month_end'";
        
            $result = mysqli_query($conn, $query);
        
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                return isset($row['total_school_days']) ? $row['total_school_days'] : 0;
            } else {
                return 0;
            }
        }

        function getStandardFromClass($class) {
            preg_match('/T([1-5])/', $class, $matches);
            return isset($matches[1]) ? $matches[1] : null;
        }

        

        function monthlyStats($month_start)  {
            global $conn;
            $days_in_month = date('t', strtotime($month_start));
            $month_end = date('Y-m-d', strtotime($month_start . ' + ' . ($days_in_month - 1) . ' days'));
            $totalSchoolDays = totalSchoolDay($month_start, $month_end, $conn);

            $totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM Students";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
            $totalStudents = mysqli_fetch_assoc($totalStudentsResult)['total_students'] * $totalSchoolDays;


            $query = "SELECT 
                        DATE(date) AS attendance_date,
                        COUNT(*) AS total_attendance,
                        COUNT(DISTINCT barcodeId) AS monthly_students
                    FROM attendance
                    WHERE DATE(date) BETWEEN '$month_start' AND '$month_end'
                    GROUP BY DATE(date)
                    HAVING total_attendance > 0
                    ORDER BY DATE(date)";

            $attendanceData = fetchMonthlyAttendanceData($month_start, $month_end, $query, $conn);

            if ($attendanceData) {
                $monthlyStats = array(
                    'labels' => array(), 
                    'data' => array(), 
                    'total_attendance' => 0,
                    'monthly_students' => 0,
                    'percentage_attendance' => 0,
                    'total_absentees' => 0
                );

                foreach ($attendanceData as $row) {
                    $monthlyStats['labels'][] = $row['attendance_date'];
                    $monthlyStats['data'][] = $row['total_attendance'];
                    $monthlyStats['total_attendance'] += $row['total_attendance'];
                    $monthlyStats['monthly_students'] += $row['monthly_students'];
                }
                
                // Calculate percentage attendance and absentees
                $monthlyStats['percentage_attendance'] = ($monthlyStats['total_attendance'] / $totalStudents) * 100;
                $monthlyStats['percentage_attendance_formatted'] = number_format($monthlyStats['percentage_attendance'], 2) . '%';
                $monthlyStats['total_absentees'] = $totalStudents - $monthlyStats['total_attendance'];
            } else {
                $monthlyStats = false;
            }

            return $monthlyStats;
        }

    
        function monthlyClassAttendance($month_start) {
            global $conn;

            $days_in_month = date('t', strtotime($month_start));
            $month_end = date('Y-m-d', strtotime($month_start . ' + ' . ($days_in_month - 1) . ' days'));
            $totalSchoolDays = totalSchoolDay($month_start, $month_end, $conn);

            $query = "SELECT 
                        Class.id AS Class_id,
                        Class.standard AS class_standard,
                        CONCAT(Class.standard, ' ', Class.name) AS class_name,
                        COUNT(attendance.barcodeId) AS total_attendance
                    FROM Students
                    LEFT JOIN Class ON Students.class = Class.id_name
                    LEFT JOIN attendance ON attendance.barcodeId = Students.username
                                        AND DATE(attendance.date) BETWEEN '$month_start' AND '$month_end'
                    GROUP BY Class.id, Class.standard, Class.name
                    ORDER BY Class.id";

            $attendanceData = fetchMonthlyAttendanceData($month_start, $month_end, $query, $conn);

            $totalStudentsQuery = "SELECT Class.id AS Class_id, Class.standard AS class_standard, COUNT(*) AS total_students FROM Students JOIN Class ON Students.class = Class.id_name GROUP BY Class.id, Class.standard";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);

            $monthlyClassAttendance = array();

            foreach ($attendanceData as $attendanceRow) {
                $class_id = $attendanceRow['Class_id'];

                $total_students = 0;
                while ($totalStudentsRow = mysqli_fetch_assoc($totalStudentsResult)) {
                    if ($totalStudentsRow['Class_id'] == $class_id && $totalStudentsRow['class_standard'] == $attendanceRow['class_standard']) {
                        $total_students = $totalStudentsRow['total_students'] * $totalSchoolDays;
                        mysqli_data_seek($totalStudentsResult, 0); // Reset pointer to the beginning
                        break;
                    }
                }

                $absentees = $total_students - $attendanceRow['total_attendance'];

                $monthlyClassAttendance[] = array(
                    'Class_id' => $class_id,
                    'class_standard' => $attendanceRow['class_standard'],
                    'class_name' => $attendanceRow['class_name'],
                    'total_attendance' => $attendanceRow['total_attendance'],
                    'total_students' => $total_students,
                    'absentees' => $absentees,
                    'attendance_percentage' => number_format(($attendanceRow['total_attendance'] / $total_students) * 100, 2),
                );
            }

            return $monthlyClassAttendance;
        }

        function monthlyStandardAttendance($month_start) {
            global $conn;
        
            $days_in_month = date('t', strtotime($month_start));
            $month_end = date('Y-m-d', strtotime($month_start . ' + ' . ($days_in_month - 1) . ' days'));
            $totalSchoolDays = totalSchoolDay($month_start, $month_end, $conn);
        
            $query = "SELECT 
                        Class.standard AS standard,
                        COUNT(attendance.barcodeId) AS total_attendance
                    FROM Class
                    LEFT JOIN Students ON Students.class = Class.id_name
                    LEFT JOIN attendance ON attendance.barcodeId = Students.username
                                        AND DATE(attendance.date) BETWEEN '$month_start' AND '$month_end'
                    GROUP BY Class.standard
                    ORDER BY Class.standard";
            $attendanceData = fetchMonthlyAttendanceData($month_start, $month_end, $query, $conn);
        
            $totalStudentsQuery = "SELECT Class.standard AS standard, COUNT(*) AS total_students FROM Students JOIN Class ON Students.class = Class.id_name GROUP BY Class.standard";
            $totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
        
            $monthlyStandardAttendance = array();
        
            foreach ($attendanceData as $attendanceRow) {
                $standard = $attendanceRow['standard'];
        
                $total_students = 0;
                while ($totalStudentsRow = mysqli_fetch_assoc($totalStudentsResult)) {
                    if ($totalStudentsRow['standard'] == $standard) {
                        $total_students = $totalStudentsRow['total_students'] * $totalSchoolDays;
                        break;
                    }
                }
        
                $absentees = max(0, $total_students - $attendanceRow['total_attendance']);
                $attendance_percentage = ($total_students > 0) ? number_format(($attendanceRow['total_attendance'] / $total_students) * 100, 2) : 0;
        
                $monthlyStandardAttendance[] = array(
                    'standard' => $standard,
                    'total_attendance' => $attendanceRow['total_attendance'],
                    'total_students' => $total_students,
                    'absentees' => $absentees,
                    'attendance_percentage' => $attendance_percentage,
                );
                
                // Reset the pointer to the beginning for reusing totalStudentsResult
                mysqli_data_seek($totalStudentsResult, 0);
            }
        
            return $monthlyStandardAttendance;
        }
    

        $selectedMonth = $_POST['month_picker'];
        $monthlyStats = monthlyStats($selectedMonth);
        $monthlyClassAttendance = monthlyClassAttendance($start_date);
        $monthlyStandardAttendance = monthlyStandardAttendance($start_date);
        // $monthlyStats = monthlyStats($start_date);

        
    } else {
        $statusMSG = "The chosen month is unavailable.";
    }
} else {
    $statusMSG = "The chosen month is unavailable.";
}

?>