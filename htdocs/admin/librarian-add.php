<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('manage_librarians');

$librarianPrivilegeTypes = lms_librarian_get_librarian_privilege_types();
if (!empty($_GET['librarianid'])) {
  $librarian = lms_librarian_get_librarian($_GET['librarianid']);
} else {
  $librarian = [];
}

$lms_layout_title = 'Home';

?>
<?php lms_layout_header('admin'); ?>

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
            <span>Add New Librarian</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- --------- -->
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label for="input_name"><?php _e('Librarian Name'); ?></label>
                <input type="text" class="form-control libn-data-input" id="input_name" value="<?php echo ($librarian)?addslashes(esc_html($librarian['librarianName'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label for="input_username"><?php _e('Username'); ?></label>
                <input type="text" class="form-control libn-data-input" id="input_username" value="<?php echo ($librarian)?addslashes(esc_html($librarian['username'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6 show-if-editing" <?php echo ($librarian)?'style="display:none;"':''; ?>>
              <div class="form-group">
                <label for="input_password">
                  <?php _e('Password'); ?>
                  <small class="font-weight-normal"><?php echo ($librarian)?__('(leave blank if not changing)'):''; ?></small>
                </label>
                <input type="password" class="form-control libn-data-input" id="input_password">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-6 show-if-editing" <?php echo ($librarian)?'style="display:none;"':''; ?>>
              <div class="form-group">
                <label for="input_confirmpassword"><?php _e('Confirm Password'); ?></label>
                <input type="password" class="form-control libn-data-input" id="input_confirmpassword">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12">
              <label><?php _e('Privileges'); ?></label>
              <div class="row">
                <?php foreach($librarianPrivilegeTypes as $privtype): ?>
                  <div class="col-12 col-md-6 col-lg-4">
                    <div class="form-check">
                      <input 
                        class="form-check-input libn-data-input" 
                        type="checkbox" 
                        name="privilegetype" 
                        value="<?php echo +$privtype['librarianPrivilegeTypeID']; ?>" 
                        id="lpt_checkbox_<?php echo +$privtype['librarianPrivilegeTypeID']; ?>"
                        <?php if(in_array($privtype['librarianPrivilegeTypeID'], $librarian['_privileges']??[])){echo 'checked';} ?>
                        <?php echo ($librarian)?'disabled':''; ?>
                      >
                      <label class="form-check-label" for="lpt_checkbox_<?php echo +$privtype['librarianPrivilegeTypeID']; ?>">
                        <?php _e($privtype['librarianPrivilegeTypeName']); ?>
                      </label>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12 text-center">

              <?php if(!$librarian): ?>
              <button class="btn btn-primary bi-add-buttons" onclick="submitLibrarian();">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Librarian'); ?>
              </button>
              <?php endif; ?>

              
              <?php if($librarian && $librarian['librarianID'] == $_SESSION['LibrarianID']): ?>
              <small class="text-secondary"><?php _e("To prevent inadvertant self-lockout, you can't edit your own profile."); ?></small>
              <?php endif; ?>

              <?php if($librarian && $librarian['librarianID'] != $_SESSION['LibrarianID']): ?>    
                <div id="libn-viewbuttons">
                  <button class="btn btn-outline-primary" onclick="enableLibrarianEdit();">
                    <i class="fas fa-edit mx-1"></i>
                    <?php _e('Edit Librarian'); ?>
                  </button>
                  <button class="btn btn-outline-danger" onclick="confirmDeleteLibrarian(<?php echo $librarian['librarianID']; ?>);">
                    <i class="fas fa-trash mx-1"></i>
                    <?php _e('Delete Librarian'); ?>
                  </button>
                </div>            
                <div id="libn-editbuttons" style="display:none;">
                  <button class="btn btn-primary" onclick="submitLibrarian(<?php echo $librarian['librarianID']; ?>);" id="bi-edit-button-submit">
                    <i class="fas fa-save mx-1"></i>
                    <?php _e('Save'); ?>
                  </button>
                  <button class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="fas fa-sync mx-1"></i>
                    <?php _e('Reset'); ?>
                  </button>
                </div>
              <?php endif; ?>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php lms_layout_scripts('admin'); ?>
<script>
  'use strict';

  function enableLibrarianEdit(){
    $('.libn-data-input').prop('disabled',false);
    $('#libn-viewbuttons').hide();
    $('#libn-editbuttons').show();
    $('.show-if-editing').show();
  }

  function submitLibrarian(librarianid = false){
    let data = {};
    data.csrf_token = '<?php echo lms_get_csrf_token(); ?>';
    if (librarianid) { data.librarianid = librarianid; }

    data.name             = $('#input_name').val().trim(); // mandatory
    data.username         = $('#input_username').val().trim(); // mandatory
    data.password         = $('#input_password').val();
    data.confirmpassword  = $('#input_confirmpassword').val();
    data.privilegetypeids = [];

    $('input[name="privilegetype"]:checked').each(function(){data.privilegetypeids.push($(this).val())});

    let errors = [];

    if (data.name.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter Librarian Name.')); ?>');
    }
    if (data.username.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Username.')); ?>');
    }
    if (data.password.length < 1 && !librarianid) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Password.')); ?>');
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
    // console.log(data);return;
    $.post('admin-ajax.php',{do:'addlibrarian',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Added/Edited Librarian')); ?>',
            icon: 'success',
            willClose: function(){
              window.location.replace('librarian-view.php');
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

  function confirmDeleteLibrarian(librarianid){
    Swal.fire({
      title: '<?php _e('Confirm delete?'); ?>',
      showCancelButton: true,
      confirmButtonText: '<?php _e('Delete'); ?>',
      cancelButtonText: `<?php _e('Cancel'); ?>`,
      icon: 'question',
    }).then((result) => {
      if (result.isConfirmed) {
        doDeleteLibrarian(librarianid);
      }
    })
  }
  function doDeleteLibrarian(librarianid){
    let data = {librarianid:librarianid};
    console.log(data);
    $.post('admin-ajax.php',{
      do:'deletelibrarian',
      data: data,
      csrf_token:'<?php echo lms_get_csrf_token(); ?>'
    })
    .done(function(data){
      try {
        const arr = JSON.parse(data);
        if (!arr.success) { throw Error(data); }
        Swal.fire({
          title: '<?php echo addslashes(esc_html__('Deletion Successful')); ?>',
          icon: 'success',
          willClose: function(){
            window.location.replace('librarian-view.php');
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

