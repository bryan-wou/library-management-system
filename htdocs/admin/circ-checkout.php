<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('circ_checkout');

$lms_layout_title = 'Check Out Item';

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
            <span><?php _e('Patron Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          
          <div class="row">
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="input-group mb-0">
                <input type="text" class="form-control" id="input_patronbarcode" placeholder="<?php esc_html_e('Input or Scan Patron Barcode'); ?>" autofocus>
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" id="input_patronbarcode_submitbtn" type="button" onclick="submitPatron()"><?php esc_html_e('Submit'); ?></button>
                </div>
              </div>
              <div class="form-group mb-0">
              </div>
            </div>
            <!-- --------- -->
          </div>
          <hr>
          <div id="patronrecord_showpatron" style="display:none;">
            <div class="row">
              <div class="col-12 col-md-8">
                <div class="form-group">
                  <label for="patronrecord_showpatron_patronname"><?php _e('Name'); ?></label>
                  <input type="text" class="form-control" id="patronrecord_showpatron_patronname" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="patronrecord_showpatron_patronquota"><?php _e('Available Quota'); ?></label>
                  <input type="text" class="form-control" id="patronrecord_showpatron_patronquota" value="" disabled>
                </div>
              </div>
            </div>
          </div>
          <div id="patronrecord_showerror" style="display:none;">
            <div class="row">
              <div class="col-12 text-center">
                <span id="patronrecord_error" class="font-weight-bold text-danger">--</span>
              </div>
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
            <span><?php _e('Item Record'); ?></span>
          </h3>
        </div>
        <div class="card-body" id="showitem_blockmsg">
          <div class="row">
            <!-- --------- -->
            <div class="col-12 col-md-12 text-center">
              <span id="" class="font-weight-bold text-secondary"><?php _e('Please Select a Patron'); ?></span>
            </div>
            <!-- --------- -->
          </div>
        </div>
        <div class="card-body" id="showitem_wholediv" style="display:none;">
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
          <div id="itemrecord_showitem" style="display:none;">
            <div class="row">
              <div class="col-12 col-md-12">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemtitle"><?php _e('Title'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemtitle" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemcallnumber"><?php _e('Call Number'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemcallnumber" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="itemrecord_showitem_itemisbn"><?php _e('ISBN'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_itemisbn" value="" disabled>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="itemrecord_showitem_expectedreturndate"><?php _e('Return Date'); ?></label>
                  <input type="text" class="form-control" id="itemrecord_showitem_expectedreturndate" value="" disabled>
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
          <div id="itemrecord_checkoutbuttondiv" style="display:none;">
            <hr>
            <div class="row">
              <div class="col-12 text-center">
                <button class="btn btn-primary" onclick="submitCheckout();" id="itemrecord_checkoutbutton">
                  <i class="fas fa-sign-out-alt mr-1"></i>
                  <?php _e('Check Out'); ?>
                </button>
              </div>
            </div>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="checkout_patronid">
<input type="hidden" id="checkout_biblioitemid">
<input type="hidden" id="checkout_itembarcode">


<?php lms_layout_scripts('admin'); ?>
<script>
  "use strict";
  function submitPatron(){
    let patronbarcode = $('#input_patronbarcode').val().trim();
    $('#showitem_blockmsg').show();
    $('#showitem_wholediv').hide();
    
    $('#checkout_patronid').val('');
    if (patronbarcode) {
      $.post('admin-ajax.php',{do:'circ_getpatron',data:patronbarcode})
      .done(function(data){
        let arr = [];
        try {
          arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          
          $('#patronrecord_showpatron_patronname').val(arr.data.patron['patronName']);
          
          $('#patronrecord_showpatron_patronquota').val(arr.data['_availablequota']);

          $('#patronrecord_showpatron').show();

          if (arr.data.patronCanCheckOut.canCheckOut == true) {
            // OK!!
            
            $('#checkout_patronid').val(arr.data.patron['patronID']);
            $('#showitem_blockmsg').hide();
            $('#showitem_wholediv').show();
            $('#itemrecord_showitem').hide();
            $('#itemrecord_showerror').hide();
            $('#itemrecord_checkoutbuttondiv').hide();
            $('#patronrecord_showerror').hide();
            $('#input_itembarcode').val('').select().focus();
            
          } else {
            $('#patronrecord_error').text(arr.data.patronCanCheckOut.reason);
            $('#patronrecord_showerror').show();
            $('#input_patronbarcode').select().focus();

          }
        } catch (error) {
          console.error(error);
          $('#patronrecord_error').text(arr.errormsg ?? error.toString());
          $('#patronrecord_showpatron').hide();
          $('#patronrecord_showerror').show();
        $('#input_patronbarcode').select().focus();
          // Swal.fire('Error1',error.toString(),'error');
        }
      })
      .fail(function(data){
        Swal.fire('Error2');
        $('#input_patronbarcode').select().focus();
      });
    } else {
      // no barcode
      $('#input_patronbarcode').focus();
    }
  }

  function submitItem(){


    let patronid = $('#checkout_patronid').val();
    let itembarcode = $('#input_itembarcode').val().trim();

    if ($('#checkout_itembarcode').val().length 
    && $('#checkout_itembarcode').val() == itembarcode
    && $('#itemrecord_checkoutbutton').is(':visible')
    && $('.swal2-container:visible').length<1
    ) {
      // is the same, if can checkout then checkout
      $('#itemrecord_checkoutbutton').click();
      return;
    }

    $('#itemrecord_checkoutbuttondiv').hide();
    $('#checkout_biblioitemid').val('');
    $('#checkout_itembarcode').val('');
    // $('#showitem_wholediv').hide();
    if (itembarcode) {
      $.post('admin-ajax.php',{do:'circ_getitem',data:{itembarcode:itembarcode,patronid:patronid}})
      .done(function(data){
        let arr = [];
        try {
          arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          
          $('#itemrecord_showitem_itemtitle').val(arr.data.biblio['biblioTitle']);
          $('#itemrecord_showitem_itemcallnumber').val(arr.data.biblio['biblioCallNumber']);
          $('#itemrecord_showitem_itemisbn').val(arr.data.biblio['biblioISBN']);
          $('#itemrecord_showitem_expectedreturndate').val(arr.data['_expectedreturndate']);

          $('#itemrecord_showitem').show();

          if (arr.data.itemCanCheckOut.canCheckOut == true) {
            
            $('#itemrecord_showerror').hide();
            $('#input_itembarcode').select().focus();
            $('#itemrecord_checkoutbuttondiv').show();
            
            $('#checkout_biblioitemid').val(arr.data.biblioItem['biblioItemID']);
            $('#checkout_itembarcode').val(itembarcode);
            
          } else {
            $('#itemrecord_error').text(arr.data.itemCanCheckOut.reason);
            $('#itemrecord_showerror').show();
            $('#input_itembarcode').select().focus();

          }
        } catch (error) {
          console.error(error);
          $('#itemrecord_error').text(arr.errormsg ?? error.toString());
          $('#itemrecord_showitem').hide();
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

  function submitCheckout(){
    let data = {};
    data.patronid     = $('#checkout_patronid').val(); // mandatory
    data.biblioitemid = $('#checkout_biblioitemid').val(); // mandatory


    $.post('admin-ajax.php',{do:'circ_checkout',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Checked Out Item')); ?>',
            text: '<?php echo addslashes(esc_html__('Expected Check In Date: ')); ?>' + arr.data['expectedCheckInDate'], 
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

