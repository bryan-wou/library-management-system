<?php

require_once __DIR__.'/../includes/load-lms.php';

lms_check_auth('Librarian');

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
  case 'addbiblio':
    lms_check_librarian_privilege('add_biblioitem_record');
    $do = lms_add_biblio_record($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;
    
  case 'addbiblioitem':
    lms_check_librarian_privilege('add_biblioitem_record');
    $do = lms_add_item_record($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'addpatron':
    lms_check_librarian_privilege('add_patron_record');
    $do = lms_add_patron_record($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;
    
  case 'circ_getpatron':
    lms_check_librarian_privilege(['circ_checkout','circ_checkin','circ_renew']);
    $do = lms_circ_get_patron_from_barcode($_POST['data']);
    if ($do['success']) {
      $do['data']['_availablequota'] = lms_get_patron_available_quota($do['data']['patron']['patronID']);
    }
    echo lms_ajax_prepare_response($do);
    break;
    
  case 'circ_getitem':
    lms_check_librarian_privilege(['circ_checkout','circ_checkin','circ_renew']);
    $do = lms_circ_get_item_from_barcode($_POST['data']['itembarcode']);
    if ($do['success'] && isset($_POST['data']['patronid'])) {
      $do['data']['_expectedreturndate'] = lms_circ_calculate_check_out_return_date($_POST['data']['patronid']);
    }
    echo lms_ajax_prepare_response($do);
    break;

  case 'circ_checkout':
    lms_check_librarian_privilege(['circ_checkout']);
    $do = lms_circ_check_out_item($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'circ_checkin':
    lms_check_librarian_privilege(['circ_checkin']);
    $do = lms_circ_check_in_item($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'circ_renew':
    lms_check_librarian_privilege(['circ_renew']);
    $do = lms_circ_renew_item($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'addlibrarian':
    lms_check_librarian_privilege(['manage_librarians']);
    $do = lms_librarian_add_librarian($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'deletelibrarian':
    lms_check_librarian_privilege(['manage_librarians']);
    $do = lms_librarian_delete_librarian($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'addpatroncategory':
    lms_check_librarian_privilege(['manage_patron_categories']);
    $do = lms_add_patron_category($_POST['data']);
    echo lms_ajax_prepare_response($do);
    break;

  case 'changelocale':
    lms_set_locale($_POST['data']);
    echo lms_ajax_prepare_response($do);
    
  default:
    lms_ajax_do_error('Invalid action');
    break;
}

die();