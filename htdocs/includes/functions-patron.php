<?php

function lms_get_available_patron_categories(){
  $q = DB::query('SELECT * FROM PatronCategory');
  return $q;
}

function lms_get_patron_category($patronCategoryID){
  $q = DB::queryFirstRow('SELECT * FROM PatronCategory WHERE patronCategoryID = %i', $patronCategoryID);
  return $q;
}

function lms_add_patron_record($data){
  $dryrun = @!!$data['dryrun'];
  $insertArray = [];
  $insertArray['patronID']          = +@$data['patronid']?:NULL;
  $insertArray['patronName']        = trim(@$data['name']);
  $insertArray['patronContact']     = trim(@$data['contact']);
  $insertArray['patronCategoryID']  = trim(@$data['category']);
  $insertArray['password']          = (mb_strlen(@$data['password'])?password_hash(@$data['password'],PASSWORD_DEFAULT):null);
  if (!mb_strlen($insertArray['patronName'])) { return array('success'=>false,'errormsg'=>'Missing Name'); }
  if (!mb_strlen($insertArray['patronCategoryID'])) { return array('success'=>false,'errormsg'=>'Missing Category'); }
  if (is_null($insertArray['patronCategoryID'])) { return array('success'=>false,'errormsg'=>'Invalid Category'); }
  if (!mb_strlen(@$data['barcode'])) { return array('success'=>false,'errormsg'=>'Missing Barcode'); }
  if ($dryrun) { return array('success'=>true); }
  if (!is_null($insertArray['patronID'])) { DB::delete('PatronBarcode', 'patronID = %s', $insertArray['patronID']); }
  if ($data['barcode'] == '<<New>>') {
    $data['barcode'] = lms_get_next_codabar_number('patron');
    if ($data['barcode']===false) { return array('success'=>false,'errormsg'=>'Auto-number failed'); }
  }
  if (!is_null(lms_get_patron_record_from_barcode($data['barcode']))) { return array('success'=>false,'errormsg'=>'Barcode Exists'); }
  DB::insertUpdate('Patron', $insertArray);
  $patronID = DB::insertId() ?: +$data['patronid'];
  DB::insert('PatronBarcode', array(
    'patronBarcode' => $data['barcode'],
    'patronID' => $patronID
  ));
  return array('success'=>true,'data'=>array('patronid'=>$patronID));
}

function lms_get_patron_record($patronID){
  $q = DB::queryFirstRow('SELECT *,IF(ISNULL(password),NULL,"x") as password FROM Patron WHERE patronID = %i', $patronID);
  if ($q) {
    $q['_barcode'] = DB::queryFirstField('SELECT patronBarcode FROM PatronBarcode WHERE patronID = %i', $patronID);
  }
  return $q;
}

function lms_get_patron_record_from_barcode($barcode){
  $patronID = DB::queryFirstField('SELECT patronID FROM PatronBarcode WHERE patronBarcode = %s', $barcode);
  return lms_get_patron_record($patronID);
}

function lms_search_patron_records_by_keyword($keyword){
  $q = DB::query('SELECT *,IF(ISNULL(password),NULL,"x") as password FROM Patron p
                  WHERE p.patronName LIKE %ss0
                  OR p.patronContact LIKE %ss0
                  OR p.patronID = (
                    SELECT patronID FROM PatronBarcode 
                    WHERE patronBarcode = %s0
                  )
                  LIMIT 101', $keyword);
  if ($q) {
    foreach ($q as &$v_ptr) {
      unset($v_ptr['password']);
      $v_ptr['_barcode'] = DB::queryFirstField('SELECT patronBarcode FROM PatronBarcode WHERE patronID = %i', $v_ptr['patronID']);
    }
  }
  return $q;
}

function lms_get_patron_available_quota($patronID){
  $patron = lms_get_patron_record($patronID);
  $usedQuota = DB::queryFirstField('SELECT COUNT(*) FROM Transaction WHERE patronID = %i AND actualCheckInDate IS NULL', $patronID);
  // var_dump(lms_get_patron_category($patron['patronCategoryID']));
  $totalQuota = lms_get_patron_category($patron['patronCategoryID'])['itemCheckOutLimit'];
  return $totalQuota - $usedQuota;
}

function lms_get_transactions_from_patron_id($patronID){
  $q = DB::query('SELECT * FROM Transaction WHERE patronID = %i', $patronID);
  if ($q) {
    foreach ($q as &$v_ptr) {
      $v_ptr['_biblioitem'] = lms_get_item_record($v_ptr['biblioItemID']);
      $v_ptr['_biblio'] = lms_get_biblio_record($v_ptr['_biblioitem']['biblioID']);
    }
  }
  return $q;
}

function lms_add_patron_category($data){
  $insertArray = [];
  $insertArray['patronCategoryID']    = +@$data['patroncategoryid']?:NULL;
  $insertArray['patronCategoryName']  = trim($data['patroncategoryname']);
  $insertArray['itemCheckOutDays']    = +trim($data['itemcheckoutdays']);
  $insertArray['itemCheckOutLimit']   = +trim($data['itemcheckoutlimit']);
  $insertArray['itemRenewLimit']      = +trim($data['itemrenewlimit']);

  if (!mb_strlen($insertArray['patronCategoryName'])) {
    return array('success'=>false,'errormsg'=>'Missing Name');
  }
  DB::insertUpdate('PatronCategory', $insertArray);

  $cateogryID = DB::insertId() ?: +$data['patronid'];
  
  return array('success'=>true,'data'=>array('patroncategoryid'=>$cateogryID));
}