<?
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
if(isset($_GET['function']))
{
	$function = $_GET['function'];
}
if(isset($_POST['name']))
{
	$name = $_POST['name'];
}
if(isset($_POST['month']))
{
	$month = $_POST['month'];
}
if(isset($_POST['day']))
{
	$day = $_POST['day'];
}
if(isset($_POST['year']))
{
	$year = $_POST['year'];
}
if(isset($_POST['amount']))
{
	$amount = $_POST['amount'];
}
if(isset($_POST['submitnu']))
{
	$submitnu = $_POST['submitnu'];
}
if(isset($_POST['function']))
{
	$function = $_POST['function'];
}
if(isset($_POST['id']))
{
	$id = $_POST['id'];
}
class khp{
	var $form;
	var $db;
	
	function khp($id=''){
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
		"name"=>"amount",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>League length error.</font>",
		"value"=>$this->db->f('amount')
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
		"value"=>'validate'
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
			$this->db->query(sprintf('select * from khp where id like "%s"',$id));
			
		}
		
		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->khp($id);
			
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
  		                <p align="center">Edit KHP Amounts</p>
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
                      <td class=dark style='vertical-align:middle; text-align:right;'>Donation Amount</td>
                      <td class=light>
                      <?
                      $this->form->show_element("amount");
                      if($validate){
                      	echo $this->form->validate('',array('amount'));
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
			$query=sprintf('replace into khp (id, name, date, amount, modified) VALUES ("%s","%s","%s","%s","%s");',$_POST['id'],$_POST['name'],$date,$_POST['amount'],time());
			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			global $page;
			
			$this->plist();
		}
	}
	function plist(){
		global $auth;
		echo '';

		echo '<table><tr><td colspan = 4><h4>Edit KHP Amounts</h4></td></tr><tr><td colspan = 4><Table width = 100%>';
		if (date('m')>4){
			
			$nyear=date('Y')+1;
		}else{
			$nyear=date('Y');
		}
		$start_year=2004;
		
		$i=1;
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
		
		$this->db->query(sprintf('select * from khp where date > "%s" and date < "%s" order by date;',$start_date,$end_date));
		//echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td></td><th width=210>Name</th><th width=110>Date</th><th width=135>Amount</th></tr>';
		$total=0;
		while($this->db->next_record()){
			if ($color=='light'){
				$color='dark';
			}else{
				$color='light';
			}
			echo sprintf('<tr class=%s><td width=60><a href="?function=edit&id=%s">Edit</a></td><td width=110>%s</td><td>%s</td><td style="text-align:right;">%01.2f</td><td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&id=%s\');">Delete</a></td></tr>',$color,$this->db->f('id'),$this->db->f('name'),date ("M jS, Y",$this->db->f('date')),$this->db->f('amount'),$this->db->f('name'),$this->db->f('id'));
			$total=$total+$this->db->f('amount');
		}
		echo sprintf('<tr class=%s><td width=60></td><td width=110></td><td style="text-align:right;">Total:</td><td style="text-align:right;">%01.2f</td><td width=60></td></tr>',$color ,$total);
		echo'<tr class=light><td colspan=6><a href="?function=new">Add Donation</a></td></tr>';
		echo '</table>';
	}
	
	function delete($id){
		
		$db=new DB;
		
		$db->query(sprintf('delete from khp where id like "%s";',$id));
		
		$this->plist();
	}
	
	
	
}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

$perm->check('khp');

switch($function){
	
	case "new":
	$khp=new khp();
	$khp->form();
	break;
	case "validate":
	$khp=new khp();
	$khp->validate();
	break;
	case "delete":
	$khp=new khp();
	$khp->delete($_GET['id']);
	break;
	case "edit":
	$khp=new khp();
	$khp->edit($_GET['id']);
	break;
	case "list":
	$khp=new khp();
	$khp->plist();
	break;
	
	
	case "tourn_add":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';
	$t->showform();
	break;
	case "tourn_saveedit":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';
	$t->validate();
	break;
	case "tourn_edit":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';
	$t->edit($id);
	break;
	case "tourn_delete":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';	
	$t->delete($id);
	break;
	case "tourn_view":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';
	$t->view($id);
	break;
	case "tournament_edit":
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->formatfile='khp.ihtml';
	$ts = new topicsystem('khpevents','Tournaments');
	$f = new jform;
	$t->edititems($time);
	break;
	
	default:
	$khp=new khp();
	$khp->plist();
	echo '<Br>';
	$t=new tournaments('khpevents');
	$t->titles='KHP Events';
	$t->postpath= 'kidshelpphone/posts/';
	$t->listing();
	break;
}

include('./includes/bottom.php');
@ page_close();
?>
