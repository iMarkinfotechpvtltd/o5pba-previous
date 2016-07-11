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
		
		$this->db->query(sprintf('select * from %s order by title',$this->t_name));
		echo '<h4>Edit Tournaments</h4><table border="0" cellpadding="2" cellspacing="0" width=550>';
		echo '<tr><td></td><td width=100>Created</td><td width=100>Modified</td><td>Title</td></tr>';
		while($this->db->next_record()){
			if ($color=='light'){
				$color='dark';
			}else{
				$color='light';
			}
			
			//echo sprintf('<tr class=%s><td width=60><a href="?function=tourn_edit&id=%s">Rename</a></td>',$color, $this->db->f('ID'));
			echo sprintf('<td width=60><a href="?function=tourn_view&id=%s">Edit</a></td>',$this->db->f('ID'));
			echo sprintf('<td width=90>%s</td>',date("M d, Y",$this->db->f('created')));
			echo sprintf('<td width=90>%s</td>',date("M d, Y",$this->db->f('modified')));
			echo sprintf('<td width=250>%s</a></td></tr>',$this->db->f('title'));
			//echo sprintf('<td><a href="javascript:confirmDelete(\'%s\',\'?function=tourn_delete&id=%s\')">Delete</a></td></tr>',$this->db->f('title'),$this->db->f('ID'));
		}
		
		//echo '<tr><td colspan=3><a href="?function=tourn_add">Add '.$this->titles.'</a></td><td>&nbsp;</td></tr>';
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
		echo '<table><tr><td width=250>';
		if ($topic->loadTournFromTimestamp($key)){
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				echo sprintf('<li><nobr>%s(%s) changed: %s</nobr><br>',$name,$file[0],date('M d/Y',$file[4]));
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
		echo '<td>';
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
		<td colspan="<? echo $colspan; ?>" style="font:bold;" align="left">Items<br>to insert a link, enter title field in the format <b>&lt;/a&gt;&lt;a href=URL>TITLE&lt;/a&gt;</b></td>
	</tr>
	<tr bgcolor="#CFCFCF">
	<? $f->show_element('title'); 
	if (!strpos($time,'_d')){
	?>
		<td width="*" style="font:bold;">Title</td>
	    <td width="200" style="font:bold;">Document</td>
	    <? }else{ ?>
	    	<td width="*" colspan=2 style="font:bold;">Date</td>
	    
	    <? } ?>
			<td width="20" style="font:bold;">Delete</td>
	    
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
	         
	        <td colspan=2 width="*" align="center"><? $f->show_element("item_title_$i"); ?></td>
	         
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
	echo "aaaaaaaaaaaaaaaaaaaaaa";
	global $ts,$htdocsdir;
	$ts->postfiles=$ts->abs.$this->postpath;
	global $f,$numitems;
	
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
			$test = $f->validate('ok',$fields);
			if($test=='ok'){
				$db=new db;
				
				if (strpos($time,'_d')){
					for($i=0;$i<$numitems;$i++){
						if (isset($_POST["item_delete_$i"])){
							$this->db->query(sprintf('DELETE from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$time,$i));
						}else{
							$this->db->query(sprintf('SELECT * from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$time,$i));
							if ($this->db->next_record()){
								if ($this->db->f('ititle')!=$_POST["item_title_$i"] || $this->db->f('order1')!=$i || $this->db->f('discription')!=$_POST['item_description_'.$count]){
									$this->db->query(sprintf('UPDATE %s_items SET ititle="%s",order1="%s",modified="%s" WHERE created="%s" AND order1="%s"',$this->t_name,$_POST["item_title_$i"],$i,time(),$time,$i));
								}
							}else{
								$this->db->query(sprintf('INSERT INTO %s_items (ititle,order1, created, modified) values ("%s","%s","%s","%s")',$this->t_name,$_POST["item_title_$i"],$i,$time,time()));
							}
						}
					}
					
					echo logit("Successfully updated tournament '".$ts->system_name."'<br>");
					
					$this->view($returnto);
					
				}else{
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
							
							logit("Successfully updated tournament '".$ts->system_name."'");
							$this->view($returnto);
						}
					}
				}
			}else{
				echo 'Errors Detected, please check and resubmit';
				$f->load_defaults($fields);
				$f->check();
				$this->add_items($_POST['numitems']);
			}
		}else{
			$topic = new topic($ts->t_name);
			$topic->loadTournFromTimestamp($time);
			
			$defaults = array("submit"=>"Process","title"=>$topic->title,"time"=>$time);
			$count=0;
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
	
	echo sprintf('<a href="%s">Return to Tournament Menu</a><br>',$_SERVER['PHP_SELF']);
	
	include ($classdir.$this->formatfile);
	
}
}


class viewtournaments{
	
	var $db;
	var $title;
	var $t_name;
	var $postpath;
	var $formatfile='tournament.ihtml';
	
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
	
	function listing(){
		
		$this->db->query(sprintf('select * from %s',$this->t_name));
		echo '<table>';
		while($this->db->next_record()){
			if ($color=='light'){
				$color='dark';
			}else{
				$color='light';
			}
			
			switch ($this->db->f('title')){
				case 'Canadian High Low Doubles':
				$img='./images/2004Highinfo.jpg';
				break;
				
				case 'Canadian Open':
				$img='./images/2004Openinfo.jpg';
				break;
				
				case 'Canadian Youth Challenge':
				$img='./images/2004Youthinfo.jpg';
				break;
			}
			echo sprintf('<tr><td><nobr><a href="?function=tourn_view&id=%s"><img src="%s" border="0"></a></nobr></td>',$this->db->f('ID'),$img);
			echo sprintf('<td style="vertical-align:middle; font-size:10pt"><nobr>Last updated: %s<br></nobr></td></tr>',date("M d, Y",$this->db->f('modified')));
			
		}
		?>
		</table>
		    </td>
  </tr>
</table>
<br>
		<?
	}
	
	function listitems($key, $title=''){
		global $webpath;
		
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo $title;
			echo '<table><tr><td width=250>';
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				if ($file[0]=='ComingSoon.pdf'){
					echo sprintf('<li><nobr>%s - Coming Soon!</nobr><br>',$name);
				}else{
					echo sprintf('<li><nobr>%s <font style="font-size: 7pt;">(%s)</font></nobr><br>',linkit($webpath.$this->postpath.$key.'/'.$file[0],$name),date('M d/Y @ H:i',$file[4]));
				}
			}
			echo '</ul>';
			echo '</td></tr></table>';
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
			echo $title;
			echo '<table><tr><td width=250>';
			echo '<ul>';
			foreach($topic->items as $name=>$file){
				echo '<li><nobr>'.$name.'</nobr>';
			}
			echo '</ul>';
			echo '</td></tr></table>';
		}else{
			echo '&nbsp;<br>';
		}
	}
	
	function resultstitle($key){
		$topic=new topic($this->t_name);
		$minutes=$topic->loadTournFromTimestamp($key.'_m');
		$results=($topic->loadTournFromTimestamp($key.'_p')||$topic->loadTournFromTimestamp($key.'_z')||$topic->loadTournFromTimestamp($key.'_n'));
		if (($results) && ($minutes)){
			echo 'Minutes / Results';
		}elseif($minutes){
			echo 'Minutes';
		}elseif($results){
			echo 'Results';
			
		}
	}
	
	function photoitems($key, $title=''){
		
		$topic = new topic($this->t_name);
		if ($topic->loadTournFromTimestamp($key)){
			echo '<td>';
			echo sprintf('<ul><li><a href="%s?function=photo&key=%s&expand=%s">View photo album</a>',$_SERVER['PHP_SELF'],$key,$_GET['expand']);
			$this->db->query(sprintf('select modified from %s_items where created="%s" order by modified DESC',$this->t_name,$key));
			$this->db->next_record();
			echo sprintf(' <font style="font-size: 7pt;">(%s)</font>',date("M d/Y  @ H:i",$this->db->f('modified')));
			
		}else{
			echo '<td valign="top"><li>Unavailable<br>';
		}
	}
	
	
	function view($id){
		global $classdir;
		
		$this->db->query(sprintf('select * from %s where ID like "%s"',$this->t_name,$id));
		$this->db->next_record();
		/*
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
		
		*/
		
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
		
			$photo=new photo();
			$photo->postpath=$this->postpath;
			$photo->t_name=$this->t_name;
			$photo->view_folder($key,$id);
	}
	
	function view_media($key, $file){
		global $htdocsdir,$webpath;
		
			$photo=new photo();
			$photo->postpath=$this->postpath;
			$photo->t_name=$this->t_name;
			$photo->view_media($key,$file);

	}
	
	
}
?>
