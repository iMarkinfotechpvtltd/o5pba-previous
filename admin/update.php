<?php

require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');

page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
                                                     // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0

$page=$function;
$perm->check('news');


include_once('./includes/menuitems.php');
include('./includes/top.php');



		echo "Last Update Performed :<b>";
		$date = getdate(filemtime("../newest.php"));
		echo $date['month'].' '.$date['mday'].','.$date['year']."</b><br>";

		$u = new update;
		$u->dowhatsnew();
		echo 'Created "Whats New" index.<br>';

include('./includes/bottom.php');
page_close();
?>
