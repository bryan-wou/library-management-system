<?php

session_name('lms');
session_start();

require_once __DIR__.'/../user/config.php';

require_once __DIR__.'/functions-auth.php';
require_once __DIR__.'/functions-db.php';
require_once __DIR__.'/functions-layout.php';
require_once __DIR__.'/functions-i18n.php';


require_once __DIR__.'/functions-biblio.php';
require_once __DIR__.'/functions-patron.php';
require_once __DIR__.'/functions-circ.php';
require_once __DIR__.'/functions-librarian.php';


require_once __DIR__.'/functions-settings.php';



//csrf
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = base64_encode(random_bytes(32));
}
