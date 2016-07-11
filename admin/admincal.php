<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include_once('./includes/top.php');

if(isset($_GET['action']))
{
	$action = $_GET['action'];
}
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
if(isset($_POST['id']))
{
	$id = $_POST['id'];
}	
if(isset($_POST['action']))
{
	$action = $_POST['action'];
}
if(isset($_POST['title']))
{
	$title = $_POST['title'];
}
if(isset($_POST['eventtype']))
{
	$eventtype = $_POST['eventtype'];
}
if(isset($_POST['eventvalue']))
{
	$eventvalue = $_POST['eventvalue'];
}
if(isset($_POST['poster']))
{
	$poster = $_POST['poster'];
}
if(isset($_POST['location']))
{
	$location = $_POST['location'];
}
if(isset($_POST['date_m']))
{
	$date_m = $_POST['date_m'];
}
if(isset($_POST['date_d']))
{
	$date_d = $_POST['date_d'];
}
if(isset($_POST['date_y']))
{
	$date_y = $_POST['date_y'];
}
if(isset($_POST['description']))
{
	$description = $_POST['description'];
}
if(isset($_POST['submit']))
{
	$submit = $_POST['submit'];
}
if(isset($_POST['process']))
{
	$process = $_POST['process'];
}
class statuses {
	var $db;
	var $form;
	
	function statuses($id=''){
		global $colour;
		$this->db=new db;
		$this->form=new form;
		
		if ($id!=''){
			$this->db->query(sprintf('select * from calendar where id = "%s"',$id));
			echo $this->db->next_record();
		}
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$this->db->f('id')));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"action",
		"value"=>$_GET['action']));
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"title",
		"valid_regex"=>"^[a-z0-9A-Z,. \(\)\$&%#\-]*$",
		"valid_e"=>"Alphanumaric only.",
		"minlength"=>"1",
		"size"=>"60",
		"length_e"=>"Title length error.",
		"value"=>$this->db->f('title')));
		
		$this->form->add_element(array("type"=>"select",
		"name"=>"eventtype",
		"value"=>$this->db->f('eventtype'),
		"minlength"=>"1",
		"valid_e"=>"Please Select.",
		"options"=>array(array('value'=>'','label'=>'Please Select'),array('value'=>'Province','label'=>'Province'),array('value'=>'Zone','label'=>'Zone'),array('value'=>'DC','label'=>'DC'),array('value'=>'League','label'=>'League'))
		));

		$this->form->add_element(array("type"=>"text",
		"name"=>"eventvalue",
		"minlength"=>"1",
		"length_e"=>"Length error.",
		"value"=>$this->db->f('eventvalue')));
		
		if ($this->db->f(date)>0) {
			$month=date('m',$this->db->f(date));
			$day=date('j',$this->db->f(date));
			$year=date('Y',$this->db->f(date));
			$hour=date('H',$this->db->f(date));
			$min=date('i',$this->db->f(date));
		}	
		
		
		global $days,$months,$years,$hours,$mins,$auth;
		
		$this->form->add_element(array("type"=>"select",
		"name"=>"date_m",
		"value"=>$month,
		"options"=>$months,
                "minlength"=>"1",
                "valid_e"=>"<nobr>Month Required.</nobr>"
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"date_d",
		"value"=>$day,
		"options"=>$days,
                "minlength"=>"1",
                "valid_e"=>"<nobr>Day Required."
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"date_y",
		"value"=>$year,
		"options"=>$years,
                "minlength"=>"1",
                "valid_e"=>"<nobr>Year Required.</nobr>"
		));
		// $this->form->add_element(array("type"=>"select",
		// "name"=>"date_h",
		// "value"=>$hour,
		// "options"=>$hours
		// ));
		// $this->form->add_element(array("type"=>"select",
		// "name"=>"date_n",
		// "value"=>$min,
		// "options"=>$mins
		// ));
		
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"poster",
		"value"=>strtolower($auth->auth['uname'])));
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"location",
		"size"=>"40",
		"value"=>$this->db->f('location')));
		
		$this->form->add_element(array("type"=>"textarea",
		"rows"=>"10",
		"cols"=>'80',
		"name"=>"description",
		"minlength"=>"1",
		"length_e"=>"Description length error.",
		"value"=>$this->db->f('description')));

		$this->form->add_element(array("type"=>"submit",
		"name"=>"submit",
		"value"=>'Submit',
		));

		$this->form->add_element(array("type"=>"hidden",
		"name"=>"process",
		"value"=>'Submit',
		));
	}
	function slist(){
/*
		global $rcolour;
		$me=$_SERVER['PHP_SELF'];
		echo 'Configure Events<br><br>';
		$this->db->query('select * from calendar order by date DESC');
		echo '<table cellpadding=0 cellspacing=0 width=400><tr><td>&nbsp;</td><td>Title</td><td colspan=3>Modified</td></tr>';
		while($this->db->next_record()){
				echo sprintf('<tr><td><a href="%s?action=edit&id=%s">Edit</a></td><td>%s</td><td>%s</td><td><a href="%s?action=delete&id=%s">Delete</a></td></tr>',$me,$this->db->f('id'),$this->db->f('title'),date ("jS M Y h:i:s A",$this->db->f('modified')),$me,$this->db->f('id'));
		}
		echo sprintf('<tr><td><a href="%s?action=add">Add</a></td><td colspan=3>&nbsp</td></tr></table>',$me);
*/



		global $months;
		if (isset($_GET['year'])){
			$year=$_GET['year'];
		}else{
			$year=date('Y');
		}
		if (isset($_GET['month'])){
			$month=$_GET['month'];
			
		}else{
			$month=date('m');
		}
		for ($i=1; $i<=12; $i++){
			$count[$i]=0;
		}
		$me=$_SERVER['PHP_SELF'];
		$list=array();
		$years=array();
		echo 'Configure Events<br><br>';
		$this->db->query('select * from calendar order by date DESC');
		while($this->db->next_record()){
			$num=date('Y', $this->db->f('date'));
			$mon=date('m', $this->db->f('date'));
						
			$years[$num]=$num;
			if ($year==$num){
				$count[date('n', $this->db->f('date'))]++;	
			}
			if ($year==$num && $month==$mon){
				
				array_push($list,sprintf('<tr><td><a href="%s?action=edit&id=%s">Edit</a></td><td style="white-space:nowrap;">%s</td><td>%s</td><td style = "white-space:nobr;">%s</td><td><a href="javascript:confirmDelete(\'%s\',\'%s?action=delete&id=%s\')">Delete</a></td></tr>',$me,$this->db->f('id'),date ("jS M Y ",$this->db->f('date')),$this->db->f('title'),date ("M j, Y",$this->db->f('modified')),$this->db->f('title'),$me,$this->db->f('id')));
			}
		}
	//	sort($years);
		echo '<table width="650"><tr><td><table width="650"><tr>';
		foreach ($years as $vals=>$num){
				if ($num==$year){
					$class='E6E6E6';
				}else{
					$class='';
				}
			echo sprintf('<td bgcolor="%s"><a href="%s?year=%s&month=1">%s</a></td>',$class,$me,$num,$num);
		}
		echo '</tr></table></tr><tr><td><table width="650"><tr>';
		foreach ($months as $vals=>$num){
			if ($num['value']!=''){
				if ($num['value']==$month){
					$class='current';
				}else{
					$class='';
				}
				$mon=$num['value'];
				echo sprintf('<td class="%s"><a href="%s?year=%s&month=%s">%s (%s)</a></td>',$class,$me,$year,$mon,$num['label'],$count[$mon]);
			}
		}
		echo '</tr></table></td></tr><tr><td><br><table cellpadding=0 cellspacing=0 width=650><tr><td width="75">&nbsp;</td><td width="100">Date</td><td width="200">Title</td><td colspan=3 width = 100>Modified</td></tr>';
		foreach ($list as $vals){
			
			echo $vals."\n";
		}
		echo sprintf('<tr><td><a href="%s?action=add&year=%s&month=%s">Add</a></td><td colspan=4>&nbsp</td></tr></table></table>',$me,$year,$month);


	}
	function snew(){
		echo 'Add Event';
		$this->sform();
	}
	function sedit($id){
		echo 'Edit Event';
		
		$this->statuses($id);
		$this->sform();
	}
	
	function sform($error=''){
		global $auth;
		$this->form->start();


		$this->form->show_element('id');
		$this->form->show_element('action');
		?>
		<table>
		<tr><td>Title:</td><td>
		<?
		$this->form->show_element('title');
		if ($error){
			echo '<br><font color="red">'.$this->form->validate("", array('title')).'</font><br>';
		}
		?>
		</td></tr>
		<tr><td>Event Type:</td><td>
		
		<?
		$this->form->show_element('eventtype');
		if ($error){
			echo '<br><font color="red">'.$this->form->validate("", array('eventtype')).'</font><br>';
		}
		
		$this->form->show_element('eventvalue');
		if ($error){
			echo '<br><font color="red">'.$this->form->validate("", array('eventvalue')).'</font><br>';
		}
		?>
		</td></tr>
		<tr><td>Posted By:</td><td>
		<?
		echo $auth->auth['uname'];
		$this->form->show_element('poster');
		?>
		</td></tr>
		<tr><td>Location:</td><td>
		<?
		$this->form->show_element('location');
		?>
		</td></tr>
		<tr><td>Event Date/Time:<br><font size=1>(mm/dd/yyyy)<br>(hh:mm)</font>
		<?
                if ($error){
                        echo '<br><font color="red">'.$this->form->validate("", array('date_m')).'</font>';
                        echo '<br><font color="red">'.$this->form->validate("", array('date_d')).'</font>';
                        echo '<br><font color="red">'.$this->form->validate("", array('date_y')).'</font>';

		}
		?>

		</td><td>
		<?
		$this->form->show_element('date_m');
		echo '/';
		$this->form->show_element('date_d');
		echo '/';
		$this->form->show_element('date_y');
		echo '<br>';
		$this->form->show_element('date_h');
		echo ' : ';
		$this->form->show_element('date_n');
		?>
		</td></tr>
		<tr><td>Description:</td><td>
		<?
		$this->form->show_element('description');
		if ($error){
			echo '<br><font color="red">'.$this->form->validate("", array('description')).'</font><br>';
		}
		?>
		</td></tr>
		<tr><td colspan=2>
		<?
		$this->form->show_element('submit');
		$this->form->show_element('process');
		?>
		</td></tr>
		</table>
		
		<?
		$this->form->finish();
	}
	function delete($id){
		echo 'Updating Database.....';
		$this->db->query(sprintf('delete from calendar where id="%s"',$id));	
		
		echo 'succeeded.<br><br>';
		$this->slist();
	}
	function validate(){
		echo 'validating '.$_POST['action'].'.......';
		$results=$this->form->validate("ok", array('title','description','eventvalue','eventtype','date_m','date_d','date_y','date_h','date_n'));

		if($results!="ok"){
			echo 'failed.  Please correct errors below and resubmit<br><Br>';
			$this->form->load_defaults(array('id','action','title','date_m','date_d','date_y','date_h','date_n','eventtype','eventvalue','poster','location','description','submit','process'));
		

			$this->sform('true');
		}else{
			echo 'passed.<br>Updating Database.....';
			$mdate=mktime($_POST['date_h'],$_POST['date_n'],'0',$_POST['date_m'],$_POST['date_d'],$_POST['date_y']);
			if($_POST['id']!=''){
				$query=sprintf('update calendar set date="%s", modified="%s", title="%s", description="%s", poster="%s", location="%s", eventtype="%s", eventvalue="%s" where id="%s"',$mdate,time(), $_POST['title'], $_POST['description'], $_POST['poster'], $_POST['location'], $_POST['eventtype'], $_POST['eventvalue'],$_POST['id']);
			}else{
				$query=sprintf('insert into calendar (date, modified, title, description, poster, location, eventtype, eventvalue ) values ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',$mdate,time(), $_POST['title'], $_POST['description'], $_POST['poster'], $_POST['location'], $_POST['eventtype'], $_POST['eventvalue']);
			}
			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			
			$this->slist();
		}
		
	}
}
$perm->check('calendar');
$s=new statuses();

switch ($action){
	
	case "add":
	if (isset($process)){
		$s->validate();
	}else{
		$s->snew();
	}
	break;
	
	case "edit":
	if (isset($process)){
		$s->validate();
	}else{
		$s->sedit($id);
	}
	break;
	
	case "delete":
		$s->delete($_GET['id']);
	break;
	
	default:
	$s->slist();
	break;
}
include('./includes/bottom.php');
page_close();
?>
