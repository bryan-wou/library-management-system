<?php

require_once __DIR__.'/../includes/load-lms.php';

$opac_patron_enabled = lms_settings_get('allow_opac')=='true' && lms_settings_get('allow_opac_patron_login')=='true';

if (!empty($_GET['do']) && $_GET['do'] === 'logout') {
  $_SESSION = array();
  header('Location: '.parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
  die();
}

if (lms_user_is_patron()) {
  header('Location: ./');
  die();
}

if (!empty($_POST['do']) && $_POST['do'] == 'login' && $opac_patron_enabled) {
  $user = DB::queryFirstRow('SELECT * FROM Patron p 
  INNER JOIN PatronBarcode pb ON pb.patronID = p.patronID
  WHERE pb.patronBarcode = %s', $_POST['un']??'');
  if (password_verify(($_POST['pw']??''),($user['password']??''))) {
    // log in successful
    $_SESSION = array();
    $_SESSION['PatronID'] = $user['patronID'];
    $_SESSION['PatronName'] = $user['patronName'];
    header('Location: ./');
    die();
  } else {
    // log in failed
    $error = 'Invalid credentials';
  }
}
if (!$opac_patron_enabled) {
  $error = 'OPAC Patron Login Disabled';
}
$lms_layout_title = 'Patron Login';

?>
<?php lms_layout_header('public'); ?>

<div class="container">
  <div class="content-header">
    <!-- <h1 class="text-dark">
      &nbsp;
    </h1> -->
  </div>
  
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span>Patron Login</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
              <p class="login-box-msg"><?php _e('Log in as <b>Patron</b>'); ?></p>
              <?php if(!empty($error)): ?>
                <p class="login-box-msg text-danger font-weight-bold"><?php _e($error); ?></p>
              <?php endif; ?>
              <form action="" method="post">
                <div class="input-group mb-3">
                  <input type="name" class="form-control" placeholder="<?php _e('Username'); ?>" name="un" autofocus>
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-user"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="<?php _e('Password'); ?>" name="pw">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block" name="do" value="login"><?php _e('Log In'); ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('public'); ?>
<?php lms_layout_footer('public'); ?>

