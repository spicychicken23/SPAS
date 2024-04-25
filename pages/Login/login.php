<html lang="en" class="type">
<!DOCTYPE html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class = "background">

  <?php

    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    define('TITLE', "Login");
    include '../../assets/includes/header.php';
    check_logged_out();
  ?>

    <section class="centered py-5 border-top-1">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 align-item-center login">
              <div class="darkText text-center p-4">

                <img src="..\..\assets\School Logo.png" alt="SMK SUNGAI PUSU">
                <h3 class="p-4">SMK Sungai Pusu</h3>

                <div class="text-center mb-3">
                    <small class="text-success font-weight-bold">
                        <?php
                            if (isset($_SESSION['STATUS']['loginstatus']))
                                echo $_SESSION['STATUS']['loginstatus'];

                        ?>
                    </small>
                </div>

                <form class="form-auth" action="includes/login.inc.php" method="post">
                  <?php insert_csrf_token(); ?>

                  
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" placeholder="Username" id="username" name="username" required>
                    <label for="username">Username</label>
                    <sub class="text-danger">
                        <?php
                            if (isset($_SESSION['ERRORS']['nouser']))
                                echo $_SESSION['ERRORS']['nouser'];
                        ?>
                    </sub>
                  </div>

                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <sub class="text-danger">
                        <?php
                            if (isset($_SESSION['ERRORS']['wrongpassword']))
                                echo $_SESSION['ERRORS']['wrongpassword'];
                        ?>
                    </sub>
                  </div>

                  <div class="custom-control custom-checkbox mr-sm-2">
                    <input type="checkbox" class="custom-control-input" id="rememberme" name="rememberme">
                    <label class="custom-control-label" for="rememberme">Remember me</label>
                  </div>

                  <button type="submit" class="btn btn-primary font-weight-bold mt-4" value="loginsubmit" name="loginsubmit">Login</button>

                  <!--
                  <div class="col-auto my-1 mb-4">
                    <p class="mt-3 text-muted text-center"><a href="../reset-password/">forgot password?</a></p>
                  </div>
                  -->

                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php include '../../assets/includes/footer.php' ?>

</body>
</html>