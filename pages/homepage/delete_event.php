<?php

// Debugging code
error_log('Received POST data: ' . file_get_contents('php://input'));

require '../../assets/setup/env.php';

$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = file_get_contents("php://input");

    // Decode the JSON data
    $postData = json_decode($data, true); // Decode as an associative array

    if (isset($postData['eventIds']) && is_array($postData['eventIds'])) {
        $eventIds = $postData['eventIds'];

        if (!empty($eventIds)) {
            $eventIds = array_map('intval', $eventIds); // Convert each element to an integer

            $eventIdsString = implode(',', $eventIds);

            $delete_query = "DELETE FROM calendar_event_master WHERE event_id IN ($eventIdsString)";
            if (mysqli_query($conn, $delete_query)) {
                $response = array(
                    'status' => true,
                    'msg' => 'Selected events deleted successfully!'
                );
            } else {
                $response = array(
                    'status' => false,
                    'msg' => 'Error deleting events: ' . mysqli_error($conn)
                );
            }
        } else {
            $response = array(
                'status' => false,
                'msg' => 'No events selected for deletion.'
            );
        }
    } else {
        $response = array(
            'status' => false,
            'msg' => 'Invalid request.'
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        'status' => false,
        'msg' => 'Invalid request.'
    );

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
