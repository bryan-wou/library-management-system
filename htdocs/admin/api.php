<?php

require_once __DIR__.'/../includes/load-lms.php';

function lms_api_check_auth($userSuppliedApiKey){
  $knownApiKey = lms_settings_get('api_key');
  if (!$knownApiKey) { lms_api_do_error('Server Error: API key not found'); }
  if (!hash_equals($knownApiKey,($userSuppliedApiKey??''))) { lms_api_do_error('Invalid API Key'); }
}

function lms_api_do_error($error){
  echo lms_api_prepare_response(false, null, $error);
  die();
}

function lms_api_parse_json($json){
  $arr = json_decode($json, true);
  if (json_last_error()) {
    lms_api_do_error('Invalid JSON');
  }
}

function lms_api_prepare_response($success, $data=null, $errormsg=null){
  if (is_array($success)) {
    if ($success['success']??false) {
      $response = array(
        'success' => ($success['success']??null),
        'data' => ($success['data']??null),
        'errormsg' => ($success['errormsg']??null)
      );
    } else {
      $response = array(
        'success' => (!!$success),
        'data' => ($success)
      );
    }
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

lms_api_check_auth(@$_POST['apikey']);

switch (@$_POST['do']??'') {
  case 'addbiblio':
    $do = lms_add_biblio_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;
    
  case 'addbiblioitem':
    $do = lms_add_item_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;

  case 'addpatron':
    $do = lms_add_patron_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;
    
  case 'getpatron':
    $do = lms_get_patron_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;
    
  case 'getbiblioitem':
    $do = lms_get_item_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;

  case 'getbiblio':
    $do = lms_get_biblio_record(@$_POST['data']);
    echo lms_api_prepare_response($do);
    break;

  default:
    lms_api_do_error('Invalid action');
    break;
}

die();

