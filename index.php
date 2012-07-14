<?php
session_start();
require_once("config.php");
if($site_settings['show_php_errors']){
  error_reporting(E_ALL);
  ini_set("display_errors",1);
}else{
  error_reporting(0);
  ini_set("display_errors",0);
}
if($site_settings['auth']){
  if (!isset($_SERVER['PHP_AUTH_USER']) &&
          (@$_SESSION['user_auth']!=$site_settings['auth_username'] ||
          @$_SESSION['user_password']!=$site_settings['auth_password'])) {
    header('WWW-Authenticate: Basic realm="'.$site_settings['realm'].'"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You must login to view';
    exit();
}else{
  $_SESSION['user_auth']      = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
  $_SESSION['user_password']  = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
  }
}
$slash =(strpos(dirname(__FILE__),"\\")!==false)? "\\" : "/";
define("SITEROOT", dirname(__FILE__),true);
require_once($site_settings['PathTosketch'] .
        'sketch-system'.$slash.
        'core'.$slash.
        'sketch'.$slash.'sketch.php');                                  // Load sketch
$sketch = new sketch();
$sketch->start();