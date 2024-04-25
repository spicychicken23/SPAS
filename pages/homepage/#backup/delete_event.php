<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
//require 'database_connection.php';
require '../../../assets/setup/db.inc.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = file_get_contents("php://input");

    // Decode the JSON data
    $selectedEvents = json_decode($data);

    if (!empty($selectedEvents)) {
        // Create a comma-separated string of event IDs
        $eventIds = implode(',', $selectedEvents);

        // Delete events based on the selected event IDs
        $delete_query = "DELETE FROM calendar_event_master WHERE event_id IN ($eventIds)";
        if (mysqli_query($conn, $delete_query)) {
            $response = array(
                'status' => true,
                'msg' => 'Selected events deleted successfully!'
            );
        } else {
            $response = array(
                'status' => false,
                'msg' => 'Error deleting events.'
            );
        }
    } else {
        $response = array(
            'status' => false,
            'msg' => 'No events selected for deletion.'
        );
    }

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        'status' => false,
        'msg' => 'Invalid request.'
    );

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
