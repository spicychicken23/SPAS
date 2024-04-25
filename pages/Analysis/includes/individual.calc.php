<?php

function countAttendance($conn, $username) {
    $barcodeId = $username;

    $totalDaysQuery = "SELECT DISTINCT DATE(date) as totalDate FROM attendance WHERE DATE(date) IS NOT NULL";
    $totalDaysResult = mysqli_query($conn, $totalDaysQuery);
    $totalDates = [];

    while ($row = mysqli_fetch_assoc($totalDaysResult)) {
        $totalDates[] = $row['totalDate'];
    }

    $attendedDaysQuery = "SELECT DISTINCT DATE(date) as attendedDate FROM attendance WHERE barcodeId = '$barcodeId' AND DATE(date) IS NOT NULL";
    $attendedDaysResult = mysqli_query($conn, $attendedDaysQuery);
    $attendedDates = [];

    while ($row = mysqli_fetch_assoc($attendedDaysResult)) {
        $attendedDates[] = $row['attendedDate'];
    }

    $absentDates = array_diff($totalDates, $attendedDates);

    $attended = count($attendedDates);
    $absented = count($absentDates);
    $totalEntries = $attended + $absented;
    $percentage = ($totalEntries > 0) ? ($attended / $totalEntries) * 100 : 0;

    $averageTimeQuery = "SELECT TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(date))), '%H:%i:%s') as averageTime FROM attendance WHERE barcodeId = '$barcodeId' AND DATE(date) IS NOT NULL";
    $averageTimeResult = mysqli_query($conn, $averageTimeQuery);
    $averageTimeRow = mysqli_fetch_assoc($averageTimeResult);
    $averageTime = $averageTimeRow['averageTime'];

    return array('attended' => $attended, 'absented' => $absented, 'percentage' => $percentage, 'averageTime' => $averageTime, 'absentDates' => $absentDates);
}

function checkAttendance($conn, $username, $table) {
    $barcodeId = $username;

    $checkQuery = "SELECT COUNT(*) as count FROM $table 
                   WHERE barcodeId = '$barcodeId' 
                   AND DATE(date) = CURDATE()";
    $result = mysqli_query($conn, $checkQuery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];

        if ($count > 0) {
            return "Attended";
        } else {
            return "Absent";
        }
    } else {
        return "Error: " . mysqli_error($conn);
    }
}


function countAttendanceTC($conn, $username) {
    $barcodeId = $username;

    $totalDaysQuery = "SELECT DISTINCT DATE(date) as totalDate FROM attendancetc WHERE DATE(date) IS NOT NULL";
    $totalDaysResult = mysqli_query($conn, $totalDaysQuery);
    $totalDates = [];

    while ($row = mysqli_fetch_assoc($totalDaysResult)) {
        $totalDates[] = $row['totalDate'];
    }

    $attendedDaysQuery = "SELECT DISTINCT DATE(date) as attendedDate FROM attendancetc WHERE barcodeId = '$barcodeId' AND DATE(date) IS NOT NULL";
    $attendedDaysResult = mysqli_query($conn, $attendedDaysQuery);
    $attendedDates = [];

    while ($row = mysqli_fetch_assoc($attendedDaysResult)) {
        $attendedDates[] = $row['attendedDate'];
    }

    $absentDates = array_diff($totalDates, $attendedDates);

    $attended = count($attendedDates);
    $absented = count($absentDates);
    $totalEntries = $attended + $absented;
    $percentage = ($totalEntries > 0) ? ($attended / $totalEntries) * 100 : 0;

    $averageTimeQuery = "SELECT TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(date))), '%H:%i:%s') as averageTime FROM attendancetc WHERE barcodeId = '$barcodeId' AND DATE(date) IS NOT NULL";
    $averageTimeResult = mysqli_query($conn, $averageTimeQuery);
    $averageTimeRow = mysqli_fetch_assoc($averageTimeResult);
    $averageTime = $averageTimeRow['averageTime'];

    return array('attended' => $attended, 'absented' => $absented, 'percentage' => $percentage, 'averageTime' => $averageTime, 'absentDates' => $absentDates);
}

?>
