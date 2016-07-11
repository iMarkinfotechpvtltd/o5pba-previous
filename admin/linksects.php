<?
class linksects{
	var $form;
	var $db;

	function linksects($id=''){
		global $auth;

		if (!$this->db){
			$this->db=new DB;
		}

		$this->form= new form;

		$this->form->add_element(array("type"=>"text",
		"name"=>"name",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Section Name length error.</font>",
		"value"=>$this->db->f('name'),
		"size"=>"40"
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
			$this->db->query(sprintf('select * from link_sections where id like "%s"',$id));

		}

		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->linksects($id);

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
  		                <p align="center">Edit Link Sections</p>
					</td>
                  </tr>

                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Section Name</td>

                      <td class=light>
                      <?
                      $this->form->start();

                      $this->form->show_element("name");
                      if($validate){
                      	echo $this->form->validate('',array('name'));
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
		global $auth;
		// if($this->form->validate()){
			// $this->form->load_defaults();

			// $this->form(true);
		// }else{

			if ($_POST['id']!=''){
				$query = sprintf('update link_sections set name = "%s", modified = "%s", modified_by = "%s" where id = "%s";',$_POST['name'], time(), $auth->auth['uname'],$_POST['id']);
			}else{
				$query = sprintf('insert into link_sections (id, name, created, modified, modified_by) VALUES ("%s","%s","%s","%s","%s");',$_POST['id'], $_POST['name'], time(), time(), $auth->auth['uname']);
			}
//			echo $query;

			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			global $page;

			$u = new update;
			$u->dowhatsnew();

			$this->plist();
		// }
	}
	function plist(){
		global $auth,$htdocsdir;

		$this->db->query('select * from link_sections order by id;');
		echo '<h4>Edit Link Page Sections</h4>';
		echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td colspan=2></td><th width=150>Name</th><td></td></tr>';
		$total=0;
		while($this->db->next_record()){
			$color = ($color=='dark') ? 'light' : 'dark';

			if (strtolower($auth->auth['uname'])=='winston'){
				echo sprintf('<tr class=%s><td width=60><a href="?function=edit&id=%s">Edit</a></td>',$color,$this->db->f('id'));
			}else{
				echo sprintf('<tr class=%s><td width=60>&nbsp;</td>',$color);
			}
			echo sprintf('<td width=120><a href="linked.php?sext=%s">Edit Section Links</a></td><td>%s</td>',$this->db->f('id'),$this->db->f('name'));

			if (strtolower($auth->auth['uname'])=='winston'){
				echo sprintf('<td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&id=%s\');">Delete</a></td></tr>',$this->db->f('name'),$this->db->f('id'));
			}else{
				echo '<td width=60>&nbsp;</td></tr>';
			}
		}

		echo'<tr class=light><td colspan=6><a href="?function=new">Add Section</a></td></tr>';
		echo '</table>';
	}

	function delete($id){
		global $htdocsdir;

		$db=new DB;

		$db->query(sprintf('delete from link_sections where id like "%s";',$id));
		$db->query(sprintf('delete from link where section like "%s";',$id));


		$this->plist();
	}


}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');
$function = $_GET['function'];
if(isset($_POST['function']))
		{
			$function = $_POST['function'];
		}
$perm->check('links');
$links=new linksects();

switch($function){

	case "new":
	$links->form();
	break;
	case "validate":
	$links->validate();
	break;
	case "delete":
	$links->delete($_GET['id']);
	break;
	case "edit":
	$links->edit($_GET['id']);
	break;
	case "list":
	$links->plist();
	break;



	default:
	$links->plist();

	break;
}

include('./includes/bottom.php');
@ page_close();
?>
