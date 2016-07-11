<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');

include('./includes/top.php');


$file = join ('', file ('./report.html'));
if ($report==''){ 
$report='gensum';
}

$end=strpos($file, '</h1>')+4;

$head=substr($file, 0,$end);
$head=str_replace('<body>','<body bgcolor="ffffff"><table width=800><tr><td colspan=2>',$head);


$start=strpos($file, '<h2><a NAME="'.$report.'">');
$end=strpos($file, '<hr>',$start);
$file= substr($file, $start,$end-$start);

$start=strpos($file, '<p>(<b>Go To</b>');
$end=strpos($file, '<p>',$start+1);

$menu=substr($file, $start,$end-$start);
$file=substr($file, 0, $start).substr($file, $end);

$start=strpos($menu, 'Top');
$menu=substr($menu, $start+14);
$menu=substr($menu, 0, strlen($menu)-2);
$menu=str_replace(':','<br>',$menu);
$menu=str_replace('#','?report=',$menu);


echo $head;
echo '</td></tr><tr><td valign=top width=200>';
echo $menu;

echo '</td><td valign=top  width=600>';
echo $file;

echo '</td></tr></table>';
include('./includes/bottom.php');
page_close();
?>
