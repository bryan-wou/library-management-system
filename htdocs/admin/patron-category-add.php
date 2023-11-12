<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('manage_patron_categories');

if (!empty($_GET['categoryid'])) {
  $category = lms_get_patron_category($_GET['categoryid']);
} else {
  $category = [];
}

$lms_layout_title = 'Add New Patron Category';

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
            <span>Add New Patron Category</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- --------- -->
            <div class="col-12">
              <div class="form-group">
                <label for="input_name"><?php _e('Category Name'); ?></label>
                <input type="text" class="form-control libn-data-input" id="input_name" value="<?php echo ($category)?addslashes(esc_html($category['patronCategoryName'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_itemCheckOutDays"><?php _e('Item Check-out Days'); ?></label>
                <input type="number" min="0" max="9999" step="1" class="form-control libn-data-input" id="input_itemCheckOutDays" value="<?php echo ($category)?addslashes(esc_html($category['itemCheckOutDays'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_itemCheckOutLimit"><?php _e('Item Check-out Limit'); ?></label>
                <input type="number" min="0" max="9999" step="1" class="form-control libn-data-input" id="input_itemCheckOutLimit" value="<?php echo ($category)?addslashes(esc_html($category['itemCheckOutLimit'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_itemRenewLimit"><?php _e('Item Renewal Limit'); ?></label>
                <input type="number" min="0" max="9999" step="1" class="form-control libn-data-input" id="input_itemRenewLimit" value="<?php echo ($category)?addslashes(esc_html($category['itemRenewLimit'])).'" disabled "':''; ?>">
              </div>
            </div>
            <!-- --------- -->
          </div>
          <hr>
          <div class="row">
            <div class="col-12 text-center">

              <?php if(!$category): ?>
              <button class="btn btn-primary bi-add-buttons" onclick="submitLibrarian();">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Patron Category'); ?>
              </button>
              <?php endif; ?>

              

              <?php if($category): ?>    
                <div id="libn-viewbuttons">
                  <button class="btn btn-outline-primary" onclick="enableLibrarianEdit();">
                    <i class="fas fa-edit mx-1"></i>
                    <?php _e('Edit Patron Category'); ?>
                  </button>
                </div>            
                <div id="libn-editbuttons" style="display:none;">
                  <button class="btn btn-primary" onclick="submitLibrarian(<?php echo $category['patronCategoryID']; ?>);" id="bi-edit-button-submit">
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

  function submitLibrarian(categoryid = false){
    let data = {};
    data.csrf_token = '<?php echo lms_get_csrf_token(); ?>';
    if (categoryid) { data.patroncategoryid = categoryid; }

    data.patroncategoryname = $('#input_name').val().trim(); // mandatory
    data.itemcheckoutdays   = $('#input_itemCheckOutDays').val();
    data.itemcheckoutlimit  = $('#input_itemCheckOutLimit').val();
    data.itemrenewlimit     = $('#input_itemRenewLimit').val();

    let errors = [];

    if (data.patroncategoryname.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter Category Name.')); ?>');
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
    $.post('admin-ajax.php',{do:'addpatroncategory',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Added/Edited Patron Category')); ?>',
            icon: 'success',
            willClose: function(){
              window.location.replace('patron-category-view.php');
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

