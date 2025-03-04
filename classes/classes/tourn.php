<?php



class tournaments{
	
	var $db;
	var $form;
	var $title;
	var $realitems;
	var $t_name;
	var $titles;
	var $postpath;
	var $formatfile='tournament.ihtml';
	var $datetypes=array('avg'=>'Average Date','ent'=>'Entry Deadline','lea'=>'League Round','zon'=>'Zone/DC Final','pro'=>'Provincial Final','nat'=>'National Championship');
	
	function tournaments($t_name, $name=''){
		global $id,$htdocsdir;
		$this->t_name = $t_name;
		$this->titles= $t_name;
		$this->postpath= $t_name.'/posts/';
		
		$this->db=new DB;
		$this->form=new form;
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"name",
		"minlength"=>1,
		"length_e"=>"Name is required.<br>",
		"value"=>$name
		));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id
		));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"function",
		"value"=>"tourn_saveedit"
		));
		
		$this->form->add_element(array("type"=>"submit",
		"name"=>"submit",
		"value"=>"submit"
		));
		
	}
	function listing(){
		
		
		echo '<table border="0" cellpadding="2" cellspacing="0" class="tournament-list"><tr><th width=200 colspan=4>'.$this->titles.' Name</th></tr>';
		
		echo '<tr><td colspan="4"><table width="100%"><tr>';
		
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
		
		echo '</tr></table></td></tr>';
		
		echo '<tr><td colspan=2></td><td width=100>Created</td><td width=100>Modified</td><td colspan=2>Title</td></tr>';
		
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
		
		$this->db->query(sprintf('select * from %s where created>"%s" and created<"%s" order by title',$this->t_name,$start_date,$end_date));
		
		
		if ($this->db->num_rows()){
			while($this->db->next_record()){
				if ($color=='light'){
					$color='dark';
				}else{
					$color='light';
				}
				
				echo sprintf('<tr class=%s><td width=60><a href="?function=tourn_edit&id=%s">Rename</a></td>',$color, $this->db->f('ID'));
				echo sprintf('<td width=60><a href="?function=tourn_view&id=%s">Edit</a></td>',$this->db->f('ID'));
				echo sprintf('<td width=90>%s</td>',date("M d, Y",$this->db->f('created')));
				echo sprintf('<td width=90>%s</td>',date("M d, Y",$this->db->f('modified')));
				echo sprintf('<td width=250>%s</a></td>',$this->db->f('title'));
				echo sprintf('<td><a href="javascript:confirmDelete(\'%s\',\'?function=tourn_delete&id=%s\')">Delete</a></td></tr>',$this->db->f('title'),$this->db->f('ID'));
			}
			
		}else{
			echo sprintf('<tr><td colspan="4">No %s(s) found!</td></tr>',$this->t_name);
		}
		echo '<tr><td colspan=3><a href="?function=tourn_add">Add '.$this->titles.'</a></td><td>&nbsp;</td></tr>';
		
		
		echo '</table>';
	}
	
	
	function validate(){
		
		global $_POST,$auth;
		
		if (!$this->form->validate(false)){
			
			if ($_POST['id']){
				$query=sprintf('update %s set title = "%s",modified="%s",modifiedby="%s" where ID like "%s"',$this->t_name,$_POST['name'],time(),$auth->auth['uname'],$_POST['id']);
				$this->db->query($query);
			}else{
				$time=time();
				$query=sprintf('insert into %s (title,created,createdby,modified,modifiedby) values ("%s","%s","%s","%s","%s");',$this->t_name,$_POST['name'],$time,$auth->auth['uname'],$time,$auth->auth['uname']);
				$this->db->query($query);
			}
			logit(str_replace('"','&quot;',$query));
			$this->listing();
		}else{
			$this->form->load_defaults();
			$this->showform(true);
		}
		
	}
	
	function edit($id){
		
		$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
		$this->db->next_record();
		$this->tournaments($this->t_name, $this->db->f('title'));
		$this->showform();
		
	}
	
	function delete($id){
		global $htdocsdir;
		
		$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
		$this->db->next_record();
		
		$time=$this->db->f('created');
		$title=$this->db->f('title');
		
		$query=sprintf('delete from %s where ID like "%s"',$this->t_name,$id);
		$this->db->query($query);
		logit(str_replace('"','&quot;',$query));
		
		$query=sprintf('select * from %s_items where created like "%s"',$this->t_name,$time.'%');
		$this->db->query($query);
		
		while($this->db->next_record()){
			@ unlink($htdocsdir.$this->postpath.$this->db->f('created').$this->db->f('file'));
		}
		
		$query=sprintf('delete from %s_items where created like "%s"',$this->t_name,$time.'%');
		$this->db->query($query);
		logit(str_replace('"','&quot;',$query));
		logit("Deleted tournament '".$title."'");
		$this->listing();
		
	}
	
	function showform($validate=false){
		
		$this->form->start();
		echo '<table><tr><td>'.$this->titles.' Name:</td><td>';
		$this->form->show_element('name');
		$this->form->show_element('id');
		$this->form->show_element('function');
		if ($validate){
			echo '<font color=red>'.$this->form->validate('',array('name')).'</font>';
		}
		echo '</td></tr></table><br>';
		$this->form->show_element('submit');
		
		
		$this->form->finish();
	}
	
	function listitems($key,$title=''){
		$topic = new topic($this->t_name);
		echo $title.'<br>';
		echo '<table><tr><td width=450>';
		if ($topic->loadTournFromTimestamp($key)){
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				echo sprintf('<li><nobr>%s(%s) changed: %s</nobr><br>',$name,$file[0],date('M d/Y @ H:i',$file[4]));
				if ($file[3]){
					echo $file[3].'<br>';
				}
			}
			echo '</ul>';
		}else{
			echo '&nbsp;';
		}
		echo '</td><td width="50" align="center" style="text-align:center;vertical-align:middle;"><a href="'.$_SERVER['PHP_SELF'].'?function=tournament_edit&time='.$key.'">Edit</a></td></tr></table>';
	}
	
	function listitemsCompressProvince($key,$title=''){
		//alias because of differences in tournamentview
		$this->listitems($key,$title);
	}
	function photoitems($key,$title=''){
		//alias because of differences in tournamentview
		$this->listitems($key,$title);
	}
	function listdates($key,$title=''){
		//alias because of differences in tournamentview
		$this->listitems($key,$title);
	}
	function resultstitle($key){
		//alias because of differences in tournamentview
		echo 'Minutes / Results';
	}

	function title($key,$is, $isnot){
		//alias because of differences in tournamentview
		echo $is;
	}
	
	function makeItems($numitems){
		global $f,$function;
		for($i = 0; $i < $numitems;$i++){
			$f->add_element(array("type"=>"text","name"=>"item_title_$i","size"=>"35","minlength"=>"1","length_e"=>"Please enter the title for this item."));
			$f->add_element(array("type"=>"file","name"=>"item_file_$i"));
			$f->add_element(array('type'=>'checkbox','name'=>"item_delete_$i"));
			$f->add_element(array('type'=>'textarea','rows'=>'5','cols'=>'60','name'=>"item_description_$i"));
			
		}
	}
	function makeDateItems($numitems, $defaults){
		global $f,$function;
		for($i = 0; $i < $numitems;$i++){
			$f->add_element(array("type"=>"hidden","name"=>"item_title_$i",'value'=>$defaults["item_title_$i"],"size"=>"35","minlength"=>"1","length_e"=>"Please enter the title for this item."));
			$f->add_element(array("type"=>"text","name"=>"item_date_$i",'value'=>$defaults["item_date_$i"],"size"=>"35","minlength"=>"1","length_e"=>"Please enter the title for this item."));
			$f->add_element(array("type"=>"text","name"=>"item_location_$i",'value'=>$defaults["item_location_$i"],"size"=>"35","minlength"=>"1","length_e"=>"Please enter the title for this item."));
			
		}		
	}
	function add_items($numitems,$defaults=''){
		global $ts,$f,$function,$time;
		$f->add_element(array("type"=>'hidden',"name"=>'numitems','value'=>$numitems));
		$colspan=3;
		if (!strpos($time,'_d')){
		?>
		<script language="Javascript">
		
		function checkAddForm(){
			
			<?
			for($i=$this->realitems;$i<$numitems;$i++){
				echo 'if(document.forms[0].item_file_'.$i.'.value==""){';
				echo 'if(strpos(document.forms[0].item_title_'.$i.'.value,"</a>")==""){';
				echo "\n".' alert("You must submit a file for an added topic.");'."\n".' return false;'."\n";
				echo '}}';
			}
			?>
			
			return true;
		}
		
	            </script>
	            
	            
	    <? } ?>
		<h4><? echo $ts->system_name; ?></h4><h5>Editing: 
		<?
		if (strpos($time,'_f')){
			echo 'Format';
		}elseif (strpos($time,'_z')){
			echo 'Zone Results';
		}elseif (strpos($time,'_d')){
			echo 'Dates';
		}elseif(strpos($time,'_p')){
			echo 'Provincial Results';
		}elseif(strpos($time,'_i')){
			echo 'Information';
		}elseif (strpos($time,'_n')){
			echo 'National Results';
		}
		$f->start('','','?function='.$function);
	?></h5>
	<table width="500" cellpadding="3" cellspacing="0" border="0" bgcolor="#AFAFAF">
	<tr>
		<td colspan="2" style="font:bold;" align="left">Items<br>to insert a link, enter title field in the format <b>&lt;/a&gt;&lt;a href=URL>TITLE&lt;/a&gt;</b>
		</td><td>
				<? 
				if (strpos($time,'_ph')){
					echo sprintf('<a href="batch.php?path=%s&status=%s"><font style="color:#FF0000;">Batch Upload</font></a>', $time, $this->t_name);
				}
		?>
		</td>
	</tr>
	<tr bgcolor="#CFCFCF">
	<? $f->show_element('title'); 
	if (!strpos($time,'_d')){
	?>
		<td width="*" style="font:bold;">Title</td>
	    <td width="200" style="font:bold;">Document</td>
		<td width="20" style="font:bold;">Delete</td>

	    <? }else{ ?>
	    	<td style="font:bold;"></td>
	    	<td style="font:bold;">Date</td>
	    	<td style="font:bold;">Location</td>
	    
	    <? } ?>
	    
	</tr>
	<tr>
		<?
		for($i=0;$i<$numitems;$i++){
	 	?>
	    <tr>
	    <? if (!strpos($time,'_d')){ ?>
	
	    	<td width="*" align="center"><? $f->show_element("item_title_$i"); ?></td>
	        <td width="200" align="center"><? $f->show_element("item_file_$i"); 
	         echo '<br>Currently: '.$defaults["item_file_$i"]; ?></td>
	         
	         <? }else{ ?>
	         
	        <td align="center"><nobr><? $f->show_element("item_title_$i"); echo $this->datetypes["$i"]?>:</nobr></td>
	        <td align="center"><? $f->show_element("item_date_$i");?></td>
	        <td align="center"><? $f->show_element("item_location_$i"); ?></td>
	         
			<? } ?>
	        	<td width="20" align="center"><? $f->show_element("item_delete_$i"); ?></td>
	    </tr>
	    
	    <? if (strpos($time,'_ph')){ ?>
		<tr>
	    	<td width="*" align="center" colspan="4" style="font:bold;"><i> only jpeg format photos</i><br>Description:<br><? $f->show_element("item_description_$i"); ?></td>
	    </tr>
	    <? 
	    }
		}
	?>
	<tr bgcolor="#CFCFCF">
		<td colspan="2"><center>
		<?  
		$f->show_element("submit");
		$f->show_element('additem');
		$f->show_element('cancel');
		?></center></td>
	
	</tr>
	</table>
	
	<?
	$f->finish();
	
}



function edititems($time){
	global $ts,$htdocsdir;
	$ts->postfiles=$ts->abs.$this->postpath;
	global $f,$numitems;
	$numitems = $_POST['numitems'];
	$time = $_POST['time'];
	$this->db->query(sprintf('select ID,title from %s where created="%s"',$this->t_name, substr($time,0,strpos($time,'_'))));
	$this->db->next_record();
	$returnto=$this->db->f('ID');
	$ts->system_name=$this->db->f('title');
	
	$f->add_element(array("type"=>"submit","name"=>"next","value"=>"Next"));
	$f->add_element(array("type"=>"submit","name"=>"cancel","value"=>"Cancel","extrahtml"=>'onClick="document.location = \'?function=tourn_view&id='.$returnto.'\';return false;"'));
	$f->add_element(array("type"=>"hidden","name"=>"title","size"=>"70", "minlength"=>"1","length_e"=>"Please enter the title for this post."));
	$f->add_element(array("type"=>"submit","name"=>"submit","value"=>"Add","extrahtml"=>"onClick=\"return checkAddForm();\""));
	$f->add_element(array("type"=>"text","name"=>"ask_numitems","size"=>3,"maxlength"=>"3","valid_regex"=>"^[1-9]{1}[0-9]{0,1}$","valid_e"=>"Must be in range 1-99"));
	$f->add_element(array("type"=>'hidden',"name"=>'time','value'=>$time));
	$f->add_element(array("type"=>'submit','name'=>'additem','value'=>'Add Item','extrahtml'=>"onClick=\"document.forms[0].numitems.value++;return true;\""));
	
	if(isset($time)&&($time!="")){
		if(isset($_POST['submit'])){
			$this->makeItems($numitems);
			//Build the load default array on number of items
			$fields = array();
			for($i=0;$i<$numitems;$i++){
				array_push($fields,"item_title_$i");
			}
			//Check Add Form, if true, process, otherwise, print form again.
			// $test = $f->validate('ok',$fields);
			if (strpos($time,'_d')){
					
					for($i=0;$i<$numitems;$i++){
						if ($_POST["item_title_$i"]==''){
							$this->db->query(sprintf('DELETE from %s_items WHERE created="%s" and order1 ="%s"',$this->t_name,$time,$i));
							if ($_POST["item_date_$i"]!='' or $_POST["item_location_$i"]!=''){
								$this->db->query(sprintf('INSERT INTO %s_items (ititle,order1, created, modified) values ("%s","%s","%s","%s")',$this->t_name,$_POST["item_date_$i"],$i,$time,time()));
							}
							
						}elseif ($_POST["item_date_$i"]!='' or $_POST["item_location_$i"]!=''){
							
							
							$this->db->query(sprintf('DELETE from %s_items WHERE created="%s" and order1 ="%s"',$this->t_name,$time,$i));
							$this->db->query(sprintf('insert INTO %s_items (ititle,order1, created, modified) values ("%s","%s","%s","%s")',$this->t_name,$_POST["item_title_$i"].chr(9).$_POST["item_date_$i"].chr(9).$_POST["item_location_$i"],$i,$time,time()));
							echo logit("Added/Updated Date Item in tournament '".$ts->system_name."'<br>");
						}else{
							$this->db->query(sprintf('DELETE from %s_items WHERE created="%s" and order1 ="%s"',$this->t_name,$time,$i));
							
							if($this->db->affected_rows()){
								echo logit("Deleted Item '".$_POST["item_title_$i"]."' from tournament '".$ts->system_name."'<br>");
							}
						}
						
					}
					
					$this->db->query(sprintf('update %s set modified="%s" where ID like "%s"',$this->t_name,time(),$returnto));
					echo logit("Successfully updated tournament '".$ts->system_name."'<br>");
					$u = new update;
					$u->dowhatsnew();
			
					
					$this->view($returnto);				
			}else{
				// if($test=='ok'){
					$db=new db;
					$topic = new topic($ts->t_name);
					
					$topic->loadTournFromTimestamp($time);
					$filetest=true;
					for($i=0;$i<$numitems;$i++){
						
						if(isset($_POST["item_delete_$i"])){
							if($topic->deleteItem($i)){
								echo "Deleted Item ".($i+1)."<br>";
								logit("Deleted Item ".($i+1)."'");
							}
							
						}else{
							if(!$topic->updateItem($i,"item_title_$i","item_file_$i","item_description_$i")){
								echo logit("File error on item '".$_POST["item_title_$i"]."'");
								$filetest = false;
								break;
							}
						}
					}
					if($filetest){
						if ($ts->updateTourn($topic)){
							echo "<h4>Successfully Updated</h4>";
							
							$this->db->query(sprintf('update %s set modified="%s" where ID like "%s"',$this->t_name,time(),$returnto));
							logit("Successfully updated tournament '".$ts->system_name."'");
							$this->view($returnto);
						}
					}
				
				// }else{
					// echo 'Errors Detected, please check and resubmit';
					// $f->load_defaults($fields);
					// $f->check();
					// $this->add_items($_POST['numitems']);
				// }
			}
		}else{
			$topic = new topic($ts->t_name);
			$topic->loadTournFromTimestamp($time);
			
			$defaults = array("submit"=>"Process","title"=>$topic->title,"time"=>$time);
			$count=0;
			if (strpos($time,'_d')){
				foreach($topic->items as $name => $data){
					if (!strpos($name,chr(9))){
						
						$defaults["item_date_$count"] = $name;
						$count++;
					}else{
						$type=substr($name,0,strpos($name,chr(9)));
						$name=substr($name,strpos($name,chr(9))+1);
						$date=substr($name,0,strpos($name,chr(9)));
						$name=substr($name,strpos($name,chr(9))+1);
						$location = $name;
						$dates["$type"]=array('date'=>$date,'location'=>$location);
					}
				}
				foreach ($this->datetypes as $key=>$name){
					$this->datetypes["$count"]=$name;
					$defaults["item_title_$count"] = $key;
					$defaults["item_location_$count"] = $dates["$key"]['location'];
					$defaults["item_date_$count"] = $dates["$key"]['date'];
					$count++;
				}
				$this->realitems = $count;
				$itemcount = $count;
				$this->makeDateItems($itemcount,$defaults);
			}else{
				foreach($topic->items as $name => $data){
					$defaults["item_title_$count"] = $name;
					$defaults["item_file_$count"] = $data[0];
					$defaults["item_description_$count"] = $data[3];
					$count++;
				}
				$this->realitems = count($topic->items);
				$itemcount = count($topic->items);
				if(isset($_POST['additem'])){
					$itemcount = $numitems++;
				}
				$this->makeItems($itemcount);
			}
			$f->load_defaults_with_vals($defaults);
			$this->add_items($itemcount,$defaults);
		}
	}else{
		echo "<h4 style=\"color:#FF0000\">Incomplete Function Call to Edit</h4>";
	}
}

function view($id){
	global $classdir;
	
	$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
	$this->db->next_record();
	
	include ($classdir.$this->formatfile);
	
}

}

class viewtournaments{
	
	var $db;
	var $title;
	var $t_name;
	var $postpath;
	var $formatfile='tournament.ihtml';
	var $datetypes=array('avg'=>'Average Date','ent'=>'Entry Deadline','lea'=>'League Round','zon'=>'Zone/DC Final','pro'=>'Provincial Final','nat'=>'National Championship');
	
	function viewtournaments($t_name){
		global $id;
		$this->t_name = $t_name;
		if ($this->titles=$t_name=='tournament'){
			$this->titles='Tournaments';
		}elseif ($this->titles=$t_name=='khpevents'){
			$this->titles='KHP Event';
		}else{
			$this->titles='Other Events';
		}
		$this->db=new DB;
	}
	
	function listing($hideyears=0){
		
		
		echo '<table border="0" cellpadding="2" cellspacing="0" width=550>';
		echo '<th colspan=3>'.$this->titles.'</th></tr>';
		echo '<tr><td colspan="3"><table width="100%"><tr>';

		if ($hideyears==0){
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
		}
		echo '</tr></table></td></tr>';
		
		echo '<tr class=dark><th width=100>Modified</th><th>'.$this->titles.' Name</th></tr>';
		
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
		
		$this->db->query(sprintf('select * from %s where created>"%s" and created<"%s" order by title',$this->t_name,$start_date,$end_date));
		//$this->db->query(sprintf('select * from %s order by title',$this->t_name,$start_date,$end_date));
		if ($this->db->num_rows()){
			while($this->db->next_record()){
				
				if ($color=='light'){
					$color='dark';
				}else{
					$color='light';
				}
				echo sprintf('<tr class=%s>',$color);
				
				//echo sprintf('<td width=100>%s</td>',date("M d, Y",$this->db->f('created')));
				echo sprintf('<td width=100>%s</td>',date("M d, Y",$this->db->f('modified')));
				
				echo sprintf('<td width=350><nobr><a href="?function=tourn_view&id=%s">%s</a></nobr></td>',$this->db->f('ID'),$this->db->f('title'));
				
			}
		}else{
			echo sprintf('<tr><td colspan="3">No %s(s) found!</td></tr>',$this->t_name);
		}
		
		echo '</table>';
		
	}
	
	function listitems($key, $title=''){
		global $webpath;
		
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo $title.'<br>';
			echo '<table><tr><td width=450>';
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				if ($file[0]=='ComingSoon.pdf'){
					echo sprintf('<li><nobr>%s - Coming Soon!</nobr><br>',$name);
				}else{
					echo sprintf('<li><nobr>%s (%s)</nobr><br>',linkit($webpath.$this->postpath.$key.'/'.$file[0],$name),date('M d/Y  @ H:i',$file[4]));
				}
			}
			echo '</ul>';
			echo '</td></tr></table>';
		}else{
			echo '&nbsp;<br>';
		}
	}
	
		function listitemsCompressProvince($key, $title=''){
		global $webpath;
		
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo $title;
			
			foreach($topic->items as $name=>$file){
				foreach($topic->items as $name=>$file){
					$split = explode(' - ',$name);
					if ($file[0]=='ComingSoon.pdf'){
						$province[$split[0]][$split[1]]=$split[1];
					}else{
					$province[$split[0]][$split[1]]=linkit($webpath.$this->postpath.$key.'/'.$file[0],$split[1]);
					}
					if ($province[$split[0]]['Date']<$file[4] && $file[0]!='ComingSoon.pdf'){
						$province[$split[0]]['Date']=$file[4];
					}
				}
				
			}
			echo '<table style="font-size:9pt;">';
			//<th>Province</th><th colspan=3>&nbsp;</th><th><nobr>Last Updated</nobr></th></tr>';
			foreach($province as $name=>$data){
				echo sprintf('<tr><td><nobr>%s</nobr></td>',$name);
				echo sprintf('<td style="padding-left:5pt;"><nobr>%s&nbsp;</nobr></td>',$data['Mens']);
				echo sprintf('<td style="border-left: thin solid; padding-left:5pt;"><nobr>%s&nbsp;</nobr></td>',$data['Ladies']);
				echo sprintf('<td style="border-left: thin solid; padding-left:5pt;"><nobr>%s&nbsp;</nobr></td>',$data['Mixed']);
				if (isset($data['Date'])){
				echo sprintf('<td style="padding-left:10pt;"><nobr>%s</nobr></td></tr>',date('M d/Y @ H:i',$data['Date']));
				}else{
				echo '<td style="padding-left:10pt;"><nobr>Coming Soon</nobr></td></tr>';
				}
			}
			echo '</table><br>';
		}
	}

	
	function listdates($key, $title=''){
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo $title.'<br>';
			echo '<table><tr><td width=450>';
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				if (strpos($name,chr(9))){
					$type=substr($name,0,strpos($name,chr(9)));
					$name=substr($name,strpos($name,chr(9))+1);
					$date=substr($name,0,strpos($name,chr(9)));
					$name=substr($name,strpos($name,chr(9))+1);
					$location = $name;
					echo '<li>'.$this->datetypes[$type];
					if($date){
						echo '<br>- '.$date;
					}
					if($location){
						echo '<br>- '.$location;
					}
					
				}else{
					echo '<li>'.$name;
				}
			}
			echo '</ul>';
			echo '</td></tr></table>';
		}else{
			echo '&nbsp;<br>';
		}
	}
	
	function resultstitle($key, $table='<td bgcolor=F2F2F2>'){
		$topic=new topic($this->t_name);
		$minutes=$topic->loadTournFromTimestamp($key.'_m');
		$results=($topic->loadTournFromTimestamp($key.'_p')||$topic->loadTournFromTimestamp($key.'_z')||$topic->loadTournFromTimestamp($key.'_n'));
		if (($results) && ($minutes)){
			echo $table.'<p><font size="2">Minutes / Results:';
		}elseif($minutes){
			echo $table.'<p><font size="2">Minutes:';
		}elseif($results){
			echo $table.'<p><font size="2">Results:';
		}else{
			echo '<td>&nbsp;';
			
		}
	}
	function title($key,$is, $isnot){
		$topic=new topic($this->t_name);
		if (is_array($key)){
			foreach ($key as $val){
				$exists=$topic->loadTournFromTimestamp($val);
				if ($exists){
					echo $is;
					return;
				}
			
			}
			echo $isnot;
		
		}else{
			$exists=$topic->loadTournFromTimestamp($key);
			if ($exists){
				echo $is;
			}else{
				echo $isnot;
			}
		}
		
	}
	
	function photoitems($key, $title=''){
		global $id;
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo $title;
			echo sprintf('<ul><li><font size=2><a href="%s?function=photo&key=%s&id=%s">View photo album</a></font>',$_SERVER['PHP_SELF'],$key,$id);
			$this->db->query(sprintf('select modified from %s_items where created="%s" order by modified DESC',$this->t_name,$key));
			$this->db->next_record();
			echo sprintf('(%s)',date('M d/Y  @ H:i',$this->db->f('modified')));
			
		}else{
			echo '<td>&nbsp;<br>';
		}
	}
	
	
	function view($id){
		global $classdir;
		
		$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
		$this->db->next_record();
		
		$created=$this->db->f('created');
		$this->db->query(sprintf('select modified from %s_items where created like "%s%%" order by modified desc',$this->t_name,$created));
		$this->db->next_record();
		$newest=$this->db->f('modified');
		
		$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
		$this->db->next_record();
		if ($newest>$this->db->f('modified')){
			$this->db->query(sprintf('update %s set modified="%s" where ID like "%s"',$this->t_name,$newest	,$id));
			$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
			$this->db->next_record();
		}
		
		include ($classdir.$this->formatfile);
	}
	
	function resize_img($path, $file){
		
		if (!file_exists($path.'t'.$file)){
			$src_img = imagecreatefromjpeg($path.$file);
			if (imagesx($src_img) > imagesy($src_img)){
				$new_w = 80;
				$new_h = imagesy($src_img)*(80/imagesx($src_img));
			}else{
				$new_w = imagesx($src_img)*(80/imagesy($src_img));
				$new_h = 80;
			}
			$dst_img = imagecreate($new_w,$new_h);
			imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
			imagejpeg($dst_img, $path.'t'.$file);
		}
		
	}
	
	function view_folder($key,$id){
		global $htdocsdir,$webpath;
		if (hasmodule('advancedphoto')){
			$photo=new photo();
			$photo->postpath=$this->postpath;
			$photo->t_name=$this->t_name;
			$photo->view_folder($key,$id);
		}else{			
			/*
			second thingo in $accepted_types can be:
			y - show thumbnail
			n - don't show anything
			some_image - to show a generic image
			*/
			$accepted_types = array(".gif"=>"y", ".jpg"=>"y", ".mpg"=>"quicktime.gif", ".htm"=>"site.jpg", ".html"=>"site.jpg",".txt"=>"text.jpg",
			".mov"=>"quicktime.gif", ".rm"=>"real.gif", ".ram"=>"real.gif", ".ra"=>"real.gif", ".avi"=>"quicktime.gif", ".wav"=>"quicktime.gif", ".mid"=>"quicktime.gif", ".swf"=>"flash.gif", ".mp3"=>"quicktime.gif");
			
			$images_per_row = 3;
			
			$thumbnails = true;
			// some programs such as Graphic Workshop put a prefix on scaled images
			$thumbnail_prefix = "t_";
			// make sure you have the trailing /
			$thumbnail_directory_name = $htdocsdir.$this->postpath;
			
			$imagepath=$webpath.$this->postpath;
			$image_directory_name = $htdocsdir.$this->postpath;
			
			$topic = new topic($this->t_name);
			
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'">Back</a><br><br>';
			echo "<i><b>Image Files:</b></i>Click on the link to see the image<hr>";
			
			// index value used together with $images_per_row
			// to see if we should skip to the next row
			$i = 0;
			
			if ($topic->loadTournFromTimestamp($key)){
				
				echo "<table border=01 cellpadding=2 width=600><tr>";
				
				foreach($topic->items as $name=>$rawfile){
					$file=$rawfile[0];
					// Don't show '.' and '..'
					if ( $file != "." && $file != ".." ) {
						$extension = strtolower(substr($file,strpos($file,".")));
						// is this file type in the accepted types array?
						if ( $accepted_types[$extension] ) {
							$file_size = floor(filesize ($image_directory_name.$key.'/'.$file) / 1024);
							if ( $i>0 && is_integer($i/$images_per_row) ) {
								$beginning = "\n</tr>\n<tr>\n";
							}else {
								$beginning = "";
							}
							echo "$beginning<td width=100><a href=".$_SERVER['PHP_SELF']."?function=show&key=".$key."&id=".$_GET['expand']."&file=".str_replace ('&', '%26', str_replace (' ', '%20', $file)).">$name</a></td><td>".$rawfile[3].'&nbsp;</td>';
							
							if ( $thumbnails && ($accepted_types[$extension]=='y') ) {
								//$this->resize_img($image_directory_name, $file);
								@ $size = GetImageSize ('.'.$imagepath.$thumbnail_prefix.$file);
								echo '<td width=80><a href='.$_SERVER['PHP_SELF'].'?function=show&key='.$key."&id=".$_GET['expand']."&file=".str_replace ('&', '%26', str_replace (' ', '%20', $file))."><img src='$imagepath$key/$thumbnail_prefix$file' border=0 $size[3]></a></td>";
							}else if ( $thumbnails && ($accepted_types[$extension]=='n') ) {
								echo '<td><font size=-2>&nbsp</td>';
							}else if ( $thumbnails ) {
								echo sprintf('<td><a href=%s?function=show&key=%s&id=%s&file=%s><img src="%s" border=0></a></td>',$_SERVER['PHP_SELF'],$key,$id,str_replace ('&', '%26', str_replace (' ', '%20', $file)),$webpath.'fileicons/'.$accepted_types[$extension]);
							}
							
							$i++;
						}
					}
				}
				echo "</table>";
				
			}
		}
	}
	
	function view_media($key, $file){
		global $htdocsdir,$webpath;
		
		if (hasmodule('advancedphoto')){
			$photo=new photo();
			$photo->postpath=$this->postpath;
			$photo->t_name=$this->t_name;
			$photo->view_media($key,$file);
		}else{
			
			echo '<a href="'.$_SERVER['PHP_SELF'].'?function=photo&id='.$_GET['expand'].'&key='.$key.'">Back</a><br><br>';
			$types_image = array(".gif"=>"y", ".jpg"=>"y");
			$types_qtime = array(".mpg"=>"quicktime.gif", ".mov"=>"quicktime.gif", ".avi"=>"quicktime.gif", ".wav"=>"quicktime.gif", ".mid"=>"quicktime.gif", ".mp3"=>"quicktime.gif");
			$types_rmedia = array(".rm"=>"real.gif", ".ram"=>"real.gif", ".ra"=>"real.gif");
			$types_sites = array(".htm"=>"site.jpg", ".html"=>"site.jpg");
			$types_text = array(".txt"=>"text.jpg");
			$types_flash = array(".swf"=>"flash.gif");
			
			$extension = strtolower(substr($file,strpos($file,".")));
			$path=$htdocsdir.$this->postpath;
			$imagepath=$webpath.$this->postpath;
			
			//show images
			if ( $types_image[$extension] ) {
				echo '<center>';
				$size = GetImageSize (str_replace ('%20', ' ',$path).$key.'/'.$file);
				echo '<img src="'.$imagepath.'/'.$key.'/'.$file.'" '.$size[3].'>';
				echo '</center>';
			}
			
			//show htmls
			if ( $types_sites[$extension] ) {
				$fcontents = join ('', file ($imagepath.$file));
				echo $fcontents;
			}
			
			//show text files
			if ( $types_text[$extension] ) {
				$fcontents = join ('', file ($imagepath.$file));
				echo '<pre>'.$fcontents.'</pre>';
			}
			
			//embed quicktime movies
			if ( $types_qtime[$extension] ) {
				echo "<center><EMBED SRC='$imagepath/$file' width=320 height =240 AUTOPLAY='true' CONTROLLER='true' CACHE='true' TYPE='video/quicktime' ></center>";
			}
			
			//embed real movies
			if ( $types_rmedia[$extension] ) {
				echo '<center><OBJECT ID=video1 CLASSID="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" BORDER="2">'."\n";
				echo '<PARAM NAME="controls" VALUE="ControlPanel">'."\n";
				echo '<PARAM NAME="console" VALUE="Clip1">'."\n";
				echo '<PARAM NAME="autostart" VALUE="true">'."\n";
				echo '<PARAM NAME="src" VALUE="'.$path.'/'.$file.'">'."\n";
				echo '<EMBED SRC="'.$imagepath.'/'.$file.'" TYPE="audio/x-pn-realaudio-plugin" CONSOLE="Clip1" CONTROLS="ControlPanel" AUTOSTART=true NOJAVA=true BORDER="2">'."\n";
				echo '</EMBED>'."\n";
				
				echo '</OBJECT></center>'."\n";
			}
			
			//embed flash movies
			if ( $types_flash[$extension] ) {
				echo '<center><OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" CODEBASE="http://active.macromedia.com/flash5/cabs/swflash.cab#version=5,0,0,0">'."\n";
				echo '<PARAM NAME="MOVIE" VALUE="'.$path.'/'.$file.'">'."\n";
				echo '<PARAM NAME="PLAY" VALUE="true">'."\n";
				echo '<PARAM NAME="LOOP" VALUE="true">'."\n";
				echo '<PARAM NAME="QUALITY" VALUE="high">'."\n";
				echo '<EMBED SRC="'.$imagepath.'/'.$file.'" WIDTH="320" HEIGHT="240" PLAY="true" LOOP="true" QUALITY="high" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">'."\n";
				echo '</EMBED>'."\n";
				echo '</OBJECT>'."</center>\n\n";
			}
			
		}
	}
	
	
}

?>
