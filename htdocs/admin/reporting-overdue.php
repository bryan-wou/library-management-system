<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');

$lms_layout_title = 'Overdue Books';

$overdueBooks = lms_circ_get_overdue_books();


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
            <span>List of Overdue Books</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row justify-content-center ">
            <div class="col-auto">
              <table class="table table-bordered table-responsive">
                <tr>
                  <th><?php _e('Txn ID'); ?></th>
                  <th><?php _e('Patron Barcode'); ?></th>
                  <th><?php _e('Patron Name'); ?></th>
                  <th><?php _e('Patron Contact'); ?></th>
                  <th><?php _e('Biblio Title'); ?></th>
                  <th><?php _e('Item Barcode'); ?></th>
                  <th><?php _e('Check-out Date'); ?></th>
                  <th><?php _e('Expected Check-in Date'); ?></th>
                  <th><?php _e('Overdue Days'); ?></th>
                </tr>
                <?php foreach($overdueBooks as $book): ?>
                  <tr>
                    <td><?php echo esc_html($book['transactionID']); ?></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($book['patronID']); ?>"><?php echo esc_html($book['_patronBarcode']); ?></a></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($book['patronID']); ?>"><?php echo esc_html($book['patronName']); ?></a></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($book['patronID']); ?>"><?php echo esc_html($book['patronContact']); ?></a></td>
                    <td><a href="biblio-view.php?bid=<?php echo esc_html($book['biblioID']); ?>"><?php echo esc_html($book['biblioTitle']); ?></a></td>
                    <td><a href="biblio-view.php?bid=<?php echo esc_html($book['biblioID']); ?>"><?php echo esc_html($book['_biblioItemBarcode']); ?></a></td>
                    <td><?php echo esc_html($book['checkOutDate']); ?></td>
                    <td><?php echo esc_html($book['expectedCheckInDate']); ?></td>
                    <td class="text-right"><?php echo esc_html(date_diff(date_create(date('Y-m-d H:i:s')),date_create($book['expectedCheckInDate']))->format('%a')); ?></td>
                  </tr>
                <?php endforeach; ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('admin'); ?>
<?php lms_layout_footer('admin'); ?>

