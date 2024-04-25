<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generator</title>
    <script src="../../js/generateBarcode.js"></script>
    <style>
        .box {
            text-align: center;
            color: white;
            padding-top: 50px;
            font-size: 24px;
        }

        .flex-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .flex-container label,
        .flex-container input,
        .flex-container button {
            margin-right: 10px;
            font-size: large;
        }

        .barcode-container {
            display: inline-block;
            margin: 20px;
            width: 250x;
            background-color: white;
            text-align: center;
            justify-content: center;
            padding: 15px;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
    </style>
    </head>

<body class = "background">
    <?php
    include '../../assets/includes/header.php';
    check_logged_in();
    ?>

    <div class="container mt-5">
        <div>
            <h1 class="lightText">Generate Barcodes</h1>
        </div>

        <div class="row justify-content-between">
            <div class="col-8">
                <form id="userTypeForm">
                    <div class="btn-group" role="group" aria-label="navi">
                        <button type="button" class="btn btn-light" name="name" id="Bulk" value="Bulk">Bulk</button>
                        <button type="button" class="btn btn-light" name="name" id="Manual" value="Manual">Manual</button>
                    </div>
                </form>
            </div>
            <div class="col-auto text-end" id="downloadSection">
                <?php 
                $zipFilename = 'all_barcodes.zip'; // Zip file name
                if (file_exists($zipFilename)) { ?>
                    <a href="<?php echo $zipFilename ?>" download="all_barcodes.zip" class="btn btn-light"><i class="bi bi-download"></i> Download All</a>
                <?php } else {
                    echo "Failed to create the zip file."; 
                } ?>
            </div>
        </div>
        <hr class="hr" />
    </div>

    <div class="container mt-5">
        <div class="row" id="bulkSection">
        <h3>For Bulk Generation: Best advised is to create by batch to ensure an efficient system traffic.
            Maximum 100 barcodes to be generated at once. Only CSV file is accepted.</h3>
            <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="input-group col  mb-5">
                    <input type="file" name="csvFile" id="csvFile" class="form-control">
                    <button class="btn btn-light" type="submit" name="upload">Generate</button>
                </div>
            </form>
        </div>

        <div class="row" id="manualSection" style="display: none;">
        <h3>For Manual Generation: This section is meant to be used for barcodes that are generated one by one </h3>
            <form action="" method="post">
                <div class="form-floating mb-3 d-flex">
                    <input type="text" class="form-control" placeholder="Unique ID" name="individualData" id="individualData" required>
                    <label for="username">Enter MOE ID</label>
                    <button class="btn btn-light" type="submit" name="generate">Generate</button>
                </div>
            </form>
        </div>
    </div>


    <?php
    $barcodes = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $saveDirectory = '../../barcodes';
        }
        
        function extractValueFromEmail($email) {
            $parts = explode('@', $email);
            if (count($parts) === 2) {
                return $parts[0];
            }
            return '';
        }

        // Check if a file has been selected for upload
        if (isset($_POST["upload"]) && empty($_FILES["csvFile"]["tmp_name"])) {
            echo '<div class="popup" id="popup">Please upload a CSV file</div>';
        } else {
            // Generate barcodes from uploaded CSV file
            if (isset($_POST["upload"]) && isset($_FILES["csvFile"]["tmp_name"])) {
                $csvFile = $_FILES["csvFile"]["tmp_name"];
                $headerSkipped = false;

                if (($handle = fopen($csvFile, 'r')) !== FALSE) {
                    $rowCounter = 0;

                    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        
                        /*
                        if (!$headerSkipped) {
                            $headerSkipped = true;
                            continue;
                        }
                        */

                        $email = $data[0];
                        $barcodeData = extractValueFromEmail($email);
            
                        $barcodes[] = $barcodeData;

                        /*
                        $barcodes[] = $data[0]; // Email(username) is in the first column of the CSV
                        */


                    }
                    fclose($handle);
                    
                    $rowCounter++;
                    if ($rowCounter >= 100) {
                        echo '<script>alert("The input is more than the recommended 100 data.");</script>';
                        exit();
                    }

                }
            }
        }

            // Generate barcode from individual data
            if (isset($_POST["generate"]) && isset($_POST["individualData"])) {
                $individualData = $_POST["individualData"];
                $barcodes[] = $individualData;
            }

            // API endpoint URL for generating Code 128 barcode using the specified API
            $apiBaseUrl = "http://bwipjs-api.metafloor.com/?bcid=code128&scale=2&rotate=N&includetext";

            // Generate barcodes and save them to the specified directory
            foreach ($barcodes as $data) {

                $apiUrl = $apiBaseUrl . "&text=" . urlencode($data);
                $filename = $saveDirectory . DIRECTORY_SEPARATOR . $data . ".png"; // Barcodes is saved in this path and filetype by default

                // Save the barcode as a file
                file_put_contents($filename, file_get_contents($apiUrl));

                // Display the barcode
            ?>
        <div class="barcode-container">
            <img class="barcode-image" src='<?php echo $apiUrl ?>' >
            <br>
            <a href="<?php echo $filename ?>" download>Download Barcode</a> <!-- Download link -->
            <br>
            <br>
        </div>
    <?php
            
    }


?>
<?php

    if (!empty($barcodes)) {
        $zipFilename = 'all_barcodes.zip'; // Zip file name
        $zip = new ZipArchive();
        if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
            foreach ($barcodes as $data) {
                $extractedValue = ($data);
                $filename = $saveDirectory . DIRECTORY_SEPARATOR . $extractedValue . ".png";

                if (file_exists($filename)) {
                    $zip->addFile($filename, $extractedValue . ".png");
                } else {
                    echo "File not found: $filename"; // Display an error message or handle the missing file scenario
                }
            }

            $zip->close();

            } else {
                echo "Failed to create the zip file."; 
        }
    
    }   

?>

<script>
    function validateForm() {
        var fileInput = document.getElementById('csvFile');
        var allowedExtensions = /(\.csv)$/i;

        if (fileInput.files.length === 0 || !allowedExtensions.test(fileInput.value)) {
            alert('Only CSV files are accepted.');
            return false;
        }

        return true;
    }
</script>


<?php
include '../../assets/includes/footer.php';
?>

</body>
</html>