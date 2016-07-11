<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
	include('../includes/top.php');
?>
<table class="w100" cellspacing="0" cellpadding="2">
<th>What's New</th>
</table>

<?
echo '<table class="w100" border=0 cellpadding="2" cellspacing="0" style="background-color: #ad201f; color: #bcbcbc;"><tr>';

$start_year=2003;

$i=1;
$nyear=date('Y');
if (isset($_GET['nyear'])){
	$year=$_GET['nyear'];
}else{
	$year=$nyear;
}
echo '<td>Year: </td>';
for ($y=$nyear; $y>=$start_year; $y--){
	
	if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
		echo sprintf('<td><a class="bright_links" href="%s?nyear=%s">%s</a></td>',$_SERVER['PHP_SELF'],$y,$y);
	}else{
		echo sprintf('<td style="color: #000000;">%s</a></td>',$y);
	}
				$i++;
	/*
	if ($i==10){
		echo '</tr><tr>';
		$i=1;
	}
	*/
}

echo '</tr></table><table class="w100" cellspacing="0" cellpadding="2">';


$echo=false;

$lines = file ($htdocsdir.'whatsnew/newest.php');
foreach ($lines as $id=>$line){
	if (strpos($line,', '.$year.'</th></tr>')){
		$echo=true;
	}
	if (strpos($line,', '.($year-1).'</th></tr>')){
		$echo=false;
	}
	
	if ($echo){
		echo $line;	
	}
}
$lines = file ($htdocsdir.'whatsnew/older.php');
foreach ($lines as $id=>$line){
	if (strpos($line,', '.$year.'</th></tr>')){
		$echo=true;
	}
	if (strpos($line,', '.($year-1).'</th></tr>')){
		$echo=false;
	}
	
	if ($echo){
		echo $line;	
	}
}

echo '</table>';
include('../includes/bottom.php');
?>
