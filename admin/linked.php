<?
echo "<pre>";
		print_r($_GET);
		echo "</pre>";
class links{
	var $form;
	var $db;
	function links($id=''){
		global $auth;

		if (!$this->db){
			$this->db=new DB;
		}

		$this->form= new form;

		$this->form->add_element(array("type"=>"text",
		"name"=>"title",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Title length error.</font>",
		"value"=>$this->db->f('title'),
		"size"=>"50"
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"url",
		"value"=>$this->db->f('url'),
		"size"=>"100"
		));
		
		$opts=array(array('label'=>'Online','value'=>'1'), array('label'=>'Offline','value'=>'0'));
				
		$this->form->add_element(array("type"=>"select",
		"name"=>"status",
		"value"=>$this->db->f('status'),
		"options"=>$opts
		));
		
		$sects=array();
		$this->db->query('select * from link_sections order by id');
		while ($this->db->next_record()){
			array_push($sects,array('label'=>$this->db->f('name'),'value'=>$this->db->f('id')));
		}
		
		$this->form->add_element(array("type"=>"select",
		"name"=>"section",
		"value"=>$_GET['sext'],
		"options"=>$sects
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
			$this->db->query(sprintf('select * from link where id like "%s"',$id));

		}

		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->links($id);

			$this->form();
		}else{

			$this->plist($_GET['sext']);
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
                      <td class=dark style='vertical-align:middle; text-align:right;'>Title</td>

                      <td class=light>
                      <?
                      $this->form->start('','POST',$_SERVER['PHP_SELF'].'?function=validate&sext='.$_GET['sext']);

                      $this->form->show_element("title");
                      if($validate){
                      	echo $this->form->validate('',array('title'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>URL</td>

                      <td class=light>
                      <?
                      $this->form->show_element("url");
                      if($validate){
                      	echo $this->form->validate('',array('url'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Status</td>

                      <td class=light>
                      <?
                      $this->form->show_element("status");
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Section</td>

                      <td class=light>
                      <?
                      $this->form->show_element("section");
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
				$query = sprintf('update link set title = "%s", url = "%s", status = "%s", section = "%s", modified = "%s", modified_by = "%s" where id = "%s";',$_POST['title'],$_POST['url'],$_POST['status'], $_POST['section'], time(), $auth->auth['uname'],$_POST['id']);
			}else{
				$query = sprintf('insert into link (id, section, title, url, status, created, modified, modified_by) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s");',$_POST['id'], $_POST['section'], $_POST['title'], $_POST['url'], $_POST['status'], time(), time(), $auth->auth['uname']);
			}
//			echo $query;

			$this->db->query($query);
			logit(str_replace('"','&quot;',$query));
			global $page;

			$u = new update;
			$u->dowhatsnew();

			$this->plist($_POST['section']);
		// }
	}
	function plist($sext=''){
		global $auth,$htdocsdir;

		$this->db->query(sprintf('select * from link_sections where id = "%s"',$sext));
		
		$this->db->next_record();
				
		
		echo sprintf('<h4>Edit Links - %s</h4>',$this->db->f('name'));
		
		$this->db->query(sprintf('select * from link where section = "%s" order by title;',$sext));
		echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td colspan=1></td><th width=150>Title</th><th width=150>URL</th><td></td></tr>';
		$total=0;
		while($this->db->next_record()){
			$color = ($color=='dark') ? 'light' : 'dark';


			echo sprintf('<tr class=%s><td width=60><a href="?function=edit&id=%s&sext=%s">Edit</a></td><td>%s</td>',$color,$this->db->f('id'),$sext,$this->db->f('title'));
			echo sprintf('<td>%s</td>',$this->db->f('url'));
			

			echo sprintf('<td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&id=%s&sext=%s\');">Delete</a></td></tr>',$this->db->f('title'),$this->db->f('id'),$sext);

		}

		echo sprintf('<tr class=light><td colspan=6><a href="?function=new&sext=%s">Add Link</a></td></tr>',$sext);
		echo '</table>';
	}

	function delete($id){
		global $htdocsdir;

		$db=new DB;

		$db->query(sprintf('delete from link where id like "%s";',$id));
		
		$this->plist($_GET['sext']);
	}


}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');
		
		$function = $_GET['function'];
		$id = $_GET['id'];
		$sext = $_GET['sext'];
		if(isset($_POST['function']))
		{
			$function = $_POST['function'];
		}
$perm->check('links');
$links=new links();

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
	$links->plist($_GET['sext']);
	break;



	default:
	$links->plist($_GET['sext']);

	break;
}

include('./includes/bottom.php');
@ page_close();
?>
