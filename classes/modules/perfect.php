<?
class perfect{
	var $form;
	var $db;
	
	function perfect($id=''){
		global $auth;
		
		if (!$this->db){
			$this->db=new DB;
		}
		
		$this->form= new form;
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"name",
		"valid_regex"=>"^[a-zA-Z ]*$",
		"valid_e"=>"<br><font color='red'>Name is letters only.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Name length error.</font>",
		"value"=>$this->db->f('name')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"league",
		"value"=>$this->db->f('league')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"center",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Center length error.</font>",
		"value"=>$this->db->f('center')
		));
		
		global $months;
		global $days;
		global $years;
		$date=$this->db->f('date');
		if ($date){
			$m=date ("n",$date);
			$d=date ("j",$date);
			$y=date ("Y",$date);
		}
		$this->form->add_element(array("type"=>"select",
		"name"=>"month",
		"size"=>1,
		"valid_e"=>"<br><font color='red'>Select month.</font>",
		"value"=>$m,
		"options"=>$months
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"day",
		"size"=>1,
		"valid_e"=>"<br><font color='red'>Select day.</font>",
		"value"=>$d,
		"options"=>$days
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"year",
		"size"=>1,
		"valid_e"=>"<br><font color='red'>Select year.</font>",
		"value"=>$y,
		"options"=>$years
		));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"function",
		"value"=>'perfect_validate'
		));
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id
		));
		$this->form->add_element(array("type"=>"submit",
		"name"=>"submitnu",
		"value"=>"Process"
		));
		
	}
	
	function edit($id){
		
		if ($id){
			$this->db->query(sprintf('select * from perfect where id like "%s"',$id));
			
		}
		
		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->perfect($id);
			
			$this->form();
		}else{
			$this->list();
		}
	}
	
	function form($validate=false){
		global $_POST;
		$this->form->start();
                ?>
                <table border="0" cellpadding="2" cellspacing="0" width=300>
				  <tr>
				  	<td colspan=2 class=dark width=300>
  		                <p align="center">Edit Perfect Games</p>
					</td>
                  </tr>
                
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Name</td>
                      <td class=light>
                      <? 
                      $this->form->show_element("name");
                      if($validate){
                      	echo $this->form->validate('',array('name'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Date</td>
                      <td class=light>
                      <?
                       $this->form->show_element("month");
                       $this->form->show_element("day");
                       $this->form->show_element("year");
                       if($validate){
                       	echo $this->form->validate('',array('month'));
                       	echo $this->form->validate('',array('day'));
                       	echo $this->form->validate('',array('year'));
                       	if(!checkdate($_POST['month'],$_POST['day'],$_POST['year'])){
                       		echo '<br><font color="red">Invalid Date</font>';
                       	}
                       }
                      ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Bowling Center</td>
                      <td class=light>
                      <?
                      $this->form->show_element("center");
                      if($validate){
                      	echo $this->form->validate('',array('center'));
                      }
                      ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Province</td>
                      <td class=light>
                      <?
                      $this->form->show_element("league");
                      if($validate){
                      	echo $this->form->validate('',array('league'));
                      }
                      ?></td>
                  </tr>
                  
                  <tr>
                      <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $this->form->show_element("submitnu"); ?></td>
                  </tr>
                </table>
                <?
                      $this->form->finish();
                      
	}
	
	function validate(){
		
		if($this->form->validate()||(!checkdate($_POST['month'],$_POST['day'],$_POST['year']))){
			$this->form->load_defaults();
			
			$this->form(true);
		}else{
			$date=mktime (0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);
			$query=sprintf('replace into perfect (id, name, date, center, league, modified) VALUES ("%s","%s","%s","%s","%s","%s");',$_POST['id'],$_POST['name'],$date,$_POST['center'],$_POST['league'],time());	
			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			global $page;
			
			$this->plist();
		}
	}
	function plist(){
		global $auth,$year;
				echo '<table width=600 cellspacing=0 cellpadding=2>';

		echo '<tr><td colspan = 6><h4>Edit Perfect Games</h4>';
		echo '<table width="100%"><tr>';
		
		$start_year=2004;
		
		$i=1;
		if (date('m')>6){
			$nyear=date('Y')+1;
		}else{
			$nyear=date('Y');
		}
		
		if (isset($_GET['nyear'])){
			$year=$_GET['nyear'];
		}else{
			$year=$nyear;
		}
		
		for ($y=$nyear; $y>=$start_year; $y--){
			
			if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
				echo sprintf('<td><a href="%s?function=%s&nyear=%s">%s-%s</a></td>',$_SERVER['PHP_SELF'],$_GET['function'],$y,$y-1,substr($y,-2));
			}else{
				echo sprintf('<td>%s-%s</a></td>',$y-1,substr($y,-2));
			}
						$i++;
			if ($i==10){
				echo '</tr><tr>';
				$i=1;
			}
		}
		
		echo '</tr></table></td></tr>';
		
		$start_date=mktime(0,0,0,6,31,$year-1)-1;
		$end_date=mktime(0,0,0,7,3,$year)-1;
		
		$this->db->query(sprintf('select * from perfect where date > "%s" and date < "%s" order by date desc;',$start_date,$end_date));
		
		
		echo '<tr><th width=110 colspan=2>Name</th><th width=110>Date</th><th width=135>Bowling Centre</th><th width=245 colspan=2>Province</th></tr>';
		while($this->db->next_record()){
			if ($color=='light'){
				$color='dark';
			}else{
				$color='light';
			}
			if (date("Y",$this->db->f('date'))<($year-1)){
				echo sprintf('<tr class=%s><td>&nbsp;</td><td width=110><nobr>%s</nobr></td><td>%s</td><td>%s</td><td width=240>%s</td><td width=60>&nbsp;</td></tr>',$color,$this->db->f('name'),date ("M jS, Y",$this->db->f('date')),$this->db->f('center'),$this->db->f('league'));
			}else{
				echo sprintf('<tr class=%s><td width=60><a href="?function=perfect_edit&id=%s">Edit</a></td><td width=110>%s</td><td>%s</td><td>%s</td><td width=240>%s</td><td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=perfect_delete&id=%s\');">Delete</a></td></tr>',$color,$this->db->f('id'),$this->db->f('name'),date ("M jS, Y",$this->db->f('date')),$this->db->f('center'),$this->db->f('league'),$this->db->f('name'),$this->db->f('id'));
			}
		}
		echo'<tr class=light><td colspan=6><a href="?function=perfect_new">Add Game</a></td></tr>';
		echo '</table>';
	}
	
	function delete($id){
		
		$db=new DB;
		
		$db->query(sprintf('delete from perfect where id like "%s";',$id));
		
		$this->plist();
	}
	
}

?>