<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include('../includes/top.php');


$max=85000;

		if (date('m')>4){
			
			$nyear=date('Y')+1;
		}else{
			$nyear=date('Y');
		}
		$start_year=2004;
		$i=1;
		for ($y=$nyear; $y>=$start_year; $y--){
			
			if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
			$links.= sprintf('<a href="%s?nyear=%s">%s-%s</a>&nbsp;&nbsp;&nbsp;',$_SERVER['PHP_SELF'],$y,$y-1,substr($y,-2,2));
			}else{
			$links.= sprintf('%s-%s</a>&nbsp;&nbsp;&nbsp;',$y-1,substr($y,-2,2));
			}
						$i++;
		}
		
				
		if (isset($_GET['nyear'])){
			$nyear=$_GET['nyear'];
		}else{
			if (date('m')>6){
				
				$nyear=date('Y')+1;
			}else{
				$nyear=date('Y');
			}
		}
?>


<table width= 100% border=0>
<tr><th colspan=2>Kids Help Phone Fundraising Campaign</th></tr>
<tr><td colspan=2><?echo $links;?>
		</td></tr>
<tr><td width=320>
<?
if ($function==''){
?>
<img src="/images/spacer.gif" width=1 height=270><br>


</td><td>

<table border=0 style='font-size:8pt;font-family: "Arial, Helvetica, sans-serif";' cellpadding=0 cellspacing=0>
<tr><td colspan=3><font size="2">Contributions to date:</font></td></tr>
<table>
<?
$db=new DB;

		
		$start_date=mktime(0,0,0,6,31,$nyear-1)-1;
		$end_date=mktime(0,0,0,7,3,$nyear)-1;


$db->query(sprintf('select * from khp where date > "%s" and date < "%s" order by date;',$start_date,$end_date));
echo '<tr><th width=210>Name</th><th width=110>Date</th><th width=135>Amount</th></tr>';
$total=0;
while($db->next_record()){
	if ($color=='light'){
		$color='dark';
	}else{
		$color='light';
	}
	echo sprintf('<tr class=%s><td width=110><nobr>%s</nobr></td><td><nobr>%s</nobr></td><td style="text-align:right;"><nobr>%01.2f</nobr></td></tr>',$color,$db->f('name'),date ("M jS, Y",$db->f('date')),$db->f('amount'));
	$total=$total+$db->f('amount');
}
echo sprintf('<tr class=%s><td width=110></td><td style="text-align:right;">Total:</td><td style="text-align:right;">%01.2f</td></tr>',$color ,$total);


if ($nyear > 2009)  {
	$max = 42500;
} else {
	$max = 85000;
}

$percent=$total/$max;

$full=(200*$percent)+10;
$position=200-$full +170;

?>

<style>


#scale{
        position:absolute;
        left:165px;
        top:<?php echo $position;?>px;
}
#temp{
        position:absolute;
        left:165px;
        top:170px;
        width:310;
}
</style>


</table>
</td></tr>
<tr><td colspan=2>

<?
}
$t=new viewtournaments('khpevents');
$t->postpath='kidshelpphone/posts/';
$t->formatfile='khp.ihtml';	

switch ($function){
	
	case "show":
		$t->view_media($key, $file);
		exit;
	break;
	
	case "photo":
	$t->view_folder($key, $id);
	exit;
	break;
	
	case "tourn_view":	
	if (isset($id)){
		$t->view($id);
		
	}elseif(isset($expand)){
		$t->view($expand);
	}else {
		$t->listing(1);
	}
		exit;
	break;
	
	default:
	$t->listing(1);
	break;
}
?>





</td><td></td></tr>
</table>

<div id='scale'>
<img src='./images/scale.gif' height=<?php echo $full;?> width=310>
</div>

<div id='temp'>
<img src='./images/pigbank2.gif'><br>
<?php
	if ($nyear > 2009) 
	 	echo ("<center>Help us exceed our goal of $40,000</center>"); 
	else 
		echo ("<center>Help us exceed our goal of $80,000</center>"); 
?>

<!--<center>Help us exceed our goal of $80,000</center>-->

</div>

<?php
include('../includes/bottom.php');
?>
