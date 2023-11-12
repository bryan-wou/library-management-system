<?php

require_once __DIR__ . '/../plugins/meekrodb-2.4/db.class.php';

DB::$user       = LMS_DB_USER;
DB::$password   = LMS_DB_PASS;
DB::$host       = LMS_DB_HOST;
DB::$dbName     = LMS_DB_NAME;
DB::$encoding   = 'utf8mb4';