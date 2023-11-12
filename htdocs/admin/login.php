<?php 

require_once __DIR__.'/../includes/load-lms.php';

// require_once __DIR__.'/../user/config.php';
// require_once __DIR__.'/../includes/functions-db.php';
// require_once __DIR__.'/../includes/functions-db.php';

if (!empty($_GET['do']) && $_GET['do'] === 'logout') {
  $_SESSION = array();
  header('Location: '.parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
  die();
}

if (!empty($_POST['do']) && $_POST['do'] == 'login') {
  $user = DB::queryFirstRow('SELECT * FROM Librarian WHERE username = %s', $_POST['un']??'');
  if (password_verify(($_POST['pw']??''),($user['password']??''))) {
    // log in successful
    
    $_SESSION = array();
    $_SESSION['LibrarianID'] = $user['librarianID'];
    $_SESSION['LibrarianName'] = $user['librarianName'];
    header('Location: ./');
    die();
  } else {
    // log in failed
    $error = 'Invalid credentials';
  }
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in</title>
    <link rel="stylesheet" href="../plugins/adminlte-3.2.0/dist/css/adminlte.min.css?v=3.2.0">
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="../../">Library Mgmt System</a>
      </div>
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Log in as <b>Librarian</b></p>

          <?php if(!empty($error)): ?>
            <p class="login-box-msg text-danger font-weight-bold"><?php _e($error); ?></p>
          <?php endif; ?>

          <form action="" method="post">
            <div class="input-group mb-3">
              <input type="name" class="form-control" placeholder="Username" name="un" autofocus>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Password" name="pw">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block" name="do" value="login">Log In</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../dist/js/adminlte.min.js?v=3.2.0"></script>
  </body>
</html>
