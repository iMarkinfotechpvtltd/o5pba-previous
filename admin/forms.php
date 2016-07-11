<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
$perm->check('forms');
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
	// $function = $_GET['function'];
	// $time = $_GET['time'];
	// $nyear = $_GET['nyear'];
	if(isset($_GET['function']))
	{
	$function = $_GET['function'];
	}
	if(isset($_GET['time']))
	{
	$time = $_GET['time'];
	}
	if(isset($_GET['nyear']))
	{
	$nyear = $_GET['nyear'];
	}
	if(isset($_POST['function']))
	{
	$function = $_POST['function'];
	}
	if(isset($_POST['submit']))
	{
	$submit = $_POST['submit'];
	}
	if(isset($_POST['time']))
	{
	$time = $_POST['time'];
	}
	if(isset($_POST['numitems']))
	{
	$numitems = $_POST['numitems'];
	}
include_once('./includes/menuitems.php');
include('./includes/top.php');
$ts = new topicsystem('forms','Forms');


//Form Elements
$f = new jform;
$f->add_element(array("type"=>"submit","name"=>"next","value"=>"Next"));
$f->add_element(array("type"=>"submit","name"=>"cancel","value"=>"Cancel","extrahtml"=>'onClick="document.location = \'?function=display\';return false;"'));
$f->add_element(array("type"=>"text","name"=>"title","size"=>"70", "minlength"=>"1","length_e"=>"Please enter the title for this post."));
function makeItems($numitems){        		
	global $f,$function;
	for($i = 0; $i < $numitems;$i++){
    	$f->add_element(array("type"=>"text","name"=>"item_title_$i","size"=>"35","minlength"=>"1","length_e"=>"Please enter the title for this item."));
        $f->add_element(array("type"=>"file","name"=>"item_file_$i"));
		$f->add_element(array("type"=>'text','name'=>"item_url_$i"));
        if($function=='edit'){
        	$f->add_element(array('type'=>'checkbox','name'=>"item_delete_$i"));
        }
    }
}
$f->add_element(array("type"=>"submit","name"=>"submit","value"=>"Add","extrahtml"=>"onClick=\"return checkAddForm();\""));
$f->add_element(array("type"=>"text","name"=>"ask_numitems","size"=>3,"maxlength"=>"3","valid_regex"=>"^[1-9]{1}[0-9]{0,1}$","valid_e"=>"Must be in range 1-99"));
$f->add_element(array("type"=>'hidden',"name"=>'time','value'=>$time));
$f->add_element(array("type"=>'submit','name'=>'additem','value'=>'Add Item','extrahtml'=>"onClick=\"document.forms[0].numitems.value++;return true;\""));

//Helpers
function display_details($topic){
	echo $topic->title;
	echo '<br><br>Created by: '.$topic->createdby;
	echo '<br>Created: '.date('F d, Y',$topic->created);
	echo '<br>Last modified by: '.$topic->modifiedby;
	//echo '<br>Last modified: '.date('F d, Y',$topic->modified);
    echo '<ul>';
    $items = $topic->items;
	foreach($items as $title=>$data){
    	echo '<li>'.$title.'(<a href="/forms/posts/'.$topic->created.'/'.str_replace("+", "%20", urlencode($data[0])).'">'.$data[0].'</a>)</li>'."\n";
    }
    echo '</ul>';
}
//Draw Forms
function ask_numitems(){
	global $f,$ts,$function,$time;
	?>
	<h4>Add <? echo $ts->system_name; ?> Topic - Step 1 of 2</h4>
	<?
	$f->load_defaults_with_vals(array("submit"=>"Next"));
	$f->start('','', $_SERVER['PHP_SELF'].'?function='.$function);
	?>
	<table width="500" cellpadding="3" cellspacing="0" border="0" bgcolor="#AFAFAF">
		<tr bgcolor="#AFAFAF">
	    <td>How many items to add to this post? <? $f->show_element('ask_numitems');?></td>
	    </tr>
	    <tr bgcolor="#CFCFCF">
	    	<td><center><? $f->show_element('cancel');$f->show_element('submit');?></center></td>
	    </tr>
	</table>
	<?
	$f->finish();	
}
function add_items($numitems,$defaults=''){
	global $ts,$f,$function,$realitems;
	$f->add_element(array("type"=>'hidden',"name"=>'numitems','value'=>$numitems));
	//Checks if files have been selected.
	if($function == 'addtopic'){
		$colspan=2;
		makeItems($numitems);
		?>
		<h4>Add <? echo $ts->system_name; ?> Topic 2 of 2</h4>
		<script language="Javascript">
	            
	            function checkAddForm(){
	
	                    <?
	                    for($i=0;$i<$numitems;$i++){
	                        echo 'if(document.forms[0].item_file_'.$i.'.value==""){'."\n".' alert("You must submit a file for each topic.");'."\n".' return false;'."\n}";
	                    }
	                    ?>
						
					return true;
	            }
	            
	            </script>
		<?
	}elseif($function=='edit'){
		$colspan=3;
		?>
		<script language="Javascript">
	            
	            function checkAddForm(){
	
	                    <?
	                    for($i=$realitems;$i<$numitems;$i++){
	                        echo 'if(document.forms[0].item_file_'.$i.'.value==""){'."\n".' alert("You must submit a file for an added topic.");'."\n".' return false;'."\n}";
	                    }
	                    ?>
	
	                return true;
	            }
	            
	            </script>
	  <table width="500">
	  	<tr>
	  		<td>
	  			<center>
	  			<? echo getSeason('forms', $_GET['time']);  ?> Season
	  			</center>
	  		</td>
	  	</tr>
	  </table>
		<h4>Edit <? echo $ts->system_name; ?></h4>	
		<?
	}
	$f->start('','', $_SERVER['PHP_SELF'].'?function='.$function);
	?>
	<table width="500" cellpadding="3" cellspacing="0" border="0" bgcolor="#AFAFAF">
	<tr bgcolor="#CFCFCF">
		<td align="center" colspan="<? echo $colspan; ?>" style="font:bold;">Title</td>
	</tr>
	<tr>
	    <td align="center" colspan="<? echo $colspan; ?>"><? $f->show_element('title'); ?></td>
	</tr>
	<tr>
		<td colspan="<? echo $colspan; ?>" style="font:bold;" align="left">Items</td>
	</tr>
	<tr bgcolor="#CFCFCF">
		<td width="*" style="font:bold;">Title</td>
	    <td width="200" style="font:bold;">Document</td>
	    <?
	    if($function=='edit'){
	    	?>
			<td width="20" style="font:bold;">Delete</td>
	    	<?    	
	    }
		?>
	    
	</tr>
	<tr>
		<?
	for($i=0;$i<$numitems;$i++){
	 	?>
	    <tr>
	    	<td width="*" align="center"><? $f->show_element("item_title_$i"); ?></td>
	        <td width="200" align="left"><? $f->show_element("item_file_$i"); 
	         echo '<br>Currently: '.$defaults["item_file_$i"];?></td>
	        <?
	        if($function=='edit'){
	        	?>
	        	<td width="20" align="center"><? $f->show_element("item_delete_$i"); ?></td>
	        	<?	
	        }
	        ?>
	    </tr>
	    <?
	}
	?>
	<tr bgcolor="#CFCFCF">
		<td colspan="2"><center>
		<?  
		$f->show_element("submit");
		if($function == 'edit')	$f->show_element('additem');
		$f->show_element('cancel');
		?></center></td>
	
	</tr>
	</table>
	
	<?
	$f->finish();	
}
//Page Function
switch($function){
	/* 
	 * displays the news posts with the option to expand.
	 */
	default:
		$ts->iterate();
        ?>
        <h4>Updating <? echo $ts->system_name; ?></h4>
        <table border="0" cellpadding="2" cellspacing="0" width="500">
        <?
       	echo '<tr><td colspan="4"><table width="100%"><tr>';
			
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
			
			for ($y=$end_year; $y>=$start_year; $y--){
				
				if ($nyear!=$y && (($nyear=='' && $nyear!=$y) || ($nyear!=''))){
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
			
			echo '</tr></table></td></tr>';
        
			$start_date=mktime(0,0,0,6,31,$nyear-1)-1;
			$end_date=mktime(0,0,0,7,3,$nyear)-1;
        ?>
        <tr><td width=100>&nbsp;</td><td width="100">Time</td><td width="300">Topic</td></tr>
        <?
        while($ts->next_topic()){
        	if ($ts->f('created')>$start_date && $ts->f('created')<$end_date){	
            	echo '<tr><td width="50" align="center" style="text-align:center;vertical-align:middle;"><a href="'. $_SERVER['PHP_SELF'].'?function=edit&time='.$ts->f('created').'&nyear='.$nyear.'">Edit</a></td><td>'.date('M d, Y',$ts->f('created')).'</td><td>';
                if($expand==$ts->f('created')){
					display_details($ts->current_topic);
            	}else{
					echo '<a href="'. $_SERVER['PHP_SELF'].'?function='.$function.'&expand='.$ts->f('created').'&nyear='.$nyear.'">'.$ts->f('title').'</a>';
        	    }
    	        echo '</td><td width="50" align="center" style="text-align:center;vertical-align:middle;">';
	            echo "<a href=\"javascript:confirmDelete('".$ts->f('title')."','". $_SERVER['PHP_SELF']."?function=delete&time=".$ts->f('created').'&nyear='.$nyear."')\">Delete</a></td><tr>";
        	}
		}
        ?>
        <tr><td colspan="4"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?function=asknumitems">Add Topic</a></td></tr>
        </table>


        <?
	break;
	/*
	 *	Add's Topic to the news system.
	 */
	case 'asknumitems':
		if(isset($_POST['submit'])){	
			//Checking Step 1
			// $test = $f->validate('ok',array('ask_numitems'));
			// if($test=='ok'){
				$function = 'addtopic';
				add_items($_POST['ask_numitems']);	
			// }else{
				// $f->load_defaults(array('numitems'));
				// $f->check();
				// ask_numitems();
			// }
		}else{
			ask_numitems();	
		}
	break;
	case 'addtopic':
	if(isset($_POST['ask_numitems']))
	{
		echo $numitems = $_POST['ask_numitems'];
	}
		if((isset($_POST['submit']))&&($_POST['numitems']!='0')){
			makeItems($numitems);
			//Build the load default array on number of items
			$fields = array('title');
			for($i=0;$i<$numitems;$i++){
				array_push($fields,"item_title_$i");
			}
			//Check Add Form, if true, process, otherwise, print form again.
			// $test = $f->validate('ok',$fields);
			// if($test=='ok'){
				
				$filetest = true;
				$topic = new topic($ts->t_name);
				$topic->title = $_POST['title'];
				$topic->created = time();
				//echo $topic->created;
				for($i=0;$i<$numitems;$i++){
					if(!($topic->addItem("item_title_$i","item_file_$i"))){
						echo "File error on item <b>".$_POST["item_title_$i"].'</b>';
						$filetest = false;
						break;
					}
				}
				if($filetest){
					if($ts->addTopic($topic)){
						echo "<h4>Success</h4>";
						$topic->loadFromTimestamp($topic->created);
						display_details($topic);
						echo '<p align="left"><a href="'. $_SERVER['PHP_SELF'].'">Return</a></>';
					}
				}else{
					$f->load_defaults($fields);
					$f->check();
					add_items($_POST['numitems']);
				}
			// }else{
				// $f->load_defaults($fields);
				// $f->check();
				// add_items($_POST['numitems']);
			// }
		}else{
			if(isset($_POST['numitems'])){
				add_items($_POST['numitems']);	
			}else{
				add_items(1);
			}
		}
	break;
	case 'delete':
		if((isset($time))&&($time!="")){
			$topic = new topic($ts->t_name);
			$topic->loadFromTimestamp($time);
			if($topic->valid){
				echo "<H5> Successfully Removed </H5>";
				display_details($topic);	
				$ts->deleteTopic($topic->created);
				echo '<p align="left"><a href="'. $_SERVER['PHP_SELF'].'">Return</a></>';
				}
		}else{
			echo "<h4 style=\"color:#FF0000\">Incomplete Function Call to Delete</h4>";	
			echo '<p align="left"><a href="'. $_SERVER['PHP_SELF'].'">Return</a></>';

		}
	break;
	case 'edit':
		if($_POST['additem']!=="")
		{
			if(isset($_POST['time']))
			{
				$time = $_POST['time'];
				$numitems = $_POST['numitems'];
			}
		}
		if((isset($time))&&($time!="")){
			if(isset($_POST['submit'])){
				makeItems($numitems);
				//Build the load default array on number of items
				$fields = array('title');
				for($i=0;$i<$numitems;$i++){
					array_push($fields,"item_title_$i");
				}
				//Check Add Form, if true, process, otherwise, print form again.
				// $test = $f->validate('ok',$fields);			
				// if($test=='ok'){
					$topic = new topic($ts->t_name);
					$topic->loadFromTimestamp($time);
					if($topic->valid){
						$topic->title = $_POST['title'];
						$filetest=true;
						for($i=0;$i<$numitems;$i++){
							if(isset($_POST["item_delete_$i"])){
								if($topic->deleteItem($i)){
									echo "Deleted Item ".($i+1)."<br>";	
								}
							}elseif(!$topic->updateItem($i,"item_title_$i","item_file_$i")){
								echo "File error on item <b>".$_POST["item_title_$i"].'</b>';
								$filetest = false;
								break;	
							}
						}	
						if($filetest){
							if($ts->updateTopic($topic)){
								echo "<h4>Successfully Updated</h4>";
								$topic2 = new topic($ts->t_name);
								$topic2->loadFromTimestamp($topic->created);
								display_details($topic2);
								echo '<p align="left"><a href="'. $_SERVER['PHP_SELF'].'">Return</a></>';

							}else{
								echo "Error updating <b>".$_POST['title'].'</b>';
								echo '<p align="left"><a href="'. $_SERVER['PHP_SELF'].'">Return</a></>';

							}	
						}
					}	
				// }else{
					// $f->load_defaults($fields);
					// $f->check();
					// add_items($_POST['numitems']);	
				// }
			}else{
				$topic = new topic($ts->t_name);
				if($topic->loadFromTimestamp($time)){
					$defaults = array("submit"=>"Process","title"=>$topic->title,"time"=>$time);
					$count=0;
					foreach($topic->items as $name => $data){
						$defaults["item_title_$count"] = $name;
						$defaults["item_file_$count"] = $data[0];
						$count++;	
					}
					$realitems = count($topic->items);
					$itemcount = count($topic->items);
					if(isset($_POST['additem'])){
						$itemcount = $numitems++;
					}
					makeItems($itemcount);
					$f->load_defaults_with_vals($defaults);
					add_items($itemcount,$defaults);		
				}else{
					echo "<h4 style=\"color:#FF0000\">No such record for time $time</h4>";	
				}
			}
		}else{
			echo "<h4 style=\"color:#FF0000\">Incomplete Function Call to Edit</h4>";	
		}
	break;
}
function getSeason ($table, $time)
{
	$date = $time;
	$year = date("Y", $time);
	$month = date("n", $time);
	if ($month<=6) $period=($year-1)."/".$year;
	else $period=$year."/".($year+1);
	return $period;
}

page_close();
include('./includes/bottom.php');
?>
