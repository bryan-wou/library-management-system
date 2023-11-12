<?php

require_once __DIR__.'/../includes/load-lms.php';

// lms_check_auth('Patron');

$lms_layout_title = 'Home';

?>
<?php lms_layout_header('public'); ?>

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
            <span><?php _e('Welcome'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <span><?php printf(__('Welcome to the %s Library Management System'), esc_html(lms_settings_get('library_name',''))); ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('public'); ?>
<?php lms_layout_footer('public'); ?>

