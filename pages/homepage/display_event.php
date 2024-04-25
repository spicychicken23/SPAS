<?php                
//require 'database_connection.php'; 
require '../../assets/setup/db.inc.php';
$display_query = "SELECT event_id, event_name, event_start_date, event_end_date, event_description FROM calendar_event_master";
$results = mysqli_query($conn, $display_query);

$count = mysqli_num_rows($results);
if ($count > 0) {
    $data_arr = array();
    $i = 1;
    while ($data_row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
        $data_arr[$i]['event_id'] = $data_row['event_id'];
        $data_arr[$i]['title'] = $data_row['event_name'];
        $data_arr[$i]['start'] = date("Y-m-d", strtotime($data_row['event_start_date']));
        $data_arr[$i]['end'] = date("Y-m-d", strtotime($data_row['event_end_date']));
        $data_arr[$i]['description'] = $data_row['event_description']; // Include the description
        $data_arr[$i]['color'] = '#' . substr(uniqid(), -6);
        $data_arr[$i]['delete_url'] = 'delete_event.php?event_id=' . $data_row['event_id'];

        $i++;
    }

    $data = array(
        'status' => true,
        'msg' => 'successfully!',
        'data' => $data_arr
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Error!'
    );

}

echo json_encode($data);
?>