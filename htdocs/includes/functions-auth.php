<?php

function lms_user_is_librarian(){
  return ($_SESSION['LibrarianID']??0) > 0;
}

function lms_user_is_patron(){
  return ($_SESSION['PatronID']??0) > 0;
}

function lms_check_librarian_privilege($priv_keyword){
  if (!is_array($priv_keyword)) { $priv_keyword = [$priv_keyword]; }
  $priv = DB::queryFirstRow('SELECT * FROM LibrarianPrivilege lp 
                              INNER JOIN LibrarianPrivilegeType lpt 
                              ON lp.LibrarianPrivilegeTypeID = lpt.LibrarianPrivilegeTypeID
                              WHERE lp.LibrarianID = %i 
                              AND lpt.LibrarianPrivilegeTypeKeyword IN %ls'
                              , $_SESSION['LibrarianID'], $priv_keyword);
  if (!$priv) { 
    if(
      !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
      && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    )	{      
      http_response_code(401);
      die('access denied');
    } else {
      header('Location: ./?ad'); 
      die('access denied');
    }
  }
  return true;
}

function lms_check_auth($role){
  if (!is_array($role)) { $role = [$role]; }
  if (in_array('Librarian', $role) && lms_user_is_librarian()) { return true; }
  if (in_array('Patron', $role)    && lms_user_is_patron()) { return true; }
  if(
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
  )	{      
    http_response_code(401);
  } else {
    header('Location: ./login.php');
    die('not logged in');
  }
  return false;
}

function lms_check_csrf_token($userSuppliedToken=''){
  if (empty(lms_get_csrf_token())) {
    http_response_code(500);
    die('csrf token retrival failed');
  }
  $check = hash_equals(lms_get_csrf_token(), $userSuppliedToken);
  if (!$check) {
    http_response_code(401);
    die('csrf token validation failed');
  }
}

function lms_get_csrf_token(){
  return $_SESSION['csrf_token'];
}