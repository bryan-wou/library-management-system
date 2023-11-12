<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('circ_checkin');

$lms_layout_title = 'Check In Item';

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
            <span><?php _e('Transaction Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          
          <div class="row">
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="input-group mb-0">
                <input type="text" class="form-control" id="input_itembarcode" placeholder="<?php esc_html_e('Input or Scan Item Barcode'); ?>" autofocus>
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" id="input_itembarcode_submitbtn" type="button" onclick="submitItem()"><?php esc_html_e('Submit'); ?></button>
                </div>
              </div>
              <div class="form-group mb-0">
              </div>
            </div>
            <!-- --------- -->
          </div>
          <hr>
          <div id="transactionrecord_showtransaction" style="display:none;">
            <div class="row">
              <div class="col-12 col-md-12">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemtitle"><?php _e('Biblio Title'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemtitle" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemcallnumber"><?php _e('Biblio Call Number'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemcallnumber" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemisbn"><?php _e('Biblio ISBN'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemisbn" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-12">
                <div class="form-group">
                  <label for="patronrecord_showpatron_patronname"><?php _e('Patron Name'); ?></label>
                  <input type="text" class="form-control" id="patronrecord_showpatron_patronname" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="transactionrecord_showtransaction_checkoutdate"><?php _e('Check Out Date'); ?></label>
                  <input type="text" class="form-control" id="transactionrecord_showtransaction_checkoutdate" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="transactionrecord_showtransaction_expectedcheckindate"><?php _e('Expected Check In Date'); ?></label>
                  <input type="text" class="form-control" id="transactionrecord_showtransaction_expectedcheckindate" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="transactionrecord_showtransaction_todaysdate"><?php _e("Today's Date"); ?></label>
                  <input type="text" class="form-control" id="transactionrecord_showtransaction_todaysdate" value="" disabled>
                </div>
              </div>
            </div>
          </div>
          <div id="itemrecord_showerror" style="display:none;">
            <div class="row">
              <div class="col-12 text-center">
                <span id="itemrecord_error" class="font-weight-bold text-danger">--</span>
              </div>
            </div>
          </div>
          <div id="itemrecord_checkinbuttondiv" style="display:none;">
            <hr>
            <div class="row">
              <div class="col-12 text-center">
                <button class="btn btn-primary" onclick="submitCheckin();" id="itemrecord_checkinbutton">
                  <i class="fas fa-sign-in-alt mr-1"></i>
                  <?php _e('Check In'); ?>
                </button>
              </div>
            </div>


          </div>
        </div>
        
      </div>
    </div>
  </div>
  
</div>

<input type="hidden" id="checkin_biblioitemid">
<input type="hidden" id="checkin_itembarcode">


<?php lms_layout_scripts('admin'); ?>
<script>
  "use strict";
  

  function submitItem(){


    let itembarcode = $('#input_itembarcode').val().trim();

    if ($('#checkin_itembarcode').val().length 
    && $('#checkin_itembarcode').val() == itembarcode
    && $('#itemrecord_checkinbutton').is(':visible')
    && $('.swal2-container:visible').length<1
    ) {
      // is the same, if can checkout then checkout
      $('#itemrecord_checkinbutton').click();
      return;
    }

    $('#itemrecord_checkinbuttondiv').hide();
    $('#checkin_biblioitemid').val('');
    $('#checkin_itembarcode').val('');
    if (itembarcode) {
      $.post('admin-ajax.php',{do:'circ_getitem',data:{itembarcode:itembarcode}})
      .done(function(data){
        let arr = [];
        try {
          arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          
          $('#itemrecord_showitem_itemtitle').val(arr.data.biblio['biblioTitle']);
          $('#itemrecord_showitem_itemcallnumber').val(arr.data.biblio['biblioCallNumber']);
          $('#itemrecord_showitem_itemisbn').val(arr.data.biblio['biblioISBN']);
          if (arr.data.itemCanCheckIn.canCheckIn) {
            $('#patronrecord_showpatron_patronname').val(arr.data.patron['patronName']);
            $('#transactionrecord_showtransaction_checkoutdate').val(arr.data.transaction['checkOutDate']);
            $('#transactionrecord_showtransaction_expectedcheckindate').val(arr.data.transaction['expectedCheckInDate']);
          }
          $('#transactionrecord_showtransaction_todaysdate').val(arr.data.todaysDate);
          // $('#itemrecord_showitem_expectedreturndate').val(arr.data['_expectedreturndate']);

          $('#transactionrecord_showtransaction').show();

          if (arr.data.itemCanCheckIn.canCheckIn == true) {
            
            $('#itemrecord_showerror').hide();
            $('#input_itembarcode').select().focus();
            $('#itemrecord_checkinbuttondiv').show();
            
            $('#checkin_biblioitemid').val(arr.data.biblioItem['biblioItemID']);
            $('#checkin_itembarcode').val(itembarcode);
            
          } else {
            $('#itemrecord_error').text(arr.data.itemCanCheckIn.reason);
            $('#itemrecord_showerror').show();
            $('#input_itembarcode').select().focus();

          }
        } catch (error) {
          console.error(error);
          $('#itemrecord_error').text(arr.errormsg ?? error.toString());
          $('#transactionrecord_showtransaction').hide();
          $('#itemrecord_showerror').show();
          $('#input_itembarcode').select().focus();
          // Swal.fire('Error1',error.toString(),'error');
        }
      })
      .fail(function(data){
        Swal.fire('Error2');
        $('#input_itembarcode').select().focus();
      });
    } else {
      // no barcode
      $('#input_itembarcode').focus();
    }
  }

  function submitCheckin(){
    let data = {};
    data.biblioitemid = $('#checkin_biblioitemid').val(); // mandatory


    $.post('admin-ajax.php',{do:'circ_checkin',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Checked In Item')); ?>',
            // text: '<?php echo addslashes(esc_html__('Expected Check In Date: ')); ?>' + arr.data['expectedCheckInDate'], 
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


  $("#input_patronbarcode").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      $('#input_patronbarcode_submitbtn').click();
    }
  });
  $("#input_itembarcode").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      $('#input_itembarcode_submitbtn').click();
    }
  });
  $("body").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      if ($('.swal2-container:visible').length>0) {
        Swal.close();
      }
    }
  });
</script>
<?php lms_layout_footer('admin'); ?>

