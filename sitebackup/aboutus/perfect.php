<?
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');

$db=new db;

include_once('../includes/top.php');


echo '<h4>Perfect Games</h4>';
echo '<p>Only Perfect Games that have been submitted and Sanctioned are listed on this Page.</p>
<p>If you have bowled a Perfect Game and feel it should be listed on this page, <br>please ensure that your local bowling association has submitted the appropriate paperwork to the office.</p><br>
<table><tr><td><table>';

$start_year=2004;

		$i=1;

		if (date('m')>4){

			$nyear=date('Y')+1;
		}else{
			$nyear=date('Y');
		}

		for ($y=$nyear; $y>=$start_year; $y--){

			if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
			echo sprintf('<td><a href="%s?nyear=%s">%s-%s</a></td>',$_SERVER['PHP_SELF'],$y,$y-1,substr($y,-2,2));
			}else{
			echo sprintf('<td>%s-%s</a></td>',$y-1,substr($y,-2,2));
			}
						$i++;
			if ($i==10){
				echo '</tr><tr>';
				$i=1;
			}
		}

		echo '</td></tr></table></td></tr>';


		if (isset($_GET['nyear'])){
			$nyear=$_GET['nyear'];
		}else{
			if (date('m')>6){

				$nyear=date('Y')+1;
			}else{
				$nyear=date('Y');
			}
		}

		$start_date=mktime(0,0,0,6,31,$nyear-1)-1;
		$end_date=mktime(0,0,0,7,3,$nyear)-1;

$db->query(sprintf('select * from perfect where date > "%s" and date < "%s" order by date;',$start_date,$end_date));

echo '<table width=600 cellspacing=0 cellpadding=2>';
echo '<tr class=dark><th width=110>Name</th><th width=110>Date</th><th width=135>Bowling Centre</th><th width=245>League/Event</th></tr>';
while($db->next_record()){
	if ($color=='light'){
		$color='dark';
	}else{
		$color='light';
	}
	echo sprintf('<tr class=%s></td><td width=110><nobr>%s</nobr></td><td><nobr>%s</nobr></td><td><nobr>%s</nobr></td><td width=240><nobr>%s</nobr></td></tr>',$color,$db->f('name'),date ("M jS, Y",$db->f('date')),$db->f('center'),$db->f('league'));
}

echo '</table>';

include_once('../includes/bottom.php');

?>
