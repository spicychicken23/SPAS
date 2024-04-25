<html lang="en" class="type">
<!DOCTYPE html>

<head>
  <title>Manage Accounts</title>
  <script src="../../js/accManage.js"></script>
</head>

<body class="background">

  <?php
  include '../../assets/includes/header.php';
  include 'accRegister.php';
  check_logged_in();
  ?>

  <div class="container mt-5">
    <div>
      <h1 class="lightText">Manage Accounts</h1>
    </div>
    

    <div class="row">
      <div class="col-8">
        <form id="userTypeForm">
          <div class="btn-group" role="group" aria-label="navi">
            <button type="button" class="btn btn-light" name="name" id="StudentBtn" value="Students">Students</button>
            <button type="button" class="btn btn-light" name="name" id="TeacherBtn" value="Teachers">Teachers</button>
            <button type="button" class="btn btn-light" name="name" id="AdminBtn" value="Admin">Admins</button>
            <button type="button" class="btn btn-light" name="name" id="ClassBtn" value="Class">Classes</button>
          </div>
        </form>
      </div>

      <div class="col">
        <div class="input-group">
          <input type="search" name="search_query" id="search_query" class="form-control" style="width: 125px;">
          <select class="form-select" id="query_selection">
            <option disabled selected>Search By</option>
            <option name="query" id="query" value="name">Name</option>
            <option name="query" id="query" value="username">Username</option>
            <option name="query" id="query" value="email">Email</option>
            <option name="query" id="query" value="class">Class</option>
            <option name="query" id="query" value="standard">Standard</option>
          </select>
          <button type="button" name="search" id="searchButton" class="btn btn-light">
            <i class="bi bi-search manageIcons"></i>
          </button>
        </div>
      </div>

      <div class="col-10 col-sm-8">
        <form method="POST" action="includes/accManage.imp.php" enctype="multipart/form-data" class="d-flex align-items-center">
          <div class="input-group">
            <input type="file" class="form-control" id="inputCsv" name="inputCsv" aria-describedby="input" aria-label="Upload">
            <button class="btn btn-light" type="submit" name="inputRole" id="inputRole">
              <i class="bi bi-upload"></i>
            </button>
          </div>
        </form>
      </div>

      <div class="col d-flex justify-content-end">
          <form method="POST">
            <button type="button" class="btn btn-light rounded mx-1" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-placement="bottom" title="Add User Manually">
              <i class="bi bi-plus-square manageIcons"></i>
            </button>
            <input type="submit" name="registerButton" style="display: none;">
          </form>

          <form method="POST" action="includes/accManage.del.php">
            <button class="btn btn-light rounded mx-1" name="deleteValue" id="deleteValue" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add all users" onclick="return confirm('Are you sure you want to delete the whole record?')">
              <i class="bi bi-trash manageIcons"></i>
            </button>
          </form>

          <form id="userListForm" method="POST" action="includes/accManage.print.php" style="display: none;">
            <input type="hidden" name="userListContent" id="userListContent">
          </form>

          <form>
            <button class="btn btn-light rounded mx-1" id="printBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Save User List" onclick="saveUserList()">
              <i class="bi bi-floppy manageIcons"></i>
            </button>
          </form>
      </div>

      <hr class="hr" />
    </div>


    <div class="row mb-5">
      <div id="userListContainer" class = "bg-light rounded mb-5">
      </div>
    </div>

  </div>

  <?php include '../../assets/includes/footer.php' ?>

</body>

</html>