<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Taking</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding-top: 20px;
            margin-top: 2rem;
        }

        .container2 {
            color: #F9F6F0;
            background-color: #F9F6F0; /* Cream-colored background */
            padding: 20px;
            margin: 20px;
            
        }

        .input-area textarea {
            width: 80%;
            height: 100px;
            margin-bottom: 20px;
        }

        .info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .info-item img {
            width: 100px;
            height: 100px;
            margin-right: 20px;
        }

        .details {
            display: flex;
            flex-direction: column;
            text-align: left;
        }


    </style>
    </head>

    <body class="background">
    <?php
    include '../../assets/includes/header.php';
    check_logged_in();
    date_default_timezone_set('Asia/Kuala_Lumpur');

    ?>

    <div class="container">
    <h1 class="text-dark">Record Attendance</h1>
    </div>
    <hr>
    <div class="container2 rounded-3">
        <h3 class="text-danger">You scan start scanning barcodes to record the attendance or manually type the barcode on the text field.</h3>
    </div>
    <div class="container">
    <br><br>
    <form action="" method="post" class="input-group-mb-2" id="barcodeId">
        <input type="text" class="form-control" name="barcodeId" placeholder="Enter MOE ID" aria-label="username" aria-describedby="button-addon2" maxlength="12" autofocus>
        <button class="btn btn-light my-3" type="submit" id="button-addon2">Submit</button>
    </form>

    <?php
    // Provided code snippet
    $selectFields = '';
    $tableEcho = '';
    $tableAtt = '';
    $whereCondition = '';
    $currentDate = date("Y-m-d H:i:s");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $barcodeId = isset($_POST["barcodeId"]) ? $_POST["barcodeId"] : '';
    
        // Validation: Check length and format (m-XXXXXXX or g-XXXXXXXX)
        $isValidFormat = preg_match('/^(m|g)-\d{7,}$/', $barcodeId);
    
        if (!$isValidFormat) {
            echo "Invalid barcode format. Please use the format 'm-XXXXXXXX' or 'g-XXXXXXXX'.";
        } else {
            $sanitizedBarcodeID = mysqli_real_escape_string($conn, $barcodeId);
    
            if (strtolower(substr($sanitizedBarcodeID, 0, 1)) === 'm') {
                $tableEcho = 'students';
                $selectFields = 'name, class';
                $whereCondition = "username = '$sanitizedBarcodeID'";
                $tableAtt = 'attendance';
            } elseif (strtolower(substr($sanitizedBarcodeID, 0, 1)) === 'g') {
                $tableEcho = 'teachers';
                $selectFields = 'name';
                $tableAtt = 'attendancetc';
                $emailPrefix = explode('-', $sanitizedBarcodeID)[1];
                $whereCondition = "email LIKE '%$emailPrefix%@%'";
            } else {
                echo "Invalid barcode format.";
                exit;
            }
    
            // Check if student data exists
            $query = "SELECT $selectFields FROM $tableEcho WHERE $whereCondition";
            $result = mysqli_query($conn, $query);
    
            if ($result === false) {
                echo "Error executing the query: " . mysqli_error($conn);
            } elseif (mysqli_num_rows($result) > 0) {
                // Student data found, proceed with attendance recording
    
                // Check if attendance is already recorded for the sanitizedBarcodeID on the current date
                $checkQuery = "SELECT * FROM $tableAtt WHERE barcodeId = '$sanitizedBarcodeID' 
                                AND DATE(date) = CURDATE()";
                $checkResult = mysqli_query($conn, $checkQuery);
    
                if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                    echo "Attendance is already recorded for today.";
                } else {
                    // Insert into the appropriate table based on the barcode prefix
                    $currentDate;
                    $insertQuery = "INSERT INTO $tableAtt (barcodeId, date) VALUES ('$sanitizedBarcodeID', '$currentDate')";
                    $insertResult = mysqli_query($conn, $insertQuery);
    
                    if ($insertResult === false) {
                        echo "Student does not exist in database";
                    } else {
                        echo "Attendance is recorded!";
                    }
                }
    
                // Display student information
                $row = mysqli_fetch_assoc($result);
    
                if ($tableEcho === 'students') {
                    $name = $row['name'];
                    $class = $row['class'];
                    ?>
                    <div class="info">
                        <div class="lightText flex-grow-1 ms-3">
                            <span>Name: <?php echo $name; ?></span><br>
                            <span>Class: <?php echo $class; ?></span><br>
                            <span>Date: <?php echo $currentDate; ?></span>
                        </div>
                    </div>
                    <?php
                } elseif ($tableEcho === 'teachers') {
                    $name = $row['name'];
                    ?>
                    <div class="info">
                        <div class="lightText flex-grow-1 ms-3">
                            <span>Name: <?php echo $name; ?></span><br>
                            <span>Date: <?php echo $currentDate; ?></span>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "No record found with this barcode ID.";
            }
        }
    }
    
    ?>
</div>

    <script src="/SPAS/js/readBarcode.js"></script>
    <?php include '../../assets/includes/footer.php'; ?>

</body>
</html>