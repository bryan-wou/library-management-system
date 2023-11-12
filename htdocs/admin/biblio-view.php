<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('search_biblioitem_record');

$lms_layout_title = 'Add New Biblio/Item Record';

$biblioData = lms_get_biblio_record($_GET['bid']??'');
if ($biblioData) {
  $biblioItems = lms_get_item_records_from_biblio_id($biblioData['biblioID']);
}

?>
<?php lms_layout_header('admin'); ?>


<div class="container">
  <div class="content-header">
  </div>
  <?php if($biblioData): ?>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Bibliographic Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_title"><?php _e('Title'); ?></label>
                <input type="text" class="form-control biblio-data-input" id="input_title" value="<?php echo addslashes(esc_html($biblioData['biblioTitle'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_author"><?php _e('Author'); ?></label>
                <input type="text" class="form-control biblio-data-input" id="input_author" value="<?php echo addslashes(esc_html($biblioData['biblioAuthor'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_publisher"><?php _e('Publisher'); ?></label>
                <input type="text" class="form-control biblio-data-input" id="input_publisher" value="<?php echo addslashes(esc_html($biblioData['biblioPublisher'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_callnumber"><?php _e('Call No.'); ?></label>
                <input type="text" class="form-control biblio-data-input" id="input_callnumber" value="<?php echo addslashes(esc_html($biblioData['biblioCallNumber'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_isbn"><?php _e('ISBN'); ?><a href="javascript:void(0)" class="ml-1 font-weight-normal" onclick="getDataFromISBN()">Autofill</a></label>
                <input type="text" class="form-control biblio-data-input" id="input_isbn" value="<?php echo addslashes(esc_html($biblioData['biblioISBN'])); ?>" disabled>
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_restricted"><?php _e('Restrict Check-Out'); ?></label>
                <select id="input_restricted" class="form-control biblio-data-input" disabled>
                  <option value="0" <?php if($biblioData['isRestricted']==0){echo 'selected';} ?>><?php _e('No Restriction'); ?></option>
                  <option value="1" <?php if($biblioData['isRestricted']==1){echo 'selected';} ?>><?php _e('Restrict from being Checked Out'); ?></option>
                </select>
              </div>
            </div>
            <!-- --------- -->
          </div>
          <hr>
          <div class="row" id="biblio-data-mainbuttons">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="location.href='biblio-add.php'">
                <i class="fas fa-plus mx-1"></i>
                <?php _e('Add Another Biblio Record'); ?>
              </button>
              <button class="btn btn-outline-primary" onclick="enableBiblioEdit();">
                <i class="fas fa-edit mx-1"></i>
                <?php _e('Edit Biblio Record'); ?>
              </button>
              <button class="btn btn-outline-danger" onclick="deleteBiblio();">
                <i class="fas fa-trash mx-1"></i>
                <?php _e('Delete Biblio Record'); ?>
              </button>
            </div>
          </div>
          <div class="row" id="biblio-data-editbuttons" style="display:none;">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="submitBiblioEdit();">
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
  <div class="row" id="biblioitem-row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <span><?php _e('Item Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- ----ITEM RECORD HERE----- -->
            <div class="col-12">
              <table class="table table-bordered table-hover table-sm">
                <tr>
                  <th>Barcode</th>
                  <th>Location</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                <?php foreach ($biblioItems as $biblioItem) { ?>
                  <tr>
                    <td id="bi-barcode-biid-<?php echo +$biblioItem['biblioItemID']; ?>"><?php echo esc_html($biblioItem['_barcode']); ?></td>
                    <td id="bi-location-biid-<?php echo +$biblioItem['biblioItemID']; ?>"><?php echo esc_html($biblioItem['biblioItemLocation']); ?></td>
                    <td id="bi-price-biid-<?php echo +$biblioItem['biblioItemID']; ?>"><?php echo esc_html($biblioItem['biblioItemPrice']); ?></td>
                    <td></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick="enableBiblioItemEdit(<?php echo +$biblioItem['biblioItemID']; ?>)"><?php _e('Edit'); ?></button></td>
                  </tr>
                <?php } ?>
              </table>
            </div>
          </div>
          <hr>
          <div class="row" id="biblioitem_additembutton_div">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="$('#biblioitem_additembutton_div').slideUp();$('#biblioitem_additem_div').slideDown();">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add Item Record'); ?>
              </button>
            </div>
          </div>
          <div class="row" id="biblioitem_additem_div" style="display:none;">
            <!-- --###- -->
            <div class="col-12">
              <div class="row">
                <!-- --------- -->
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="input_barcode"><?php _e('Barcode'); ?><a href="javascript:void(0)" class="ml-1 font-weight-normal" onclick="$('#input_barcode').val('<<New>>')"><?php _e('Autonumber'); ?></a></label>
                    <input type="text" class="form-control" id="input_barcode">
                  </div>
                </div>
                <!-- --------- -->
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="input_location"><?php _e('Location'); ?></label>
                    <input type="text" class="form-control" id="input_location">
                  </div>
                </div>
                <!-- --------- -->
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="input_price"><?php _e('Price'); ?></label>
                    <input type="text" class="form-control" id="input_price">
                  </div>
                </div>
                <!-- --------- -->
              </div>
              <hr>
              <div class="row">
                <div class="col-12 text-center">
                  <button class="btn btn-primary bi-add-buttons" onclick="submitBiblioItem();">
                    <i class="fas fa-plus mr-1"></i>
                    <?php _e('Add Item Record'); ?>
                  </button>
                  
                  <button class="btn btn-primary bi-edit-buttons" onclick="submitBiblioItemEdit();" style="display:none;" id="bi-edit-button-submit">
                    <i class="fas fa-save mx-1"></i>
                    <?php _e('Save'); ?>
                  </button>
                  <button class="btn btn-outline-secondary bi-edit-buttons" onclick="location.reload()"  style="display:none;">
                    <i class="fas fa-sync mx-1"></i>
                    <?php _e('Reset'); ?>
                  </button>
                </div>
              </div>
            </div>
            <!-- -###- -->
          </div>
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
            <span><?php _e('Biblio Record'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <div class="alert alert-danger mb-3" role="alert">
            <strong><?php _e('Error:'); ?></strong>
            <?php _e('Biblio Record Not Found'); ?>
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

  function enableBiblioEdit(){
    $('#biblioitem-row').hide();
    $('.biblio-data-input').prop('disabled',false);
    $('#biblio-data-mainbuttons').hide();
    $('#biblio-data-editbuttons').show();
  }

  function submitBiblioEdit(){
    let data = {};
    data.biblioid     = '<?php echo addslashes($biblioData['biblioID']); ?>';
    data.title        = $('#input_title').val().trim(); // mandatory
    data.author       = $('#input_author').val().trim();
    data.publisher    = $('#input_publisher').val().trim();
    data.callnumber   = $('#input_callnumber').val().trim();
    data.isbn         = $('#input_isbn').val().trim();
    data.isrestricted = $('#input_restricted>option:selected').val().trim();

    let errors = [];

    if (data.title.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Title.')); ?>');
    }
    
    if (errors.length) {
      Swal.fire({
        title: 'Error',
        html: errors.join('<br>'),
        icon: 'error'
      });
      return false;
    }

    $.post('admin-ajax.php',{do:'addbiblio',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Edited Bibliographic Record')); ?>',
            icon: 'success',
            willClose: function(){
              window.location.replace('biblio-view.php?bid='+arr.data.biblioid);
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

  function enableBiblioItemEdit(biid){
    $('#biblioitem_additembutton_div').slideUp();
    $('#biblioitem_additem_div').slideDown();

    $('#input_barcode').val($('#bi-barcode-biid-'+biid).text());
    $('#input_location').val($('#bi-location-biid-'+biid).text());
    $('#input_price').val($('#bi-price-biid-'+biid).text());

    $('#bi-edit-button-submit').data('biid',biid);

    $('.bi-add-buttons').hide();
    $('.bi-edit-buttons').show();


  }

  function submitBiblioItem(){
    let data = {};
    data.biblioid = <?php echo addslashes(esc_html($biblioData['biblioID']??0)); ?>;
    data.barcode  = $('#input_barcode').val().trim(); // mandatory
    data.location = $('#input_location').val().trim();
    data.price    = $('#input_price').val().trim();

    let errors = [];

    if (data.barcode.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Title.')); ?>');
    }
    
    if (errors.length) {
      Swal.fire({
        title: 'Error',
        html: errors.join('<br>'),
        icon: 'error'
      });
      return false;
    }

    $.post('admin-ajax.php',{do:'addbiblioitem',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Added Item Record')); ?>',
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

  function submitBiblioItemEdit(){
    
    let data = {};
    data.biblioid = <?php echo addslashes(esc_html($biblioData['biblioID']??0)); ?>;
    data.biblioitemid = $('#bi-edit-button-submit').data('biid');
    data.barcode  = $('#input_barcode').val().trim(); // mandatory
    data.location = $('#input_location').val().trim();
    data.price    = $('#input_price').val().trim();

    let errors = [];

    if (data.barcode.length < 1) {
      errors.push('<?php echo addslashes(esc_html__('Please enter a Title.')); ?>');
    }
    
    if (errors.length) {
      Swal.fire({
        title: 'Error',
        html: errors.join('<br>'),
        icon: 'error'
      });
      return false;
    }

    $.post('admin-ajax.php',{do:'addbiblioitem',data:data})
      .done(function(data){
        try {
          const arr = JSON.parse(data);
          if (!arr.success) { throw Error(data); }
          Swal.fire({
            title: '<?php echo addslashes(esc_html__('Successfully Edited Item Record')); ?>',
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

  function getDataFromISBN(){
    let isbn = $('#input_isbn').val().trim();
    $.get('https://www.googleapis.com/books/v1/volumes?q=isbn:'+isbn)
    .done(function(data){
      if (data.totalItems > 0) {
        let booktitle = data.items[0].volumeInfo.title ?? '';
        if (data.items[0].volumeInfo.subtitle??false) {
          booktitle += ' - ' + data.items[0].volumeInfo.subtitle;
        }
        let bookauthor = (data.items[0].volumeInfo.authors ?? []).join(', ');
        let bookpublisher = data.items[0].volumeInfo.publisher ?? '';
        $('#input_title').val(booktitle);
        $('#input_author').val(bookauthor);
        $('#input_publisher').val(bookpublisher);
      } else {
        Swal.fire({
          title: 'No Results Found'
        });
      }
    });
  }
</script>
<?php lms_layout_footer('admin'); ?>

