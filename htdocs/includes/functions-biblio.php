<?php


function lms_add_biblio_record($data){
  $dryrun = @!!$data['dryrun'];
  $insertArray = [];
  $insertArray['biblioID']          = @+@$data['biblioid']?:NULL;
  $insertArray['biblioTitle']       = trim($data['title']);
  $insertArray['biblioCallNumber']  = trim($data['callnumber']);
  $insertArray['biblioAuthor']      = trim($data['author']);
  $insertArray['biblioPublisher']   = trim($data['publisher']);
  $insertArray['biblioISBN']        = trim($data['isbn']);
  $insertArray['isRestricted']      = trim($data['isrestricted']);
  if (!mb_strlen($insertArray['biblioTitle'])) { return array('success'=>false,'errormsg'=>'Missing Title'); }
  if ($dryrun) { return array('success'=>true); }
  DB::insertUpdate('Biblio', $insertArray);
  $biblioID = DB::insertId() ?: +$data['biblioid'];
  return array('success'=>true,'data'=>array('biblioid'=>$biblioID));
}

function lms_get_biblio_record($biblioID){
  $q = DB::queryFirstRow('SELECT * FROM Biblio WHERE biblioID = %i', $biblioID);
  return $q;
}

function lms_get_biblio_record_from_item_id($biblioItemID){
  $biblioItem = lms_get_item_record($biblioItemID);
  return lms_get_biblio_record($biblioItem['biblioID']);
}

function lms_add_item_record($data){
  $dryrun = @!!$data['dryrun'];
  $insertArray = [];
  $insertArray['biblioID']       = trim($data['biblioid']);
  $insertArray['biblioItemID']          = @+@$data['biblioitemid']?:NULL;
  $insertArray['biblioItemLocation']  = trim($data['location']);
  $insertArray['biblioItemPrice']      = trim($data['price']);
  if (!mb_strlen($data['barcode'])) { return array('success'=>false,'errormsg'=>'Missing Barcode'); }
  if ($data['barcode'] != '<<New>>' && !is_null(lms_get_item_record_from_barcode($data['barcode']))) { 
    return array('success'=>false,'errormsg'=>'Barcode Exists');
  }
  if ($dryrun) { return array('success'=>true); }
  if (!is_null($insertArray['biblioItemID'])) { DB::delete('BiblioItemBarcode', 'biblioItemID = %s', $insertArray['biblioItemID']); }
  if ($data['barcode'] == '<<New>>') {
    $data['barcode'] = lms_get_next_codabar_number('biblioitem');
    if ($data['barcode']===false) { return array('success'=>false,'errormsg'=>'Auto-number failed'); }
  }
  if (!is_null(lms_get_item_record_from_barcode($data['barcode']))) { return array('success'=>false,'errormsg'=>'Barcode Exists'); }
  DB::insertUpdate('BiblioItem', $insertArray);
  $biblioItemID = DB::insertId() ?: +$data['biblioitemid'];
  DB::insertIgnore('BiblioItemBarcode', array(
    'biblioItemBarcode' => $data['barcode'],
    'biblioItemID' => $biblioItemID
  ));
  return array('success'=>true,'data'=>array('biblioid'=>$insertArray['biblioID'], 'biblioitemid'=>$biblioItemID));
}

function lms_get_item_record($biblioItemID){
  $q = DB::queryFirstRow('SELECT * FROM BiblioItem WHERE biblioItemID = %i', $biblioItemID);
  if ($q) {
    $q['_barcode'] = DB::queryFirstField('SELECT biblioItemBarcode FROM BiblioItemBarcode WHERE biblioItemID = %i', $biblioItemID);
  }
  return $q;
}

function lms_get_item_records_from_biblio_id($biblioID){
  $q = DB::query('SELECT * FROM BiblioItem WHERE biblioID = %i', $biblioID);
  foreach ($q as &$v_ptr) {
    $v_ptr['_barcode'] = DB::queryFirstField('SELECT biblioItemBarcode FROM BiblioItemBarcode WHERE biblioItemID = %i', $v_ptr['biblioItemID']);
  }
  return $q;
}

function lms_get_item_record_from_barcode($barcode){
  $biblioItemID = DB::queryFirstField('SELECT biblioItemID FROM BiblioItemBarcode WHERE biblioItemBarcode = %s', $barcode);
  return lms_get_item_record($biblioItemID);
}

function lms_search_biblio_records_by_keyword($keyword){
  $q = DB::query("SELECT * FROM Biblio b
                  WHERE b.biblioTitle LIKE %ss0
                  OR b.biblioAuthor LIKE %ss0
                  OR b.biblioPublisher LIKE %ss0
                  OR b.biblioCallNumber LIKE %ss0
                  OR b.biblioISBN = %s0
                  OR b.biblioID = (
                    SELECT bi.biblioID FROM BiblioItem bi 
                    INNER JOIN BiblioItemBarcode bib ON bi.biblioItemID = bib.biblioItemID 
                    WHERE bib.biblioItemBarcode = %s0
                  )
                  LIMIT 101", $keyword);
  return $q;
}

function lms_get_uncheckedin_transaction_from_item_id($biblioItemID){
  $q = DB::queryFirstRow('SELECT * FROM transaction WHERE biblioItemID = %i AND actualCheckInDate IS NULL', $biblioItemID);
  return $q;
}

function lms_is_item_checked_out($biblioItemID){
  $q = lms_get_uncheckedin_transaction_from_item_id($biblioItemID);
  return $q > 0;
}

function lms_get_biblio_availability($biblioID){
  $restricted = DB::queryFirstField('SELECT isRestricted FROM Biblio
  WHERE biblioID = %i', $biblioID);
  
  $checked_out = DB::queryFirstField('SELECT COUNT(*) FROM Transaction t
  INNER JOIN BiblioItem bi ON t.biblioItemID = bi.biblioItemID
  WHERE t.actualCheckInDate IS NULL
  AND t.void = 0
  AND bi.biblioID = %i', $biblioID);
  $total_items = DB::queryFirstField('SELECT COUNT(*) FROM BiblioItem
  WHERE biblioID = %i', $biblioID);

  $available = $total_items - $checked_out;
  $available = ($available>0) ? $available : 0;

  return [
    'checked_out'=>$checked_out,
    'total_items'=>$total_items,
    'available'  =>$available,
    'restricted' =>$restricted,
  ];
}