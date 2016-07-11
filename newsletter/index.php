<?
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');


include('../includes/top.php');

?>
<font size=3>The following files are all posted in Adobe Acrobat format.  To view you will require Adobe Acrobat 
Reader available for free from <a href="http://www.adobe.com">Adobe's Website</a>.  On MOST computers 
clicking on the file images below will open the file inside your browser.  To download the printable files, 
you may need to "right click" (option click on a mac) on the link images and say "Save Target As". <br><Br>
<a href="subscribe.php">Click here to Subscribe/Unsubscribe to automatic notification of a new newsletter posting.</a> 
<br></font><center>
<br>
<br><br><table width="600">
<tr><td colspan="3" class="header_dates"><table width="600"><tr>
<?
$start_year=2003;
global $webfolder;

if ((date('m')+2)>7){
	$end_year=date('Y')+1;
}else{
	$end_year=date('Y');
}


$i=1;
echo '<td>Season: </td>';
for ($y= $end_year; $y>=$start_year; $y--){
	if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
		echo sprintf('<td><a class="bright_links" href="index.php?nyear=%s">%s-%02s</a></td>',$y,$y-1,$y-2000);
	}else{
		echo sprintf('<td style="color: #000000;">%s-%s</a></td>',$y-1,$y-2000);
	}
	
	$i++;
	/*
	if ($i==10){
		echo '</tr><tr>';
		$i=1;
	}
	*/
}

echo '</tr></table></td></tr><tr><th width="200">Month</th><th width="100">View</th></tr>';


$nyear=$_GET['nyear'];
if (!$nyear){
	if ((date('m'))>7){
		$nyear=date('Y')+1;
	}else{
		$nyear=date('Y');
	}
}
for ($month=-4; $month<=7; $month++){
	echo sprintf('<tr><td>%s</td><td>',date('F Y',mktime(0,0,0,$month,1,$nyear)));
	
	
	if (file_exists(sprintf('%s-%02s.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))){
		echo sprintf('<a href="%s-%02s.pdf"><img border="0" src="/images/acrobat.gif"></a> <i>%d K</i></td></tr>',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear)),filesize(sprintf('%s-%02s.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))/1024);
	}else{
		echo 'N/A</td></tr>';
	}
	
	
	if (file_exists(sprintf('%s-%02s-1.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))){
		echo sprintf('<tr><td>%s</td><td>',date('F Y',mktime(0,0,0,$month,1,$nyear)));
		echo sprintf('<a href="%s-%02s-1.pdf"><img border="0" src="/images/acrobat.gif"></a> <i>%d K</i></td></tr>',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear)),filesize(sprintf('%s-%02s.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))/1024);
	}
	if (file_exists(sprintf('%s-%02s-2.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))){
		echo sprintf('<tr><td>%s</td><td>',date('F Y',mktime(0,0,0,$month,1,$nyear)));
		echo sprintf('<a href="%s-%02s-2.pdf"><img border="0" src="/images/acrobat.gif"></a> <i>%d K</i></td></tr>',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear)),filesize(sprintf('%s-%02s.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))/1024);
	}
	if (file_exists(sprintf('%s-%02s-3.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))){
		echo sprintf('<tr><td>%s</td><td>',date('F Y',mktime(0,0,0,$month,1,$nyear)));
		echo sprintf('<a href="%s-%02s-3.pdf"><img border="0" src="/images/acrobat.gif"></a> <i>%d K</i></td></tr>',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear)),filesize(sprintf('%s-%02s.pdf',date('Y',mktime(0,0,0,$month,1,$nyear)),date('m',mktime(0,0,0,$month,1,$nyear))))/1024);
	}
}

?>
</table>
</center>

<?

include('../includes/bottom.php');

?>
