<?
class bod{
	var $form;
	var $db;
	
	function bod($class,$id=''){
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
		"name"=>"position",
		"value"=>$this->db->f('position')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"email",
		"value"=>$this->db->f('email')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"address",
		"value"=>$this->db->f('address')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"city",
		"value"=>$this->db->f('city')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"province",
		"value"=>$this->db->f('province')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"postal",
		"value"=>$this->db->f('postal')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"phone",
		"value"=>$this->db->f('phone')
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
			$this->db->query(sprintf('select * from aboutus where id like "%s"',$id));
			
		}
		
		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->bod($_GET['class'],$id);
			
			$this->form();
		}else{
			
			$this->plist();
		}
	}
	
	function form($validate=false){
		global $_POST;
		
                ?>
                <table border="0" cellpadding="2" cellspacing="0" width=500>
				  <tr>
				  	<td colspan=2 class=dark width=500>
  		                <p align="center">Edit</p>
					</td>
                  </tr>
                
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Name</td>
                      
                      <td class=light>
                      <? 
                      $this->form->start('','POST',$_SERVER['PHP_SELF'].'?class='.$_GET['class']);
                      
                      $this->form->show_element("name");
                      
                      if($validate){
                      	echo $this->form->validate('',array('name'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Position</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("position");
                      
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Email?</td>
                      <td class=light>
                      <? 
                      $this->form->show_element("email");

                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Address</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("address");
                      
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>City</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("city");
                      
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Province</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("province");
                      
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Postal</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("postal");
                      
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Phone</td>
                      <td class=light>
                      <? 
                      
                      $this->form->show_element("phone");
                      
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
		
		if($this->form->validate()){
			$this->form->load_defaults();
			
			$this->form(true);
		}else{
						
			$query=sprintf('replace into aboutus (id, name, position, email, address, city, province, postal, phone, class, modified) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s");',$_POST['id'], $_POST['name'], $_POST['position'], $_POST['email'], $_POST['address'], $_POST['city'], $_POST['province'], $_POST['postal'], $_POST['phone'], $_GET['class'], time());
//			echo $query;
			
			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			global $page;
			
			$this->plist();
		}
	}
	function plist(){
		global $auth,$htdocsdir;
		
		if ($_GET['class']=='bod'){
			echo '<h4>Edit BOD Entries</h4>';
		}elseif ($_GET['class']=='officestaff'){
			echo '<h4>Edit Office Staff</h4>';
		}
		
		echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td colspan=1></td><th width=150>Name</th><th width=100>Photo?</th><th width=100>Position</th><th width=100>Email</th></tr>';
		$total=0;
		$this->db->query(sprintf('select * from aboutus where class = "%s" order by id;',$_GET['class']));
		while($this->db->next_record()){
			$color = ($color=='dark') ? 'light' : 'dark';
			
			
			echo sprintf('<tr class=%s><td width=60><a href="?function=edit&class=%s&id=%s">Edit</a></td><td>%s</td>',$color,$_GET['class'],$this->db->f('id'),$this->db->f('name'));
			
			if (file_exists($htdocsdir.'aboutus/'.$_GET['class'].'/pictures/'.$this->db->f('id').'.jpg')){
				echo sprintf('<td width=60>Y - <a href="bodpic.php?class=%s&id=%s">New</a></td>',$_GET['class'],$this->db->f('id'));
			}else{
				echo sprintf('<td width=60>N - <a href="bodpic.php?class=%s&id=%s">Add</a></td>',$_GET['class'],$this->db->f('id'));
			}

			echo sprintf('<td>%s</td>',$this->db->f('position'));
			echo sprintf('<td>%s</td>',$this->db->f('email'));

			echo sprintf('<td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&class=%s&id=%s\');">Delete</a></td></tr>',$this->db->f('name'),$_GET['class'],$this->db->f('id'));

		}
		
		echo '<tr class=light><td colspan=6><a href="?function=new&class='.$_GET['class'].'">Add Person</a></td></tr>';
		echo '</table>';
	}
	
	function delete($id){
		global $htdocsdir;
		
		$db=new DB;
		
		$db->query(sprintf('delete from aboutus where id like "%s";',$id));
		
		$this->plist();
		
		
		@ unlink($htdocsdir."about_us/".$_GET['class']."/pictures/".$id.'.jpg');
		@ unlink($htdocsdir."about_us/".$_GET['class']."/pictures/t".$id.'.jpg');
	}
	
	
	
}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

$bod=new bod($_GET['class']);

switch($function){
	
	case "new":
	$bod->form();
	break;
	case "validate":
	$bod->validate();
	break;
	case "delete":
	$bod->delete($_GET['id']);
	break;
	case "edit":
	$bod->edit($_GET['id']);
	break;
	case "list":
	$bod->plist();
	break;
	

	
	default:
	$bod->plist();

	break;
}

include('./includes/bottom.php');
@ page_close();
?>
