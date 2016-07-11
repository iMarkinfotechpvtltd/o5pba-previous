<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');



//$update=new update;
//$page=$_SERVER['PHP_SELF'];
$page=$function;

include('../includes/top.php');
$ts = new topicsystem('bowling_school','Bowling School');
$ts2 = new viewtournmini('bowling_school_mini','Bowling School Entries');
$db = new DB;

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
        
       	<table border="0" cellpadding="2" cellspacing="0" class="w100">
       		<tr><td>
       	<span class=med>The Bowling School is operated by the Ontario 5 Pin Bowlers' Association, in collaboration with the Youth Bowling Council and the Master Bowlers Association.<br>
				
By bringing the expertise of these organizations together in such an endeavour, the school hopes to promote the sport of five pin bowling and its combined play by all age groups.&nbsp; We hope to foster an atmosphere of camaraderie, sportsmanship and excellence through the best &quot;forum&quot; of learning available.<br>

This learning will hopefully take place, not only through the instructor/student relationship, but also with all individuals involved.<br>

The bowling school consists of 96 students from all across Ontario.&nbsp; An effort will be made to include a representation of province-wide individuals in order to maintain a mix of variety and interest.<br>

The students are divided into three major &quot;classes&quot; then into groups within that class.&nbsp;&nbsp; This will allow for 
a very low student to instructor ratio.&nbsp;&nbsp;  The three classes are arranged by the students skill level following the Long Term Athlete Development model and Canadian Sport for Life program.<br></span>	
      		
       	</td></tr></table>
       <table border="0" cellpadding="2" cellspacing="0" class="w100">
       	
        <?
        	echo '<tr><td colspan="4" class="header_dates"><table width="100%"><tr>';
			
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
				echo sprintf('<td class="header_inactive">%s-%s</a></td>',$y-1,substr($y,-2,2));
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
        <tr class=dark><th class="w20">Modified</th><th class="w80">Topic</th></tr>
        <?
        while($ts->next_topic()){
        	if ($ts->f('created')>$start_date && $ts->f('created')<$end_date){

	        		if ($color=='light'){
	        			$color='dark';
	        		}else{
	        			$color='light';
	        		}
	        	
	        	
	        	if($expand==$ts->f('created')){
	        		echo '<tr class='.$color.'><td>'.date('M d, Y',$ts->f('modified')).'</td><td>';
	        		display_details($ts->current_topic);
	        	}else{
	        		echo '<tr class="'.$color.'"><td>'.date('M d, Y',$ts->f('modified')).'</td><td>';
	        		echo '<a href="'.$_SERVER['PHP_SELF'].'?function='.$function.'&expand='.$ts->f('created').'&nyear='.$nyear.'">'.$ts->f('title').'</a>';
	        	}
	        	echo '</td></tr>';
	        	echo "\n\n";
        	}
         }
         $color = $ts2->listing(0, $color);
         if ($color=='light'){
	        			$color='dark';
	        		}else{
	        			$color='light';
	        		}
         $sql = 'SELECT * FROM `aboutus` WHERE class=\'bowl\' order by `modified` ASC LIMIT 1';
         $db->query($sql);
         $db->next_record();
         echo '<tr class="'.$color.'"><td>'.date('M d, Y',$db->f('modified')).'</td><td><a href="/bowling_school/staff.php?class=bowl">Staff / Instructors / Pros</a></td></tr>';
        ?>
        <tr>
        	<td colspan=2>
        		<br><br>
        		<center>
        			<b>In Partnership with:</b><br><br>
        			<a href="http://www.bpao.ca/" target="_blank"><img border=0 src="bpao.gif"></a>
        				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        			<a href="http://www.masterbowlers.com/" target="_blank"><img border=0 src="mba.gif"><a/>
        		</center>
        	</td>
        </table>
        <?
        break;
}

include('../includes/bottom.php');
page_close();
?>
