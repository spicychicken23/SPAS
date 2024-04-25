
<html>
<head>
    <title>SPAS Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <!-- *Note: You must have internet connection on your laptop or pc other wise below code is not working -->
    <!-- CSS for full calender -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- JS for full calender -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <!-- bootstrap css and js -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</head>
<body class="background">
    <?php 
        include "../../assets/includes/header.php"; 
        include "../Analysis/includes/individual.calc.php";

        check_logged_in();
    ?>

    <div class = "container-fluid my-2">
            <div class="row mb-5 p-5">
                <div class="col-3 col-sm-4 mx-auto">
                    
                    <div class="row vh-20 bg1 shadow mb-4">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" style="height: 20vh;">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            </div>
                            <div class="carousel-inner rounded-3">
                                <div class="carousel-item active">
                                    <img src="\SPAS\assets\images\Homepage1.jpg" class="d-block w-100" alt="SMK Sungai Pusu Image 1" style="object-fit: cover; height: 100%;">
                                </div>
                                <div class="carousel-item">
                                    <img src="\SPAS\assets\images\Homepage2.jpg" class="d-block w-100" alt="SMK Sungai Pusu Image 2" style="object-fit: cover; height: 100%;">
                                </div>
                                <div class="carousel-item">
                                    <img src="\SPAS\assets\images\Homepage3.jpg" class="d-block w-100" alt="SMK Sungai Pusu Image 3" style="object-fit: cover; height: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row-auto vh-20 bg2 shadow mb-4">            
                            <h2>Hello there, </h2>
                            <span class = "greet"><?php echo $_SESSION['first_name']; ?></span>

                            
                            <div class="px-3">
                                <div class="row">
                                    <?php if ($_SESSION['role'] !== 'Student') { ?>
                                    <div class="col mt-3">
                                        <a href="/SPAS/pages/attendance/readBarcode.php" class="btn btn-dark btn-block rounded-pill shadow" style="position: relative;">
                                            <i class="bi bi-pencil-square bi-lg icon"></i>
                                            <span class="text">Record</span>
                                        </a>
                                    </div>
                                    <div class="col mt-3">
                                        <a href="/SPAS/pages/attendance/teacher attendance.php" class="btn btn-dark btn-block rounded-pill shadow" style="position: relative;">
                                            <i class="bi bi-search bi-lg icon"></i>
                                            <span class="text">View</span>
                                        </a>
                                    </div>
                                    <?php } ?>
                                    <?php if ($_SESSION['role'] !== 'Admin') { ?>
                                    <div class="col mt-3">
                                        <a href="/SPAS/pages/attendance/myAttendance.php" class="btn btn-dark btn-block rounded-pill shadow" style="position: relative;">
                                            <i class="bi bi-journal-check bi-lg icon"></i>
                                            <span class="text">My Attendance</span>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>                    
                    </div>
 
                    <?php
                        function fetchData($date, $query) {
                            global $conn;

                            $result = mysqli_query($conn, $query);

                            if ($result) {
                                return  mysqli_fetch_assoc($result);
                            } else {
                                return false;
                            }
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
                            
                                if ($attendanceData['total_attendance'] == 0) {
                                    $dailyStats['total_absentees'] = 0;
                                } else {
                                    $dailyStats['total_absentees'] = $totalStudents - $attendanceData['daily_students'];
                                }
                            }
                             else {
                                $dailyStats = false;
                            }

                            return $dailyStats;
                        }

                        $currentDate = date("Y-m-d");
                        $dailyStats = dailyStats($currentDate); 
                        if ($_SESSION['role'] == 'Student' ) {
                            $individualStats = countAttendance($conn,$_SESSION['username']);
                        } else if ($_SESSION['role'] == 'Teacher'){
                            $individualStats = countAttendanceTC($conn,$_SESSION['MOE']);
                        }
                        
                    ?>

                    <?php if ($_SESSION['role'] == 'Admin') { ?>
                    <div class="row bg3 mb-4">
                        <?php if ($dailyStats) { ?>
                            <div class = "col stats">
                                <h0><?php echo $dailyStats['total_attendance'];?></h0>
                                <span class="text">ATTENDEES FOR TODAY</span>
                            </div>
                            <div class = "col-auto iconStats">
                                <i class="bi bi-building-check"></i>
                            </div>
                        <?php } else {
                            echo '<p>No attendance data available for the current date.</p>';
                        } ?>
                    </div>
                    <div class="row bg3 mb-4">
                        <?php if ($dailyStats) { ?>
                            <div class = "col stats">
                                <h0><?php echo $dailyStats['total_absentees'];?></h0>
                                <span class="text">ABSENTEES FOR TODAY</span>
                            </div>
                            <div class = "col-auto iconStats">
                                <i class="bi bi-building-dash"></i>
                            </div>
                        <?php } else {
                            echo '<p>No attendance data available for the current date.</p>';
                        } ?>
                    </div>
                    <?php } ?>

                    <?php if ($_SESSION['role'] !== 'Admin') { ?>
                    <div class="row bg3 mb-4">
                        <?php if ($dailyStats) { ?>
                            <div class = "col stats">
                                <h0><?php echo $individualStats['attended'];?></h0>
                                <span class="text">ATTENDED</span>
                            </div>
                            <div class = "col-auto iconStats">
                                <i class="bi bi-person-check"></i>
                            </div>
                        <?php } else {
                            echo '<p>No attendance data available for the current date.</p>';
                        } ?>
                    </div>
                    <div class="row bg3 mb-4">
                        <?php if ($dailyStats) { ?>
                            <div class = "col stats">
                                <h0><?php echo $individualStats['absented'];?></h0>
                                <span class="text">ABSENT</span>
                            </div>
                            <div class = "col-auto iconStats">
                                <i class="bi bi-person-dash"></i>
                            </div>
                        <?php } else {
                            echo '<p>No attendance data available for the current date.</p>';
                        } ?>
                    </div>
                    <?php } ?>
                </div>

                <div class="col-8 col-sm-7 mx-auto small">
                    <div class="shadow rounded-3 p-2">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            
            

            <?php if ($_SESSION['role'] == 'Admin') { ?>
<!-- Start popup dialog box -->
        <div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Add New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <div class="row">
                                <div class="col-sm-12">  
                                    <div class="form-group">
                                    <label for="event_name">Event name</label>
                                    <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Enter your event name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">  
                                    <div class="form-group">
                                        <label for="event_description">Event description</label>
                                        <textarea name="event_description" id="event_description" class="form-control" placeholder="Enter your event description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">  
                                    <div class="form-group">
                                    <label for="event_start_date">Event start</label>
                                    <input type="date" name="event_start_date" id="event_start_date" class="form-control onlydatepicker" placeholder="Event start date">
                                    </div>
                                </div>
                                <div class="col-sm-6">  
                                    <div class="form-group">
                                    <label for="event_end_date">Event end</label>
                                    <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Event end date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End popup dialog box -->
<?php } ?>

        <!-- Modal for displaying event description -->
        <div class="modal fade" id="eventDescriptionModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Event Description</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="eventDescriptionText"></p>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

<?php if ($_SESSION['role'] == 'Admin') { ?>
        <!-- Add this HTML for the event list modal somewhere in your document -->
        <div class="modal fade" id="eventListModal" tabindex="-1" role="dialog" aria-labelledby="eventListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventListModalLabel">Select Events to Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Event list will be displayed here -->
                        <ul id="eventList" class="list-group">
                            <!-- List items will be dynamically added here -->
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="deleteSelectedEvents()">Delete Events</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>



    <?php 
        include "C:/xampp/htdocs/SPAS/assets/includes/footer.php"; 
    ?>
</body>

<script>
$(document).ready(function() {
	display_events();

    <?php
        // Add a JavaScript variable to store the user's role
        echo "var userRole = '" . $_SESSION['role'] . "';";
        ?>

	$(document).on('click', '#eventList li', function () {
        $(this).toggleClass('active');
    });

    if (userRole === 'Admin') {
            initCalendarForAdmin();
        } else {
            initCalendarForStudentTeacher();
        }
	
}); //end document.ready block

    function display_events() {
        var events = [];

        // Your existing AJAX call to fetch events
        $.ajax({
            url: 'display_event.php',
            dataType: 'json',
            success: function (response) {
                var result = response.data;
                $.each(result, function (i, item) {
                    events.push({
                        event_id: result[i].event_id,
                        title: result[i].title,
                        start: result[i].start,
                        end: result[i].end,
                        color: result[i].color,
                        description: result[i].description,
                        url: result[i].url,
                    });
                });

                var calendarConfig = {
                    defaultView: 'month',
                    timeZone: 'local',
                    editable: true,
                    selectable: true,
                    selectHelper: true,
                    select: function (start, end) {
                    $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
                    $('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
                    $('#event_entry_modal').modal('show');
                    },
                    events: events,
                    eventRender: function (event, element, view) {
                        element.bind('click', function () {
                            $('#eventDescriptionText').text(event.description);
                            $('#eventDescriptionModal').modal('show');
                        });
                    },
                    header: {
                        right: 'prev,next todayButton deleteEventButton',
                    },
                    customButtons: {
                        todayButton: {
                            text: 'today',
                            click: function () {
                                $('#calendar').fullCalendar('today');
                            },
                            icon: 'bi bi-calendar-check-fill',
                        },
                        deleteEventButton: {
                            text: 'Delete Event',
                            click: function () {
                                showEventListModal();
                            },
                            icon: 'bi bi-trash',
                        },
                    },
                };

                // Customize calendar based on user's role
                var userRole = '<?php echo $_SESSION['role']; ?>';
                if (userRole !== 'Admin') {
                    calendarConfig.header.right = 'prev,next todayButton';
                    delete calendarConfig.customButtons.deleteEventButton;
                }

                // Initialize the calendar
                var calendar = $('#calendar').fullCalendar(calendarConfig);
            },
            error: function (xhr, status, error) {
                console.log('Ajax error:', status);
                console.log('Ajax error details:', error);
                console.log('Response:', xhr.responseText);
                alert('Error occurred while fetching events.');
            },
        });
    }


function save_event() {
    var event_name = $("#event_name").val();
    var event_description = $("#event_description").val();
    var event_start_date = $("#event_start_date").val();
    var event_end_date = $("#event_end_date").val();

    // Check for empty fields
    if (event_name == "" || event_description == "" || event_start_date == "" || event_end_date == "") {
        alert("Please enter all required details.");
        return false;
    }

    $.ajax({
        url: "save_event.php",
        type: "POST",
        dataType: 'json',
        data: {
            event_name: event_name,
            event_description: event_description,
            event_start_date: event_start_date,
            event_end_date: event_end_date
        },
		success: function (response) {
			console.log("AJAX Response:", response);  // Log the entire response
		
			$('#event_entry_modal').modal('hide');
			if (response.status == true) {
				alert(response.msg);
				location.reload();
			} else {
				alert(response.msg);
			}
		},
        error: function (xhr, status) {
            console.log('ajax error = ' + xhr.statusText);
            alert('Error occurred while saving the event.');
        }
    });

    return false;
}

function showEventListModal() {
    var calendar = $('#calendar').fullCalendar('getCalendar'); // Get the calendar instance
    var events = calendar.clientEvents(); // Use calendar.clientEvents()

    var eventList = $('#eventList');

    // Clear existing list
    eventList.empty();

    // Populate the event list
    $.each(events, function (index, event) {
        var listItem = $('<li class="list-group-item">');
        listItem.text(event.title);
        listItem.attr('data-event-id', event.event_id); // Use event.event_id instead of event.id
        eventList.append(listItem);
    });

    // Show the modal
    $('#eventListModal').modal('show');
}



function deleteSelectedEvents() {
    var eventListItems = $('#eventList li.active');
    var eventIds = [];

    eventListItems.each(function () {
        var eventId = $(this).attr('data-event-id');
        eventIds.push(eventId);
    });

    console.log('Event IDs:', eventIds);

    if (eventIds.length > 0) {
        $.ajax({
            url: 'delete_event.php',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ eventIds: eventIds }), // Wrap eventIds in an object
            success: function (response) {
                console.log('AJAX Response:', response);

                if (response.status === true) {
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.msg);
                }
            },
            error: function (xhr, status, error) {
                console.log('AJAX error:', status);
                console.log('AJAX error details:', error);
                console.log('Response:', xhr.responseText);
                alert('Error occurred while deleting events.');
            }
        });
    } else {
        alert('No events selected for deletion.');
    }
}



function getCurrentDate() {
            var today = new Date();
            var year = today.getFullYear();
            var month = ('0' + (today.getMonth() + 1)).slice(-2);
            var day = ('0' + today.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        }

        document.getElementById('dateInput').value = getCurrentDate();
        document.getElementById('myForm').submit();

</script>
</html> 