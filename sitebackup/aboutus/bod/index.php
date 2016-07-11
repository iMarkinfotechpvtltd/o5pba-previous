<?
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include_once($htdocsdir.'includes/top.php');

echo '<table width=600>';
?>
<STYLE>
A.images:active{
	color: 000000;
	text-decoration:none;
}
A.images:link{
	color: 000000;
	text-decoration:none;
}
A.images:visited{
	color: 000000;
	text-decoration:none;
}
</style>


<h3>Board of Directors</h3>
<?



$db=new db();

$db->query(sprintf('select * from aboutus where class = "%s" order by id;',$_GET['class']));

$i=0;
while ($db->next_record()){
	if ($i==0){
		echo '<tr>';
	}
	echo sprintf('<td><center><font style="font-size:10pt; font-weight:bold;"><nobr>%s</nobr></font><br>',$db->f('position'));
	
	if (file_exists($htdocsdir.'aboutus/'.$_GET['class'].'/pictures/t'.$db->f('id').'.jpg')){
		echo sprintf('<a class="images" href="%s"><img src="%s" border=4></a><br>',$webpath.'aboutus/'.$_GET['class'].'/pictures/'.$db->f('id').'.jpg',$webpath.'aboutus/'.$_GET['class'].'/pictures/t'.$db->f('id').'.jpg');
	}else{
		echo '<br>';
	}
	echo sprintf('<font style="font-size:9pt; font-weight:bold;">%s</font><br>',$db->f('name'));
	if ($db->f('address')!=''){
		echo sprintf('%s<br>',$db->f('address'));
		echo sprintf('%s, %s<br>',$db->f('city'),$db->f('province'));
		echo sprintf('%s<br>',$db->f('postal'));
		echo sprintf('%s<br>',$db->f('phone'));

		
	}
	
	echo sprintf('%s</center></td>',$db->f('email'));
	
	if ($i==2){
		$i=0;
		echo sprintf('</tr><tr><td colspan = "%s"><br></td></tr>',($i+1));
	}else{
		$i++;
	}
}

echo '</tr></table>';

include_once($htdocsdir.'includes/bottom.php');

?>
