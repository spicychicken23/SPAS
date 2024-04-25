<?php
// Update the path to db.inc.php using an absolute path
require $_SERVER['DOCUMENT_ROOT'] . '/SPAS/assets/setup/db.inc.php';

$event_name = $_POST['event_name'];
$event_description = $_POST['event_description'];
$event_start_date = date("Y-m-d", strtotime($_POST['event_start_date']));
$event_end_date = date("Y-m-d", strtotime($_POST['event_end_date']));

$insert_query = "INSERT INTO `calendar_event_master`(`event_name`, `event_description`, `event_start_date`, `event_end_date`) VALUES ('$event_name','$event_description','$event_start_date','$event_end_date')";

if(mysqli_query($conn, $insert_query)) {
    $data = array(
        'status' => true,
        'msg' => 'Event added successfully!',
        'event_description' => $event_description
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Sorry, Event not added.'
    );
}

echo json_encode($data);
?>
