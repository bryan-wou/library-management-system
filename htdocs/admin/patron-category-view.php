<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('manage_patron_categories');

$categories = lms_get_available_patron_categories();

$lms_layout_title = 'View Patron Categories';

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
            <span>Librarian List</span>
          </h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-hover">
            <tr>
              <th class="text-center"><?php _e('ID'); ?></th>
              <th><?php _e('Category Name'); ?></th>
              <th><?php _e('Item Check-out Days'); ?></th>
              <th><?php _e('Item Check-out Limit'); ?></th>
              <th><?php _e('Item Renewal Limit'); ?></th>
              <th class="text-center"><?php _e(''); ?></th>
            </tr>
            <?php foreach($categories as $category): ?>
              <tr>
                <td class="text-center"><?php echo esc_html($category['patronCategoryID']); ?></td>
                <td><?php echo esc_html($category['patronCategoryName']); ?></td>
                <td><samp><?php echo esc_html($category['itemCheckOutDays']); ?></samp></td>
                <td><samp><?php echo esc_html($category['itemCheckOutLimit']); ?></samp></td>
                <td><samp><?php echo esc_html($category['itemRenewLimit']); ?></samp></td>
                <td class="text-center">
                  <a href="patron-category-add.php?categoryid=<?php echo esc_html($category['patronCategoryID']); ?>" class="btn btn-sm btn-outline-primary"><?php _e('View'); ?></a>
                  
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          
          <hr>
          <div class="row">
            <div class="col-12 text-center">
              <a class="btn btn-primary" href="patron-category-add.php">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Patron Category'); ?>
              </a>
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
  

</script>
<?php lms_layout_footer('admin'); ?>

