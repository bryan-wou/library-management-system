<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('manage_librarians');

$librarians = lms_librarian_get_librarians();

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
            <span>Librarian List</span>
          </h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-hover">
            <tr>
              <th class="text-center"><?php _e('ID'); ?></th>
              <th><?php _e('Name'); ?></th>
              <th><?php _e('Username'); ?></th>
              <th class="text-center"><?php _e(''); ?></th>
            </tr>
            <?php foreach($librarians as $librarian): ?>
              <tr>
                <td class="text-center"><?php echo esc_html($librarian['librarianID']); ?></td>
                <td><?php echo esc_html($librarian['librarianName']); ?></td>
                <td><samp><?php echo esc_html($librarian['username']); ?></samp></td>
                <td class="text-center">
                  <a href="librarian-add.php?librarianid=<?php echo esc_html($librarian['librarianID']); ?>" class="btn btn-sm btn-outline-primary"><?php _e('View'); ?></a>
                  
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          
          <hr>
          <div class="row">
            <div class="col-12 text-center">
              <a class="btn btn-primary" href="librarian-add.php">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Librarian'); ?>
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

