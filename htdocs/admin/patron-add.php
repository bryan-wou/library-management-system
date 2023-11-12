<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('add_patron_record');

$lms_layout_title = 'Add New Patron Record';

$patronCategory = lms_get_available_patron_categories();
?>
<?php lms_layout_header('admin'); ?>


<div class="container">
  <div class="content-header">
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Patron Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- --------- -->
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_name"><?php _e('Name'); ?></label>
                <input type="text" class="form-control" id="input_name">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_barcode"><?php _e('Barcode'); ?><a href="javascript:void(0)" class="ml-1 font-weight-normal" onclick="$('#input_barcode').val('<<New>>')"><?php _e('Autonumber'); ?></a></label>
                <input type="text" class="form-control" id="input_barcode">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_contact"><?php _e('Contact'); ?></label>
                <input type="text" class="form-control" id="input_contact">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_category"><?php _e('Category'); ?></label>
                <select id="input_category" class="form-control">
                  <?php foreach ($patronCategory as $pc) { ?>
                    <option value="<?php esc_html_e($pc['patronCategoryID']); ?>">
                      <?php esc_html_e($pc['patronCategoryName']); ?>
                      (<?php echo esc_html($pc['itemCheckOutDays']); ?>d/<?php echo esc_html($pc['itemCheckOutLimit']); ?>x/<?php echo esc_html($pc['itemRenewLimit']); ?>t)
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label for="input_password"><?php _e('Password'); ?></label>
                <input type="password" class="form-control" id="input_password">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label for="input_confirmpassword"><?php _e('Confirm Password'); ?></label>
                <input type="password" class="form-control" id="input_confirmpassword">
              </div>
            </div>
            
            <!-- --------- -->
          </div>
          <hr>
          <div class="row">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="submitPatron();">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add'); ?>
              </button>
              <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-undo-alt mr-1"></i>
                <?php _e('Reset'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('admin'); ?>
<script>
  "use strict";
  function submitPatron(){
    let data = {};
    data.name             = $('#input_name').val().trim(); // mandatory
    data.contact          = $('#input_contact').val().trim();
    data.barcode          = $('#input_barcode').val().trim(); // mandatory
    data.category         = $('#input_category>option:selected').val().trim();
    data.password         = $('#input_password').val();
    data.confirmpassword  = $('#input_confirmpassword').val();

    let errors = [];

    if (data.name.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Title.')); ?>');
    }
    if (data.barcode.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Barcode.')); ?>');
    }
    if (data.password != data.confirmpassword) {
      errors.push('<?php echo addslashes(esc_html__('Passwords do not match.')); ?>');
    }
    
    if (errors.length) {
      Swal.fire({
        title: 'Error',
        html: errors.join('<br>'),
        icon: 'error'
      });
      return false;
    }

    $.post('admin-ajax.php',{do:'addpatron',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Added Patron Record')); ?>',
            icon: 'success',
            willClose: function(){
              window.location.replace('patron-view.php?pid='+arr.data.patronid);
            }
          });
        } catch (error) {
          console.error(error);
          Swal.fire('Error1',error.toString(),'error');
        }
      })
      .fail(function(data){
        Swal.fire('Error2');
      });

  }
</script>
<?php lms_layout_footer('admin'); ?>

