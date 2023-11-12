<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('search_patron_record');

$lms_layout_title = 'Add New Patron Record';

$patronCategory = lms_get_available_patron_categories();

$patronData = lms_get_patron_record($_GET['pid']??'');

if ($patronData) {
  $transactions = lms_get_transactions_from_patron_id($_GET['pid']);
}



?>
<?php lms_layout_header('admin'); ?>


<div class="container">
  <div class="content-header">
  </div>
  <?php if($patronData): ?>
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
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_name"><?php _e('Name'); ?></label>
                <input type="text" class="form-control patron-data-input" id="input_name" value="<?php echo addslashes(esc_html($patronData['patronName'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_barcode"><?php _e('Barcode'); ?></label>
                <input type="text" class="form-control patron-data-input" id="input_barcode" value="<?php echo addslashes(esc_html($patronData['_barcode'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_contact"><?php _e('Contact'); ?></label>
                <input type="text" class="form-control patron-data-input" id="input_contact" value="<?php echo addslashes(esc_html($patronData['patronContact'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_category"><?php _e('Category'); ?></label>
                <select id="input_category" class="form-control patron-data-input" disabled>
                  <?php foreach ($patronCategory as $pc) { ?>
                    <option value="<?php esc_html_e($pc['patronCategoryID']); ?>" <?php if($patronData['patronCategoryID']==$pc['patronCategoryID']){echo 'selected';} ?>>
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
                <input type="password" class="form-control patron-data-input" id="input_password" value="<?php echo (!!$patronData['password'])?'xxxxxxxx':''; ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label for="input_confirmpassword"><?php _e('Confirm Password'); ?></label>
                <input type="password" class="form-control patron-data-input" id="input_confirmpassword" value="<?php echo (!!$patronData['password'])?'xxxxxxxx':''; ?>" disabled>
              </div>
            </div>
            
            <!-- --------- -->
          </div>
          <hr>
          <div class="row" id="patron-data-mainbuttons">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="location.href='patron-add.php'">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Another Patron Record'); ?>
              </button>
              <button class="btn btn-outline-primary" onclick="enablePatronEdit();">
                <i class="fas fa-edit mx-1"></i>
                <?php _e('Edit Patron Record'); ?>
              </button>
              <button class="btn btn-outline-danger" onclick="deletePatron();">
                <i class="fas fa-trash mx-1"></i>
                <?php _e('Delete Patron Record'); ?>
              </button>
            </div>
          </div>
          <div class="row" id="patron-data-editbuttons" style="display:none;">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="submitPatronEdit();">
                <i class="fas fa-save mx-1"></i>
                <?php _e('Save'); ?>
              </button>
              <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync mx-1"></i>
                <?php _e('Reset'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Transactions'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-hover">
            <tr>
              <th><?php _e('Title'); ?></th>
              <th><?php _e('Check Out Date'); ?></th>
              <th><?php _e('Expected Check In Date'); ?></th>
              <th><?php _e('Actual Check In Date'); ?></th>
              <th><?php _e('Renew Count'); ?></th>
            </tr>
            <?php foreach ($transactions as $txn) { ?>
              <tr>
                <td><?php echo esc_html($txn['_biblio']['biblioTitle']); ?></td>
                <td><?php echo esc_html($txn['checkOutDate']); ?></td>
                <td><?php echo esc_html($txn['expectedCheckInDate']); ?></td>
                <td><?php echo esc_html($txn['actualCheckInDate']??'- not checked in -'); ?></td>
                <td><?php echo esc_html($txn['renewCount']); ?></td>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Patron Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="alert alert-danger mb-3" role="alert">
            <strong><?php _e('Error:'); ?></strong>
            <?php _e('Patron Record Not Found'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php lms_layout_scripts('admin'); ?>
<script>
  "use strict";
  
  function enablePatronEdit(){
    $('.patron-data-input').prop('disabled',false);
    $('#patron-data-mainbuttons').hide();
    $('#patron-data-editbuttons').show();
  }

  function submitPatronEdit(){
    let data = {};
    data.patronid     = '<?php echo addslashes($patronData['patronID']); ?>';
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
            title: '<?php echo addslashes(esc_html__('Successfully Edited Patron Record')); ?>',
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

