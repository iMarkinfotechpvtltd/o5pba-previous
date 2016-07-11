<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');



//$update=new update;
//$page=$_SERVER['PHP_SELF'];
$page=$function;

include('../includes/top.php');
$ts = new topicsystem('forms','Forms');

//Helpers
function display_details($topic){
	echo $topic->title;
	echo '<ul>';
	$items = $topic->items;
	foreach($items as $title=>$data){
		echo '<li>'.linkit('posts/'.$topic->created.'/'.$data[0],$title).'</li>'."\n";
	}
	echo '</ul>';
}
//Page Function
switch($function){
	/*
	* displays the news posts with the option to expand.
	*/
	default:
	$ts->iterate();
        ?>
        <h4><? echo $ts->system_name; ?></h4>
       <table border="0" cellpadding="2" cellspacing="0" width="500">
        <?
        	echo '<tr><td colspan="4" class="header_dates"><table width="100%" cellpadding="2" cellspacing="0" border=0><tr>';
			
			$start_year=2004;
			
			$i=1;
			
			if (date('m')>4){
				
				$end_year=date('Y')+1;
			}else{
				$end_year=date('Y');
			}
			
			if (isset($_GET['nyear'])){
				$nyear=$_GET['nyear'];
			}else{
				if (isset($_GET['expand']) && intval($_GET['expand'])>0){
					if (date('m',$_GET['expand'])>6){
						
						$nyear=date('Y',$_GET['expand'])+1;
					}else{
						$nyear=date('Y',$_GET['expand']);
					}
					
				}else{
					if (date('m')>6){
						
						$nyear=date('Y')+1;
					}else{
						$nyear=date('Y');
					}
				}
			}
			echo '<td>Season: </td>';
			for ($y=$end_year; $y>=$start_year; $y--){
				
				if ($nyear!=$y && (($nyear=='' && $nyear!=$y) || ($nyear!=''))){
				echo sprintf('<td><a class="bright_links" href="%s?nyear=%s">%s-%s</a></td>',$_SERVER['PHP_SELF'],$y,$y-1,substr($y,-2,2));
				}else{
				echo sprintf('<td style="color: #000000;">%s-%s</a></td>',$y-1,substr($y,-2,2));
				}
							$i++;
				/*
				if ($i==10){
					echo '</tr><tr>';
					$i=1;
				}
				*/
			}
			
			echo '</tr></table></td></tr>';
        
			$start_date=mktime(0,0,0,6,31,$nyear-1)-1;
			$end_date=mktime(0,0,0,7,3,$nyear)-1;
        ?>
        <tr class=dark><th width="100">Created</th><th width="100">Modified</th><th width="300">Topic</th></tr>
        <?
        while($ts->next_topic()){
        	if ($ts->f('created')>$start_date && $ts->f('created')<$end_date){
	        	if(isset($expand)){
	        		$color='light';
	        	}else{
	        		if ($color=='light'){
	        			$color='dark';
	        		}else{
	        			$color='light';
	        		}
	        	}
	        	
	        	if($expand==$ts->f('created')){
	        		echo '<tr class=dark><td>'.date('M d, Y',$ts->f('created')).'</td><td>'.date('M d, Y',$ts->f('modified')).'</td><td>';
	        		display_details($ts->current_topic);
	        	}else{
	        		echo '<tr class="'.$color.'"><td>'.date('M d, Y',$ts->f('created')).'</td><td>'.date('M d, Y',$ts->f('modified')).'</td><td>';
	        		echo '<a href="'.$_SERVER['PHP_SELF'].'?function='.$function.'&expand='.$ts->f('created').'&nyear='.$nyear.'">'.$ts->f('title').'</a>';
	        	}
	        	echo '</td><tr>';
        	}
         }
        ?>
        </table>
        <?
        break;
}

include('../includes/bottom.php');
page_close();
?>
