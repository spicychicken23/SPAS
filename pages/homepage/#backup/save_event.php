<?php
//require 'database_connection.php'; 
require '../../../assets/setup/db.inc.php';

$event_name = $_POST['event_name'];
$event_description = $_POST['event_description']; // Correctly retrieve event description
$event_start_date = date("y-m-d", strtotime($_POST['event_start_date'])); 
$event_end_date = date("y-m-d", strtotime($_POST['event_end_date'])); 

//log recieved data
error_log("Event Name: " . $event_name);
error_log("Event Description: " . $event_description);
error_log("Event Start Date: " . $event_start_date);
error_log("Event End Date: " . $event_end_date);

$insert_query = "INSERT INTO `calendar_event_master`(`event_name`, `event_description`, `event_start_date`, `event_end_date`) VALUES ('".$event_name."','".$event_description."','".$event_start_date."','".$event_end_date."')";             

if(mysqli_query($conn, $insert_query)) {
    
    $data = array(
        'status' => true,
        'msg' => 'Event added successfully!',
        'event_description' => $event_description  // Add this line
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Sorry, Event not added.'				
    );
}

echo json_encode($data);
?>
