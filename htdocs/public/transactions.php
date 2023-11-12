<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Patron');


$opac_patron_renew_enabled = lms_settings_get('allow_opac')=='true' 
                              && lms_settings_get('allow_opac_patron_login')=='true'
                              && lms_settings_get('allow_opac_patron_renew')=='true';


$transactions = lms_get_transactions_from_patron_id($_SESSION['PatronID']);

$lms_layout_title = 'Transactions';

?>
<?php lms_layout_header('public'); ?>

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
            <span>Transactions</span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row justify-content-center ">
            <div class="col-auto">
              <table class="table table-sm table-bordered table-hover table-responsive">
                <tr>
                  <th><?php _e('Barcode'); ?></th>
                  <th><?php _e('Title'); ?></th>
                  <th><?php _e('Check Out Date'); ?></th>
                  <th><?php _e('Due Date'); ?></th>
                  <th><?php _e('Check In Date'); ?></th>
                  <th><?php _e(''); ?></th>
                </tr>
                <?php foreach ($transactions as $txn) { ?>
                  <tr>
                    <td><?php echo esc_html($txn['_biblioitem']['_barcode']); ?></td>
                    <td><?php echo esc_html($txn['_biblio']['biblioTitle']); ?></td>
                    <td><?php echo esc_html($txn['checkOutDate']); ?></td>
                    <td><?php echo esc_html($txn['expectedCheckInDate']); ?></td>
                    <td><?php echo esc_html($txn['actualCheckInDate']??'- not checked in -'); ?></td>
                    <td>
                      <?php if(lms_is_transaction_late($txn['transactionID'])): ?>
                        <span class="text-danger font-weight-bold"><?php _e('Overdue!'); ?></span>
                      <?php elseif(is_null($txn['actualCheckInDate']) && $opac_patron_renew_enabled): ?>
                        <?php 
                          $renewalLimit = lms_get_transaction_renewal_limit($txn['transactionID']);
                          $renewalLimitReached = lms_is_transaction_renewal_limit_reached($txn['transactionID']);
                        ?>
                        <button 
                          class="btn renew-item-btn btn-sm btn-outline-<?php echo (!$renewalLimitReached)?'primary':'secondary'; ?>" 
                          data-txnid="<?php echo +$txn['transactionID']; ?>"
                          data-norenew="<?php echo +$renewalLimitReached; ?>"
                        >
                          <?php printf(__('Renew (%s/%s)'),$renewalLimit['renewCount'],$renewalLimit['availableRenewCount']); ?>
                        </button>
                      <?php else: ?>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('public'); ?>
<script>
  "use strict";

  $('.renew-item-btn').click(function(){
    const txnid = $(this).data('txnid');;
    const norenew = $(this).data('norenew');
    if (norenew) {
      Swal.fire({
        'title': '<?php _e('Error'); ?>',
        'text': '<?php _e('Renewal Limit Reached'); ?>',
        'icon': 'error'
      });
      return false;
    }
    confirmRenewTxn(txnid);
  });
  function confirmRenewTxn(txnid){
    Swal.fire({
      title: '<?php _e('Confirm renewal?'); ?>',
      text:  '<?php printf(__('The due date will be extended until %s.'),lms_circ_calculate_check_out_return_date($_SESSION['PatronID'])) ?>',
      showCancelButton: true,
      confirmButtonText: '<?php _e('Renew'); ?>',
      cancelButtonText: `<?php _e('Cancel'); ?>`,
      icon: 'question',
    }).then((result) => {
      if (result.isConfirmed) {
        doRenewTxn(txnid);
      }
    })
  }
  function doRenewTxn(txnid){
    let data = {transactionid:txnid};
    console.log(data);
    $.post('public-ajax.php',{
      do:'renewtxn',
      data: data,
      csrf_token:'<?php echo lms_get_csrf_token(); ?>'
    })
    .done(function(data){
      try {
        const arr = JSON.parse(data);
        if (!arr.success) { throw Error(data); }
        Swal.fire({
          title: '<?php echo addslashes(esc_html__('Renewal Successful')); ?>',
          text:  '<?php printf(__('The due date has been extended until ')); ?>'+arr.data.expectedCheckInDate,
          icon: 'success',
          willClose: function(){
            window.location.reload();
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
<?php lms_layout_footer('public'); ?>

