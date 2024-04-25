<html lang="en" class="type">
<!DOCTYPE html>

<head>
    <title>Individual Analysis & Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../js/individual.js"></script>
</head>

<body class="background">

    <?php
    include '../../assets/includes/header.php';
    check_logged_in();
    ?>

    <div class="container mt-5">

        <div>
            <h1 class="lightText">Individual Analysis & Report</h1>
        </div>

        <div class="row">
            <div class="col-auto">
                <form id="userTypeForm">
                    <div class="btn-group" role="group" aria-label="navi">
                        <button type="button" class="btn btn-light" name="Form1" id="Form1" value="Form1">Form 1</button>
                        <button type="button" class="btn btn-light" name="Form2" id="Form2" value="Form2">Form 2</button>
                        <button type="button" class="btn btn-light" name="Form3" id="Form3" value="Form3">Form 3</button>
                        <button type="button" class="btn btn-light" name="Form4" id="Form4" value="Form4">Form 4</button>
                        <button type="button" class="btn btn-light" name="Form5" id="Form5" value="Form5">Form 5</button>
                        <button type="button" class="btn btn-light" name="Teachers" id="Teachers" value="Teachers">Teachers</button>
                    </div>
                </form>
            </div>

            <div class="col-4 ms-auto">
                <div class="input-group">
                    <input type="search" name="search_query" id="search_query" class="form-control" style="width: 150px;">
                    <select class="form-select" id="query_selection">
                        <option name="query" id="query" value="name" selected disabled>Search By</option>
                        <option name="query" id="query" value="name">Name</option>
                        <option name="query" id="query" value="class">Class</option>
                        <option name="query" id="query" value="username">ID</option>
                    </select>
                    <button type="button" name="search" id="searchButton" class="btn btn-light">
                        <i class="bi bi-search manageIcons"></i>
                    </button>
                </div>
            </div>
            <hr>
        </div>
        
        <div class = "row mb-5">
            <div id="userListContainer" class = "bg-light rounded mb-5"></div>
        </div>

    </div>

    <?php include '../../assets/includes/footer.php'; ?>
</body>
</html>
