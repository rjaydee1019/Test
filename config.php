<?php
declare(strict_types=1);
const DB_HOST='127.0.0.1'; const DB_NAME='hr_records'; const DB_USER='root'; const DB_PASS='';
if(session_status()!==PHP_SESSION_ACTIVE){ini_set('session.use_strict_mode','1');session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','samesite'=>'Lax']);session_start();}
mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);
try{$db=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);$db->set_charset('utf8mb4');}
catch(Throwable $e){http_response_code(500);exit('Database connection failed. Check config.php and import schema.sql.');}
require_once __DIR__.'/functions.php';