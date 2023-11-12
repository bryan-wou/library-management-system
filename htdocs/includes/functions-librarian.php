<?php

function lms_librarian_get_librarians(){
  $q = DB::query('SELECT * FROM Librarian');
  array_walk( $q, function(&$a){ unset($a['password']); });
  return $q;
}

function lms_librarian_get_librarian($librarianID){
  $q = DB::queryFirstRow('SELECT * FROM Librarian WHERE LibrarianID = %i', $librarianID);
  if ($q) {
    unset($q['password']);
    $q['_privileges'] = lms_librarian_get_librarian_privileges($librarianID);
  }
  return $q;
}

function lms_librarian_get_librarian_privileges($librarianID){
  $q = DB::queryFirstColumn('SELECT librarianPrivilegeTypeID FROM LibrarianPrivilege WHERE librarianID = %i', $librarianID);
  return $q;
}

function lms_librarian_set_librarian_privilege($librarianID, $librarianPrivilegeTypeID, $enable){
  if ($enable) {
    $q = DB::insertIgnore('LibrarianPrivilege',array('librarianID'=>$librarianID,'librarianPrivilegeTypeID'=>$librarianPrivilegeTypeID));
  } else {
    $q = DB::delete('LibrarianPrivilege', 'librarianID = %i AND librarianPrivilegeTypeID = %i', $librarianID, $librarianPrivilegeTypeID);
  }
}

function lms_librarian_get_librarian_privilege_types(){
  $q = DB::query('SELECT * FROM LibrarianPrivilegeType');
  return $q;
}

function lms_librarian_add_librarian($data){
  $insertArray = [];
  $insertArray['librarianID']       = +@$data['librarianid']?:NULL;
  $insertArray['librarianName']     = trim($data['name']);
  $insertArray['username']          = trim($data['username']);
  if ($insertArray['librarianID'] && mb_strlen($data['password'])<1) {
    //
  } else {
    $insertArray['password']        = (mb_strlen($data['password'])?password_hash($data['password'],PASSWORD_DEFAULT):null);
  }
  
  if (!mb_strlen($insertArray['librarianName'])) {
    return array('success'=>false,'errormsg'=>'Missing Name');
  }
  if (!mb_strlen($insertArray['username'])) {
    return array('success'=>false,'errormsg'=>'Missing Username');
  }
  if (!!@$insertArray['password'] && !mb_strlen($insertArray['password']) && is_null($insertArray['librarianID'])) {
    return array('success'=>false,'errormsg'=>'Missing Password');
  }
  if (is_null($insertArray['librarianID']) && !!DB::queryFirstField('SELECT username FROM Librarian WHERE username = %s', $insertArray['username'])) {
    return array('success'=>false,'errormsg'=>'Username Exists');
  }

  if (!is_null($insertArray['librarianID'])) {
    DB::delete('LibrarianPrivilege', 'librarianID = %i', $insertArray['librarianID']);
  }
 
  DB::insertUpdate('Librarian', $insertArray);

  $librarianID = DB::insertId() ?: +$data['librarianid'];

  $privilegeTypeIDs = $data['privilegetypeids'];
  $privilegeTypeInsertArray = [];

  foreach ($privilegeTypeIDs as $privilegeTypeID) {
    $privilegeTypeInsertArray[] = array(
      'LibrarianID' => $librarianID,
      'LibrarianPrivilegeTypeID' => $privilegeTypeID
      
    );
  }

  if (count($privilegeTypeInsertArray)) {
    DB::insert('LibrarianPrivilege', $privilegeTypeInsertArray);
  }

  return array('success'=>true,'data'=>array('librarianID'=>$librarianID));
  
}

function lms_librarian_delete_librarian($data) {

  $librarianID = $data['librarianid'];
  if ($librarianID == $_SESSION['LibrarianID']) {
    return array('success'=>false,'errormsg'=>'cannot delete account currently logged in');
  }

  $q = DB::delete('Librarian','librarianID = %i', $librarianID);
  return array('success'=>true);
  
}