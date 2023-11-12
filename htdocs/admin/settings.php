<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');
lms_check_librarian_privilege('manage_settings');

$settings = [];
$settings_names = [
  'library_name',
  'library_code',
  'allow_opac',
  'allow_opac_patron_login',
  'allow_opac_patron_renew'
];

if (isset($_POST['data'])) {
  $data = $_POST['data'];

  $data['library_code'] = str_pad(preg_replace("/[^0-9]/", "", $data['library_code'] ),4,'0',STR_PAD_LEFT);

  foreach ($settings_names as $settings_name) {
    lms_settings_set($settings_name, $data[$settings_name]);
  }

  echo json_encode(array('success'=>true));
  die();
}


foreach ($settings_names as $settings_name) {
  $settings[$settings_name] = lms_settings_get($settings_name);
}




$apiKey = lms_settings_get('api_key',false);
if (@!$apiKey || isset($_POST['resetapikey'])) {
  lms_settings_set('api_key',substr(bin2hex(random_bytes(36)),0,64));
  $apiKey = lms_settings_get('api_key',false);
}



$lms_layout_title = 'Settings';






// echo lms_get_next_codabar_number('biblioitem');
// die();
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
            <span>Settings</span>
          </h3>
        </div>
        <div class="card-body">
          
          <!-- =============== -->
          <div class="form-group">
            <label class="mb-0" for="settings-library_name">
              <span><?php _e('Library Name'); ?></span>
            </label> 
            <div class="my-1">
              <input 
                class="form-control setting-input" 
                id="settings-library_name" 
                data-settingname="library_name"
                placeholder="<?php _e('Enter Library Name here...'); ?>"
                value="<?php echo addslashes($settings['library_name']); ?>"
              >
            </div>
          </div>
          <!-- =============== -->
          <div class="form-group">
            <label class="mb-0" for="settings-library_code">
              <span><?php _e('Library Code'); ?> <small class="font-weight-normal"><?php _e('(four-digit library code for Codabar barcode format)'); ?></small></span>
            </label> 
            <div class="my-1">
              <input 
                class="form-control setting-input" 
                id="settings-library_code" 
                data-settingname="library_code"
                placeholder="<?php _e('Enter four-digit Library Code here...'); ?>"
                value="<?php echo addslashes($settings['library_code']); ?>"
                pattern="[0-9]{4}" maxlength="4"
              >
            </div>
          </div>
          <!-- =============== -->
          <div class="form-group">
            <label class="mb-0">
              <span><?php _e('OPAC Settings'); ?></span>
            </label> 
            <div class="form-check ml-3 my-1">
              <input 
                class="form-check-input setting-checkbox" 
                type="checkbox" 
                id="settings-allow_opac" 
                data-settingname="allow_opac"
                <?php echo (($settings['allow_opac']=='true')?'checked':''); ?>
              >
              <label class="form-check-label" for="settings-allow_opac">
                <span><?php _e('Enable OPAC access'); ?></span>
              </label>
            </div>
            <div class="form-check ml-3 my-1">
              <input 
                class="form-check-input setting-checkbox" 
                type="checkbox" 
                id="settings-allow_opac_patron_login" 
                data-settingname="allow_opac_patron_login"
                <?php echo (($settings['allow_opac_patron_login']=='true')?'checked':''); ?>
              >
              <label class="form-check-label" for="settings-allow_opac_patron_login">
                <span><?php _e('Allow patrons to log into OPAC'); ?></span>
              </label>
            </div>
            <div class="form-check ml-3 my-1">
              <input 
                class="form-check-input setting-checkbox" 
                type="checkbox" 
                id="settings-allow_opac_patron_renew" 
                data-settingname="allow_opac_patron_renew"
                <?php echo (($settings['allow_opac_patron_renew']=='true')?'checked':''); ?>
              >
              <label class="form-check-label" for="settings-allow_opac_patron_renew">
                <span><?php _e('Allow patrons to self-renew transactions'); ?></span>
              </label>
            </div>
            <!-- <small id="" class="form-text text-muted">
              <span><?php _e(''); ?></span>
            </small> -->
          </div>

          <hr>

          <div class="row">
            <div class="col-12 text-center">

              <button class="btn btn-primary" onclick="saveSettings();">
                <i class="fas fa-save mx-1"></i>
                <?php _e('Save'); ?>
              </button>
              
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <?php _e('API'); ?>
          </h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="mb-0" for="">
              <span><?php _e('API Key'); ?></span>
            </label> 
            <div class="my-1">
              <div class="input-group">
                <input 
                  class="form-control" 
                  value="<?php echo addslashes($apiKey); ?>"
                  readonly
                >
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" onclick="resetApiKey();"><?php _e('Reset'); ?></button>
                </div>
              </div>
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
  function saveSettings(){
    let data = {};
    $('.setting-input').each(function(){
      data[ $(this).data('settingname') ] = $(this).val();
    });
    $('.setting-checkbox').each(function(){
      data[ $(this).data('settingname') ] = $(this).is(':checked');
    });
    $.post(location.href,{data:data})
    .done(function(data){
      try {
        const arr = JSON.parse(data);
        if (!arr.success) { throw Error(data); }
        Swal.fire({
          title: '<?php echo addslashes(esc_html__('Successfully Saved Settings')); ?>',
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

  function resetApiKey(){
    $.post(location.href,{resetapikey:1})
    .done(function(data){
      try {
        Swal.fire({
          title: '<?php echo addslashes(esc_html__('Successfully Reset Api Key')); ?>',
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
<?php lms_layout_footer('admin'); ?>

