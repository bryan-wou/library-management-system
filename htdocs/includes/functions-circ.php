<?php

function lms_circ_get_patron_from_barcode($barcode){
  $patron = lms_get_patron_record_from_barcode($barcode);
  if (is_null($patron)) {
    // no patron
    return array('success'=>false,'errormsg'=>'No Patron Record Found');
  }
  return array('success'=>true,
  'data'=>array(
    'patron'=>$patron,
    'patronCanCheckOut'=>lms_circ_can_patron_check_out($patron['patronID'])
  ));
}
function lms_circ_get_item_from_barcode($barcode){
  $biblioItem = lms_get_item_record_from_barcode($barcode);
  if (is_null($biblioItem)) {
    // no item
    return array('success'=>false,'errormsg'=>'No Item Record Found');
  }
  $biblio = lms_get_biblio_record($biblioItem['biblioID']);
  $arr = array('success'=>true,
          'data'=>array(
            'biblio'=>$biblio,
            'biblioItem'=>$biblioItem,
            'itemCanCheckOut'=>lms_circ_can_item_be_checked_out($biblioItem['biblioItemID']),
            'itemCanCheckIn'=>lms_circ_can_item_be_checked_in($biblioItem['biblioItemID']),
            'itemCanRenew'=>lms_circ_can_item_be_renewed($biblioItem['biblioItemID']),
          ));
  if ($arr['data']['itemCanCheckIn']['canCheckIn'] == true) {
    $uncheckedinTransaction = lms_get_uncheckedin_transaction_from_item_id($biblioItem['biblioItemID']);
    $arr['data']['transaction'] = $uncheckedinTransaction;
    $arr['data']['patron'] = lms_get_patron_record($uncheckedinTransaction['patronID']);
    $arr['data']['renewLimit'] = lms_get_transaction_renewal_limit($uncheckedinTransaction['transactionID']);
    $arr['data']['renewNewCheckInDate'] = lms_circ_calculate_check_out_return_date($uncheckedinTransaction['patronID']);
  }
  
  $arr['data']['todaysDate'] = date('Y-m-d');

  return $arr;
}

function lms_circ_check_out_item($data){
  $biblioItemID = $data['biblioitemid'];
  $patronID     = $data['patronid'];
  $biblioItem = lms_get_item_record($biblioItemID);
  $patron = lms_get_patron_record($patronID);
  $biblio = lms_get_biblio_record($biblioItem['biblioID']);
  if (is_null($biblioItem)) { return array('success'=>false,'errormsg'=>'No Item Record Found'); }
  if (is_null($patron)) { return array('success'=>false,'errormsg'=>'No Patron Record Found'); }
  if (lms_get_patron_available_quota($patronID) < 1) { return array('success'=>false,'errormsg'=>'Patron Quota Exhausted'); }
  if (lms_is_item_checked_out($biblioItemID) == true) { return array('success'=>false,'errormsg'=>'Item Already Checked Out'); }
  if ($biblio['isRestricted'] == 1) { return array('success'=>false,'errormsg'=>'Item Restricted '); }
  $insertArray = [];
  $insertArray['biblioItemID'] = $biblioItemID;
  $insertArray['patronID'] = $patronID;
  $insertArray['checkOutDate'] = date('Y-m-d');
  $insertArray['expectedCheckInDate'] = lms_circ_calculate_check_out_return_date($patronID);
  $insertArray['actualCheckInDate'] = NULL;
  DB::insert('Transaction', $insertArray);
  return array('success'=>true,'data'=>array('expectedCheckInDate'=>$insertArray['expectedCheckInDate']));
}

function lms_circ_check_in_item($data){
  $biblioItemID = $data['biblioitemid'];
  $biblioItem = lms_get_item_record($biblioItemID);
  $transaction = lms_get_uncheckedin_transaction_from_item_id($biblioItemID);
  if (is_null($biblioItem)) { return array('success'=>false,'errormsg'=>'No Item Record Found'); }
  if (is_null($transaction)) { return array('success'=>false,'errormsg'=>'Item Not Checked Out'); }
  $updateArray = [];
  $updateArray['actualCheckInDate'] = date('Y-m-d');
  DB::update('Transaction', $updateArray, 'biblioItemID = %i AND actualCheckInDate IS NULL', $biblioItemID);
  return array('success'=>true);
}

function lms_circ_renew_item($data){
  if (!isset($data['transactionid']) && lms_user_is_librarian()) {
    $biblioItemID = $data['biblioitemid'];
    $biblioItem = lms_get_item_record($biblioItemID);
    $transaction = lms_get_uncheckedin_transaction_from_item_id($biblioItemID);
  } else {
    $transaction = lms_get_transaction_record($data['transactionid']);
    $biblioItem  = lms_get_item_record($transaction['biblioItemID']);
    $biblioItemID = $biblioItem['biblioItemID'];
  }
  if (is_null($biblioItem)) { return array('success'=>false,'errormsg'=>'No Item Record Found'); }
  if (is_null($transaction)) { return array('success'=>false,'errormsg'=>'Item Not Checked Out'); }
  if (lms_is_transaction_late($transaction['transactionID']) == true) { return array('success'=>false,'errormsg'=>'Item Overdue'); }
  if (lms_is_transaction_renewal_limit_reached($transaction['transactionID']) == true) {
    return array('success'=>false,'errormsg'=>'Item Renewal Limit Reached');
  }
  $updateArray = [];
  $updateArray['expectedCheckInDate'] = lms_circ_calculate_check_out_return_date($transaction['patronID']);
  $updateArray['renewCount'] = $transaction['renewCount']+1 ;
  DB::update('Transaction', $updateArray, 'biblioItemID = %i AND actualCheckInDate IS NULL', $biblioItemID);
  return array('success'=>true,'data'=>array('expectedCheckInDate'=>$updateArray['expectedCheckInDate']));
}

function lms_circ_can_patron_check_out($patronID){
  if (lms_get_patron_available_quota($patronID) < 1) {
    return array('canCheckOut'=>false,'reason'=>'Patron Quota Exhausted');
  }
  return array('canCheckOut'=>true);
}

function lms_circ_can_item_be_checked_out($biblioItemID){
  if (lms_is_item_checked_out($biblioItemID) == true) {
    return array('canCheckOut'=>false,'reason'=>'Item Already Checked Out');
  }
  
  $biblio = lms_get_biblio_record_from_item_id($biblioItemID);
  if ($biblio['isRestricted'] == 1) {
    return array('canCheckOut'=>false,'reason'=>'Item Restricted');
  }
  return array('canCheckOut'=>true);
}

function lms_circ_can_item_be_checked_in($biblioItemID){
  if (lms_is_item_checked_out($biblioItemID) != true) {
    return array('canCheckIn'=>false,'reason'=>'Item Not Checked Out');
  }
  return array('canCheckIn'=>true);
}

function lms_circ_can_item_be_renewed($biblioItemID){
  
  if (lms_is_item_checked_out($biblioItemID) != true) {
    return array('canRenew'=>false,'reason'=>'Item Not Checked Out');
  }

  $transaction = lms_get_uncheckedin_transaction_from_item_id($biblioItemID);
  
  if (lms_is_transaction_late($transaction['transactionID']) == true) {
    return array('canRenew'=>false,'reason'=>'Item Overdue');
  }

  if (lms_is_transaction_renewal_limit_reached($transaction['transactionID']) == true) {
    return array('canRenew'=>false,'reason'=>'Item Renewal Limit Reached');
  }
  return array('canRenew'=>true);
}

function lms_is_transaction_late($transactionID){
  $transaction = lms_get_transaction_record($transactionID);
  return date('Y-m-d') > $transaction['expectedCheckInDate'];
}

function lms_get_transaction_renewal_limit($transactionID){
  $transaction = lms_get_transaction_record($transactionID);
  $patron = lms_get_patron_record($transaction['patronID']);
  $renewalLimit = lms_get_patron_category($patron['patronCategoryID'])['itemRenewLimit'];
  return array('renewCount' => $transaction['renewCount'] , 'availableRenewCount' => $renewalLimit);
}

function lms_is_transaction_renewal_limit_reached($transactionID){
  $q = lms_get_transaction_renewal_limit($transactionID);
  return $q['renewCount'] >= $q['availableRenewCount'];
}

function lms_circ_calculate_check_out_return_date($patronID, $date = null){
  $date = $date ?? date('Y-m-d');
  $patron = lms_get_patron_record($patronID);
  $patronCategory = lms_get_patron_category($patron['patronCategoryID']);
  return date('Y-m-d',strtotime($date.' + '.(+$patronCategory['itemCheckOutDays']).'days'));
}

function lms_get_transaction_record($transactionID){
  $q = DB::queryFirstRow('SELECT * FROM Transaction WHERE transactionID = %i', $transactionID);
  return $q;
}

function lms_circ_get_overdue_books($includeNonOverdue = false){
  $q = DB::query('SELECT * FROM Transaction t 
  LEFT JOIN Patron p ON t.patronID = p.patronID
  LEFT JOIN BiblioItem bi ON t.biblioItemID = bi.biblioItemID
  LEFT JOIN Biblio b ON bi.biblioID = b.biblioID
  WHERE t.actualCheckInDate IS NULL
  AND t.void = 0
  AND ((1=%i) OR (t.expectedCheckInDate < %s))
  ',+$includeNonOverdue,date('Y-m-d H:i:s'));
  array_walk( $q, function(&$a){
    unset($a['password']);
    $a['_patronBarcode'] = DB::queryFirstField('SELECT patronBarcode FROM PatronBarcode WHERE patronID = %i', $a['patronID']);
    $a['_biblioItemBarcode'] = DB::queryFirstField('SELECT biblioItemBarcode FROM BiblioItemBarcode WHERE biblioItemID = %i', $a['biblioItemID']);

  });
  return $q;
}

function lms_circ_get_most_checkedout_books($startdate=false, $enddate=false){
  $where = new WhereClause('and');
  if (!!$startdate && !!$enddate) {
    $startdate = date('Y-m-d', strtotime($startdate));
    $enddate   = date('Y-m-d', strtotime($enddate));
    $where->add('t.checkOutDate BETWEEN %s AND %s',$startdate, $enddate);
  }
  $q = DB::query('SELECT 
  bi.biblioID,
  b.biblioTitle, 
  b.biblioAuthor,
  b.biblioPublisher,
  b.biblioCallNumber,
  b.biblioISBN,
  COUNT(*) AS countTxn 
  FROM Transaction t
  LEFT JOIN BiblioItem bi ON t.biblioItemID = bi.biblioItemID
  LEFT JOIN Biblio b ON bi.biblioID = b.biblioID
  WHERE t.void = 0
  AND %l
  GROUP BY bi.biblioID
  ORDER BY countTxn DESC
  ', $where);
  return $q;
}

function lms_circ_get_most_checkouts_patron($startdate=false, $enddate=false){
  $where = new WhereClause('and');
  if (!!$startdate && !!$enddate) {
    $startdate = date('Y-m-d', strtotime($startdate));
    $enddate   = date('Y-m-d', strtotime($enddate));
    $where->add('t.checkOutDate BETWEEN %s AND %s',$startdate, $enddate);
  }
  $q = DB::query('SELECT 
  t.patronID,
  p.patronName,
  p.patronContact,
  pc.patronCategoryName,
  COUNT(*) AS countTxn 
  FROM Transaction t
  LEFT JOIN Patron p ON t.PatronID = p.PatronID
  LEFT JOIN PatronCategory pc ON p.PatronCategoryID = pc.PatronCategoryID
  WHERE t.void = 0
  AND %l
  GROUP BY t.PatronID
  ORDER BY countTxn DESC
  ', $where);
  array_walk( $q, function(&$a){
    unset($a['password']);
    $a['_patronBarcode'] = DB::queryFirstField('SELECT PatronBarcode FROM PatronBarcode WHERE patronID = %i', $a['patronID']);
  });
  return $q;
}

function lms_get_next_codabar_number($type){
  $libcode = lms_settings_get('library_code');
  if (!preg_match("/^\d{4}$/",$libcode)) { return false; }
  if ($type == 'patron') {
    $lastnumber = DB::queryFirstField('SELECT patronBarcode FROM PatronBarcode WHERE patronBarcode LIKE "2'.$libcode.'_________" ORDER BY patronBarcode DESC LIMIT 1');
    if ($lastnumber) {
      $seq = substr($lastnumber,5,8);
      if (!preg_match("/^\d{8}$/",$seq)) { return false; }
      $seq++;
    } else {
      $seq = 1;
    }
    
    $nextnumber = '2' . $libcode . str_pad(+$seq,8,'0',STR_PAD_LEFT);
    $nextnumber_withchecksum = $nextnumber . lms_luhn($nextnumber);
  }
  elseif ($type == 'biblioitem') {
    $lastnumber = DB::queryFirstField('SELECT biblioItemBarcode FROM BiblioItemBarcode WHERE biblioItemBarcode LIKE "3'.$libcode.'_________" ORDER BY biblioItemBarcode DESC LIMIT 1');
    if ($lastnumber) {
      $seq = substr($lastnumber,5,8);
      if (!preg_match("/^\d{8}$/",$seq)) { return false; }
      $seq++;
    } else {
      $seq = 1;
    }
    
    $nextnumber = '3' . $libcode . str_pad(+$seq,8,'0',STR_PAD_LEFT);
    $nextnumber_withchecksum = $nextnumber . lms_luhn($nextnumber);
  } else {return false;}
  return $nextnumber_withchecksum;
}

function lms_luhn($value) {
  if (!is_numeric($value)) {
    throw new \InvalidArgumentException(__FUNCTION__ . ' can only accept numeric values.');
  }
  $value = (string) $value;
  $length = strlen($value);
  $parity = $length % 2;
  $sum = 0;
  for ($i = $length - 1; $i >= 0; --$i) {
    $char = $value[$i];
    if ($i % 2 != $parity) {
      $char *= 2;
      if ($char > 9) {
        $char -= 9;
      }
    }
    $sum += $char;
  }
  return ($sum * 9) % 10;
}