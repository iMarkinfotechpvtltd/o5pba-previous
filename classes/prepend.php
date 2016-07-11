<?php
error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL);
//ini_set("display_errors", "on");
//ini_set("display_startup_errors", "on");
$absdir = $_SERVER["DOCUMENT_ROOT"].'/';
$htdocsdir = $absdir;
$classdir = $absdir.'classes/';
$webpath = 'http://www.o5pba.ca/';
require_once($classdir.'phplib/prepend.php3');
require_once($classdir.'phplib/oohforms.inc');
require_once('jform.inc');
require_once('local.php');
require_once('globals.php');
require_once('menu.php');
require_once('classes/user.php');
require_once('modules/cal.php');
require_once('modules/topicsystem.php');
require_once('modules/tourn.php');
require_once('modules/tournmini.php');
require_once('modules/perfect.php');
require_once('modules/upload.php');
require_once('modules/photo.php');

?>
