<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');

$lms_layout_title = 'Most Check-outs Patron';

$mostCheckoutsPatron = lms_circ_get_most_checkouts_patron(@$_GET['start']??false, @$_GET['end']??false);


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
            <span>List of Patron with the Most Check-outs</span>
          </h3>
        </div>
        <div class="card-header">
          <div class="form-inline">
            <label class="mr-1" for="dateInput"><?php _e('Date Range:'); ?></label>
            <input class="form-control text-center" id="dateInput">
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center ">
            <div class="col-auto">
              <table class="table table-bordered table-responsive">
                <tr>
                  <th><?php _e('Patron Barcode'); ?></th>
                  <th><?php _e('Patron Name'); ?></th>
                  <th><?php _e('Patron Contact'); ?></th>
                  <th><?php _e('Patron Category'); ?></th>
                  <th><?php _e('No. of Check-outs'); ?></th>
                </tr>
                <?php foreach($mostCheckoutsPatron as $patron): ?>
                  <tr>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($patron['patronID']); ?>"><?php echo esc_html($patron['_patronBarcode']); ?></a></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($patron['patronID']); ?>"><?php echo esc_html($patron['patronName']); ?></a></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($patron['patronID']); ?>"><?php echo esc_html($patron['patronContact']); ?></a></td>
                    <td><a href="patron-view.php?pid=<?php echo esc_html($patron['patronID']); ?>"><?php echo esc_html($patron['patronCategoryName']); ?></a></td>
                    <td class="text-right"><?php echo esc_html($patron['countTxn']); ?></td>
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
<script>
  
  $('#dateInput').daterangepicker({
    "locale": {
      "format": "YYYY-MM-DD",
      "separator": " - ",
      "firstDay": 0,
    },
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      // 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      'This Year': [moment().startOf('year'), moment().endOf('year')],
    },
    "alwaysShowCalendars": true,
    "showDropdowns": true,
    
    <?php if(@$_GET['start'] && $_GET['end']): ?>
    "startDate": "<?php echo date('Y-m-d',strtotime($_GET['start'])); ?>",
    "endDate": "<?php echo date('Y-m-d',strtotime($_GET['end'])); ?>",
    <?php else: ?>
    "autoUpdateInput": false,
    <?php endif; ?>
    
  }, function(start, end, label) {
    window.location.href = window.location.pathname+"?"+$.param({'start':start.format('YYYY-MM-DD'),'end':end.format('YYYY-MM-DD')});
  });
</script>
<?php lms_layout_footer('admin'); ?>

