<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Patron');

lms_check_csrf_token($_POST['csrf_token']);

function lms_ajax_do_error($error){
  // http_response_code(400);
  echo lms_ajax_prepare_response(false, null, $error);
  die();
}

function lms_ajax_parse_json($json){
  $arr = json_decode($json, true);
  if (json_last_error()) {
    lms_ajax_do_error('Invalid JSON');
  }
}

function lms_ajax_prepare_response($success, $data=null, $errormsg=null){
  if (is_array($success)) {
    $response = array(
      'success' => ($success['success']??null),
      'data' => ($success['data']??null),
      'errormsg' => ($success['errormsg']??null)
    );
  } else {
    $response = array(
      'success' => $success,
      'data' => $data
    );
    if (!$success) {
      $response['errormsg'] = $errormsg ?? 'Unknown Error';
    }
  }
  return json_encode($response);
}


switch ($_POST['do']??'') {
  case 'renewtxn':
    if (  lms_settings_get('allow_opac')=='true' 
          && lms_settings_get('allow_opac_patron_login')=='true'
          && lms_settings_get('allow_opac_patron_renew')=='true'
    ) {
      $do = lms_circ_renew_item($_POST['data']);
      echo lms_ajax_prepare_response($do);
    } else {
      lms_ajax_prepare_response(false,null,'Not enabled');
    }
    
    break;

  case 'changelocale':
    lms_set_locale($_POST['data']);
    echo lms_ajax_prepare_response($do);
    
  default:
    lms_ajax_do_error('Invalid action');
    break;
}

die();