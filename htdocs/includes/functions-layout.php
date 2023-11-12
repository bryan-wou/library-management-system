<?php

function lms_layout_header($class=''){
  global $lms_layout_title;
  require __DIR__.'/../layout/'.$class.'-header.php';
}
function lms_layout_scripts($class=''){
  require __DIR__.'/../layout/'.$class.'-scripts.php';
}
function lms_layout_footer($class=''){
  require __DIR__.'/../layout/'.$class.'-footer.php';
}