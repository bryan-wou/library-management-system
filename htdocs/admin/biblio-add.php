<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('add_biblioitem_record');

$lms_layout_title = 'Add New Biblio/Item Record';
?>
<?php lms_layout_header('admin'); ?>


<div class="container">
  <div class="content-header">
  </div>
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
                <input type="text" class="form-control" id="input_title">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_author"><?php _e('Author'); ?></label>
                <input type="text" class="form-control" id="input_author">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="input_publisher"><?php _e('Publisher'); ?></label>
                <input type="text" class="form-control" id="input_publisher">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_callnumber"><?php _e('Call No.'); ?></label>
                <input type="text" class="form-control" id="input_callnumber">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_isbn"><?php _e('ISBN'); ?><a href="javascript:void(0)" class="ml-1 font-weight-normal" onclick="getDataFromISBN()">Autofill</a></label>
                <input type="text" class="form-control" id="input_isbn">
              </div>
            </div>
            <!-- --------- -->
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label for="input_restricted"><?php _e('Restrict Check-Out'); ?></label>
                <select id="input_restricted" class="form-control">
                  <option value="0"><?php _e('No Restriction'); ?></option>
                  <option value="1"><?php _e('Restrict from being Checked Out'); ?></option>
                </select>
              </div>
            </div>
            <!-- --------- -->
          </div>
          <hr>
          <div class="row">
            <div class="col-12 text-center">
              <button class="btn btn-primary" onclick="submitBiblio();">
                <i class="fas fa-plus mr-1"></i>
                <?php _e('Add'); ?>
              </button>
              <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-undo-alt mr-1"></i>
                <?php _e('Reset'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php lms_layout_scripts('admin'); ?>
<script>
  "use strict";
  function submitBiblio(){
    let data = {};
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
            title: '<?php echo addslashes(esc_html__('Successfully Added Bibliographic Record')); ?>',
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

  function getDataFromISBN(){
    let isbn = $('#input_isbn').val().trim();
    if (isbn.length<1) { return; }
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
        Swal.fire({ title: '<?php _e('No Results Found'); ?>' });
      }
    });
  }
  
</script>
<?php lms_layout_footer('admin'); ?>

