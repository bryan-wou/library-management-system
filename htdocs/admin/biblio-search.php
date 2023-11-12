<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('search_biblioitem_record');

$lms_layout_title = 'Search Biblio/Item Record';

// if (isset($_POST['keyword']) && mb_strlen(trim($_POST['keyword']))) {
if (isset($_POST['keyword'])) {
  $searchResults = lms_search_biblio_records_by_keyword($_POST['keyword']??'');
  // var_dump($searchResults);
}



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
            <span><?php _e('Search Parameters'); ?></span>
          </h3>
        </div>
        <div class="card-body">
          <form action="" method="post">
            <div class="row">
              <!-- --------- -->
              <div class="col-12 col-md-12">
                <div class="form-group mb-0">
                  <label for="input_keyword"><?php _e('Keyword'); ?></label>
                  <input type="text" class="form-control" id="input_keyword" name="keyword" placeholder="Input Title / Author / Publisher / ISBN / Item Barcode" autofocus>
                </div>
              </div>
              <!-- --------- -->
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-12 text-center">
                <button class="btn btn-primary" type="submit">
                  <i class="fas fa-search mr-1"></i>
                  <?php _e('Search'); ?>
                </button>
                <button class="btn btn-outline-secondary" onclick="location.reload()">
                  <i class="fas fa-undo-alt mr-1"></i>
                  <?php _e('Reset'); ?>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php if(isset($searchResults)): ?>
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <span><?php _e('Search Results'); ?></span>
            </h3>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- --------- -->
              <div class="col-12 col-md-12">
                <?php if(count($searchResults??[])>100): ?>
                  <p class="text-center mb-0">- <?php _e('Too many results. Enter a more specific keyword.'); ?> -</p>
                <?php elseif(count($searchResults??[])>0): ?>
                  <table class="table table-sm table-hover table-bordered">
                    <tr>
                      <th>Title</th>
                      <th>Call Number</th>
                      <th>Author</th>
                      <th>Publisher</th>
                      <th>ISBN</th>
                      <th></th>
                    </tr>
                    <?php foreach ($searchResults as $sr) { ?>
                      <tr>
                        <td><?php echo esc_html($sr['biblioTitle']); ?></td>
                        <td><?php echo esc_html($sr['biblioCallNumber']); ?></td>
                        <td><?php echo esc_html($sr['biblioAuthor']); ?></td>
                        <td><?php echo esc_html($sr['biblioPublisher']); ?></td>
                        <td><?php echo esc_html($sr['biblioISBN']); ?></td>
                        <td><a href="biblio-view.php?bid=<?php echo esc_html($sr['biblioID']); ?>" class="btn btn-sm btn-outline-primary"><?php _e('View'); ?></a></td>
                      </tr>
                    <?php } ?>
                  </table>
                <?php else: ?>
                  <p class="text-center mb-0">- <?php _e('No Bibliographic Records Found'); ?> -</p>
                <?php endif; ?>
              </div>  
              <!-- --------- -->
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
  
</script>
<?php lms_layout_footer('admin'); ?>

