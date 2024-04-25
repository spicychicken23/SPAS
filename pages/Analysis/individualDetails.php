<html lang="en" class="type">
<!DOCTYPE html>

<head>
    <title>Detailed Individual Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body class="background">

    <?php
    include '../../assets/includes/header.php';
    check_logged_in();

    require '../../assets/setup/db.inc.php';
    require 'includes/individual.calc.php';

    $table = $_POST['table_name'];
    $name = $_POST['name'];
    $id = $_POST['id'];
    
    if ($table == 'students') {
        $sql = "SELECT * FROM attendance WHERE barcodeId = '$id'";
        $username = $_POST['id'];
        $qry = mysqli_query($conn, $sql);
        $attendanceData = countAttendance($conn, $id);
    } else {
        
        $sql = "SELECT * FROM attendanceTC WHERE barcodeId = '$id'";
        $qry = mysqli_query($conn, $sql);
        $attendanceData = countAttendanceTC($conn, $id);
    }    
    ?>

    <div class="container my-5 pb-5">

        <div>
            <h1 class="lightText">Detailed Individual Report</h1>
        </div>

        <div class="row">
          <div class="col">
            <form id="userTypeForm">
              <div class="btn-group" role="group" aria-label="navi">
                <button type="button" class="btn btn-light">
                  <a href="individual.php" class="btn btn-light">
                    <i class="bi bi-arrow-bar-left manageIcons"></i>
                  </a>
                </button>
                <button type="button" class="btn btn-light" name="name" id="Attended" value="Attended">Attended</button>
                <button type="button" class="btn btn-light" name="name" id="Absented" value="Absented">Absent</button>
              </div>
            </form>
          </div>
          
          <div class="col-2">
            <div class="input-group">
              <select class="form-select" id="query_selection">
                  <option value="all" selected>All</option>
                  <option value="January">January</option>
                  <option value="February">February</option>
                  <option value="March">March</option>
                  <option value="April">April</option>
                  <option value="May">May</option>
                  <option value="June">June</option>
                  <option value="July">July</option>
                  <option value="August">August</option>
                  <option value="September">September</option>
                  <option value="October">October</option>
                  <option value="November">November</option>
                  <option value="December">December</option>
              </select>
          </div>
          </div>

          <hr class="hr" />
        </div>

        

        <div class="row mb-3">
            <div class="col-5 bg-light rounded-3 p-2 m-2"><?php echo "$name"; ?></div>
            <div class="col bg-light rounded-3 p-2 m-2"> Attended: <?php echo $attendanceData['attended']; ?></div>
            <div class="col bg-light rounded-3 p-2 m-2"> Absent: <?php echo $attendanceData['absented']; ?></div>
            <div class="col bg-light rounded-3 p-2 m-2"> Rate: <?php echo intval($attendanceData['percentage']); ?>%</div>
            <div class="col bg-light rounded-3 p-2 m-2">Avg. Time: <?php echo $attendanceData['averageTime']; ?></div>
        </div>
        
        <div class="row rounded bg-light p-2 my-3 hidden" id="attendedDatesTable">
            <h2>Attended Dates</h2>
            <table class="table table-light table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1;
                    while ($row = mysqli_fetch_assoc($qry)) {
                        $dateTime = strtotime($row['date']);
                        $formattedDate = date('d/m/Y', $dateTime);
                        $formattedDay = date('l', $dateTime);
                        $formattedTime = date('H:i:s', $dateTime);

                        echo "<tr data-original-date=\"" . $row['date'] . "\">";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td>" . $formattedDate . "</td>";
                        echo "<td>" . $formattedDay . "</td>";
                        echo "<td>" . $formattedTime . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="row rounded bg-light p-2 my-3 hidden" id="absentDatesTable">
          <h2>Absent Dates</h2>
          <table class="table table-light table-hover" style="width:100%">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Date</th>
                      <th>Day</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  $index = 1;
                  foreach ($attendanceData['absentDates'] as $absentDate) {
                      $dateTime = strtotime($absentDate);
                      $formattedDate = date('d/m/Y', $dateTime);
                      $formattedDay = date('l', $dateTime);

                      echo "<tr data-original-date=\"" . $absentDate . "\">";
                      echo "<td>" . $index++ . "</td>";
                      echo "<td>" . $formattedDate . "</td>";
                      echo "<td>" . $formattedDay . "</td>";
                      echo "</tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>


    </div>

    <script src="/SPAS/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('Attended').addEventListener('click', function() {
                showTable('attendedDatesTable', 'absentDatesTable');
            });

            document.getElementById('Absented').addEventListener('click', function() {
                showTable('absentDatesTable', 'attendedDatesTable');
            });

            document.getElementById('query_selection').addEventListener('change', function() {
                filterByMonth(this.value);
            });

            function showTable(showTableId, hideTableId) {
                document.getElementById(showTableId).classList.remove('hidden');
                document.getElementById(hideTableId).classList.add('hidden');
            }

            function filterByMonth(selectedMonth) {
                var attendedTable = document.getElementById('attendedDatesTable');
                var absentTable = document.getElementById('absentDatesTable');

                var attendedRows = attendedTable.querySelectorAll('tbody tr');
                var absentRows = absentTable.querySelectorAll('tbody tr');

                attendedRows.forEach(function(row) {
                    row.classList.remove('hidden');
                });

                absentRows.forEach(function(row) {
                    row.classList.remove('hidden');
                });

                if (selectedMonth !== 'all') {
                    attendedRows.forEach(function(row) {
                        if (!isMonthMatch(row, selectedMonth)) {
                            row.classList.add('hidden');
                        }
                    });

                    absentRows.forEach(function(row) {
                        if (!isMonthMatch(row, selectedMonth)) {
                            row.classList.add('hidden');
                        }
                    });
                }
            }

            function isMonthMatch(row, selectedMonth) {
                var originalDate = row.getAttribute('data-original-date');
                var date = new Date(originalDate);
                var rowMonth = date.toLocaleString('en-US', { month: 'long' });

                return rowMonth.toLowerCase() === selectedMonth.toLowerCase();
            }
        });
    </script>

    <?php include '../../assets/includes/footer.php'; ?>
</body>

</html>
