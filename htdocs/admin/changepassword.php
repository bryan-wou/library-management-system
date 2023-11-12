<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');

if (!empty($_POST['do']) && $_POST['do'] == 'changepassword') {
  lms_check_csrf_token($_POST['csrf_token']);
  $user = DB::queryFirstRow('SELECT * FROM Librarian l WHERE l.librarianID = %i', $_SESSION['LibrarianID']);
  if (password_verify(($_POST['cpw']??''),($user['password']??''))) {
    // log in successful
    if ($_POST['npw'] == $_POST['cnpw']) {
      DB::update('Librarian',array('password'=>password_hash($_POST['npw'],PASSWORD_DEFAULT)),'librarianID=%i',$_SESSION['LibrarianID']);
      $success = 'Password changed successfully.';
    } else {
      // new password not same
      $error = 'New passwords do not match.';
    }
  } else {
    // log in failed
    $error = 'Current password invalid.';
  }
}

$lms_layout_title = 'Change Password';

?>
<?php lms_layout_header('admin'); ?>

<div class="container">
  <div class="content-header">
    <!-- <h1 class="text-dark">
      &nbsp;
    </h1> -->
  </div>
  
  <?php if(isset($_GET['ad'])): ?>
    <div class="alert alert-danger mb-3" role="alert">
      <strong><?php _e('Error:'); ?></strong>
      <?php _e('Insufficient Privileges'); ?>
    </div>
  <?php endif; ?>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Change Password'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
              <?php if(!empty($success)): ?>
                <p class="login-box-msg text-success font-weight-bold"><?php _e($success); ?></p>
              <?php endif; ?>
              <?php if(!empty($error)): ?>
                <p class="login-box-msg text-danger font-weight-bold"><?php _e($error); ?></p>
              <?php endif; ?>
              <form action="" method="post">
                <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="<?php _e('Current Password'); ?>" name="cpw">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="<?php _e('New Password'); ?>" name="npw">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="<?php _e('Confirm New Password'); ?>" name="cnpw">
                  <div class="input-group-append">
                    <div class="input-group-text">
                      <span class="fas fa-lock"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" class="form-control" name="csrf_token" value="<?php echo lms_get_csrf_token(); ?>">
                    <button type="submit" class="btn btn-primary btn-block" name="do" value="changepassword"><?php _e('Change Password'); ?></button>
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

<?php lms_layout_scripts('admin'); ?>
<?php lms_layout_footer('admin'); ?>

