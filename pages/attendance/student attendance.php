<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/fa133b6a54.js" crossorigin="anonymous"></script>
    <title>View Attendance</title>
    <style>
        .custom {
            background-color: #f8f9fa;
            border: 0.25rem solid #ccc1a7;
            border-top-left-radius: 0.3rem;
            border-bottom-left-radius: 0.3rem;
        }
    </style>

</head>

<body class = "background">
    <?php 
        include "../../assets/includes/header.php"; 

        check_logged_in();
    ?>
    <div class="container my-5">

        <div>
        <h1 class="lightText">View Attendance</h1>
        </div>

        <div class="row">
            <div class="col">
                <form id="userTypeForm">
                    <div class="btn-group" role="group" aria-label="navi">
                        <button type="button" class="custom" name="name" id="StudentBtn" value="Students">Students</button>
                        <button type="button" class="btn btn-light" name="name" id="TeacherBtn" value="Teachers">Teachers</button>
                    </div>
                </form>
            </div>

            <div class="col">
                <!-- Search Form -->
                <form class="h2-style" action="" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by Student ID" name="search" id="search">
                        <button class="btn btn-light" type="submit" name="searchSubmit"><i class="fas fa-search"></i></button>
                        <button class="btn btn-light rounded mx-2" type="submit" name="clearsearch">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Replace these with your actual database credentials
        $servername = "localhost";
        $username = "admin";
        $password = "admin";
        $dbname = "spas_db";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Function to display data
        function displayData($conn, $orderBy, $order) {
            // Update the SQL query to select timestamp and separate date and time
            $sql = "SELECT barcodeid, DATE(date) AS date, TIME(date) AS time FROM attendance ORDER BY $orderBy $order";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table class='table table-light table-hover bg-light rounded' style='width:100%'>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['barcodeid']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['time']}</td>
                            <td><a href='javascript:void(0);' onclick='confirmDelete(\"{$row['barcodeid']}\", \"{$row['date']}\", \"{$row['time']}\")' class='btn btn-light btn-sm'><i class='fas fa-trash'></i></a></td>
                          </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p class='lead'>No data found.</p>";
            }
        }

 // Function to delete data
function deleteData($conn, $barcodeId, $date, $time) {
    $sql = "DELETE FROM attendance WHERE barcodeid='$barcodeId' AND DATE(date)='$date' AND TIME(date)='$time'";
    if ($conn->query($sql) === TRUE) {
        return "Record successfully deleted. Refresh the page to see the updated data.";
    } else {
        return "Error deleting record: " . $conn->error;
    }
}

        // Function to clear all data
        function clearAllData($conn) {
            $sql = "TRUNCATE TABLE attendance";
            $conn->query($sql);
        }

        // Display sort form
        echo '<form class="my-4" action="" method="get">
                    <div class="row">
                        <div class="col-md-6">
                                <select class="form-select" id="sortBy" name="orderby">
                                    <option selected disabled> Sort By </option>
                                    <option value="barcodeid" ' . (isset($_GET['orderby']) && $_GET['orderby'] == 'barcodeid' ? 'selected' : '') . '>Student ID</option>
                                    <option value="date" ' . (isset($_GET['orderby']) && $_GET['orderby'] == 'date' ? 'selected' : '') . '>Date</option>
                                    <option value="time" ' . (isset($_GET['orderby']) && $_GET['orderby'] == 'time' ? 'selected' : '') . '>Time</option>
                                </select>
                        </div>
                        <div class="col-md-5">
                            <select class="form-select" id="sortOrder" name="order">
                                <option selected disabled> Sort Order </option>
                                <option value="asc" ' . (isset($_GET['order']) && $_GET['order'] == 'asc' ? 'selected' : '') . '>Ascending</option>
                                <option value="desc" ' . (isset($_GET['order']) && $_GET['order'] == 'desc' ? 'selected' : '') . '>Descending</option>
                            </select>
                        </div>

                        <div class="col text-end">
                            <button class="btn btn-light" type="submit">Sort</button>
                        </div>
                    </div>
                </form>

                <hr class="hr" />';

        // Display data
        $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'barcodeid';
        $order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

// Display data based on search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Perform search based on the input
    $searchInput = $_GET['search'];
    $searchSql = "SELECT barcodeid, DATE(date) AS date, TIME(date) AS time FROM attendance WHERE barcodeid = '$searchInput'";
    $searchResult = $conn->query($searchSql);

    // Display search result table
    if ($searchResult->num_rows > 0) {
        echo "<h3>Search Results</h3>";
        echo "<table class='table table-light table-hover bg-light rounded' style='width:100%'>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($row = $searchResult->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['barcodeid']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['time']}</td>
                    <td><a href='javascript:void(0);' onclick='confirmDelete(\"{$row['barcodeid']}\", \"{$row['date']}\", \"{$row['time']}\")' class='btn btn-light btn-sm'><i class='fas fa-trash'></i></a></td>
                  </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p class='lead'>No matching data found.</p>";
    }
} elseif (isset($_GET['clearsearch'])) {
    // Display data table when 'Clear Search' is clicked
    displayData($conn, $orderBy, $order);
} else {
    // Display data table
    displayData($conn, $orderBy, $order);
}

if ($_SESSION['role'] == 'Admin') {
    echo    "<p><a href='?action=clear' onclick='return confirm(\"Are you sure you want to clear all data from database?\")'"; 
    echo    "class='btn btn-danger'>Clear All Data</a></p>";
}

// Handle clear all data
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    clearAllData($conn);
}

// Handle delete action for the search result table
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $studentIdToDelete = $_GET['id'];
    $dateToDelete = $_GET['date'];
    $timeToDelete = $_GET['time'];
    $deleteMessage = deleteData($conn, $studentIdToDelete, $dateToDelete, $timeToDelete);
    if (strpos($deleteMessage, 'successfully') !== false) {
        echo "<div class='alert alert-success'>$deleteMessage</div>";
    } else {
        echo "<div class='alert alert-danger'>$deleteMessage</div>";
    }
    // echo "Refresh the page to see the updated data.";

    // header("Location: ".$_SERVER['PHP_SELF'].'?search='.$_GET['search']);
}



$conn->close();

        ?>
    </div>

    <script>
    function confirmDelete(barcodeId, date, time) {
        var confirmation = confirm("Are you sure you want to delete this data from database?");
        if (confirmation) {
            window.location.href = `?action=delete&id=${barcodeId}&date=${date}&time=${time}&search=${encodeURIComponent(document.getElementById('search').value)}`;
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the user type form
        var userTypeForm = document.getElementById("userTypeForm");

        // Add click event listeners to the buttons
        document.getElementById("StudentBtn").addEventListener("click", function() {
            // Redirect to the student page (code 1)
            window.location.href = " student attendance.php";
        });

        document.getElementById("TeacherBtn").addEventListener("click", function() {
            // Redirect to the teacher page (code 2)
            window.location.href = "teacher attendance.php";
        });
    });
</script>

<?php include '../../assets/includes/footer.php' ?>
</body>

</html>
