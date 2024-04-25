<html lang="en" class="type">
<!DOCTYPE html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="background">

  <?php
  include '../../assets/includes/header.php';
  require '../../assets/setup/db.inc.php';

  $role = $_POST['table_name'];
  $id = $_POST['id'];

  $sql = "select * from $role where id = $id";
  $qry = mysqli_query($conn, $sql);
  while ($result = mysqli_fetch_array($qry)) {

  ?>
  <div class="container mt-5">
    <div>
      <h1 class="lightText">Manage Accounts</h1>
    </div>

    <hr class="hr" />

    <section class="centered py-5 border-top-1">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-5 col-md-8 align-item-center">
            <form action="includes/accEdit.inc.php" method="post">
              <div class="border bg-light rounded-3 text-center p-4">

                <input type="hidden" id="table_name" name="table_name" value="<?php echo $role; ?>">
                <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">

                <div class="mb-3 row">
                  <label for="name" class="col-sm-2 col-form-label">Name:</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" value='<?php echo $result['name']; ?>' required>
                  </div>
                </div>

                <?php if ($role !== 'Admin' && $role !== 'Class') { ?>
                  <div class="mb-3 row">
                    <label for="email" class="col-sm-2 col-form-label">Email:</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="email" name="email" value='<?php echo $result['email']; ?>' required>
                    </div>
                  </div>

                  <?php if ($role === 'Students') { ?>
                    <div class="mb-3 row">
                      <label for="class" class="col-sm-2 col-form-label">Class:</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="class" name="class" value='<?php echo $result['class']; ?>' required>
                      </div>
                    </div>
                  <?php } ?>
                <?php } elseif ($role === 'Class') { ?>
                  <div class="mb-3 row">
                    <label for="standard" class="col-sm-2 col-form-label">Standard:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="standard" name="standard" value='<?php echo $result['standard']; ?>' required>
                    </div>
                  </div>
                <?php } else { ?>
                  <div class="mb-3 row">
                    <label for="username" class="col-sm-2 col-form-label">Username:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="username" name="username" value='<?php echo $result['username']; ?>' required>
                    </div>
                  </div>
                <?php } ?>

                <div class="row mt-3">
                  <div class="col-sm-6">
                    <a href="accManage.php" class="btn btn-secondary">Back</a>
                  </div>
                  <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php } ?>

  <script>
    const usernameInput = document.getElementById("username");
    usernameInput.addEventListener("input", function() {
      const usernameValue = usernameInput.value;
      if (!usernameValue.startsWith("Admin")) {
        usernameInput.value = "Admin" + usernameValue;
      }
    });
  </script>

</body>

</html>