<?
// echo "<pre>";
// print_r($_GET);
// echo "</pre>";
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
if(isset($_GET['function']))
{
	$function = $_GET['function'];
}
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
if(isset($_POST['function']))
{
	$function = $_POST['function'];
}
class halloffame{
	var $form;
	var $db;

	function halloffame($id=''){
		global $auth;

		if (!$this->db){
			$this->db=new DB;
		}

		$this->form= new form;

		$this->form->add_element(array("type"=>"text",
		"name"=>"first",
		"valid_regex"=>"^[a-zA-Z ]*$",
		"valid_e"=>"<br><font color='red'>First Name is letters only.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>First Name length error.</font>",
		"value"=>$this->db->f('first')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"last",
		"valid_regex"=>"^[a-zA-Z ]*$",
		"valid_e"=>"<br><font color='red'>Last Name is letters only.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Last Name length error.</font>",
		"value"=>$this->db->f('last')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"city",
		"length_e"=>"<br><font color='red'>City length error.</font>",
		"value"=>$this->db->f('city')
		));
		$this->form->add_element(array("type"=>"checkbox",
		"name"=>"deceased",
		"checked"=>$this->db->f('deceased')
		));
		$this->form->add_element(array("type"=>"text",
		"name"=>"deceased_year",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"value"=>$this->db->f('deceased_year')
		));


		$this->form->add_element(array("type"=>"text",
		"name"=>"builder_year",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"value"=>$this->db->f('builder_year')
		));
		$this->form->add_element(array("type"=>"textarea",
		"rows"=>"10",
		"cols"=>'60',
		"name"=>"builder_story",
		"value"=>$this->db->f('builder_story')));


		$this->form->add_element(array("type"=>"text",
		"name"=>"legend_year",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"value"=>$this->db->f('legend_year')
		));
		$this->form->add_element(array("type"=>"textarea",
		"rows"=>"10",
		"cols"=>'60',
		"name"=>"legend_story",
		"value"=>$this->db->f('legend_story')));

		$this->form->add_element(array("type"=>"text",
		"name"=>"player_year",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"value"=>$this->db->f('player_year')
		));
		$this->form->add_element(array("type"=>"textarea",
		"rows"=>"10",
		"cols"=>'60',
		"name"=>"player_story",
		"value"=>$this->db->f('player_story')));

		$this->form->add_element(array("type"=>"text",
		"name"=>"bobi_year",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color='red'>Numaric only.</font>",
		"value"=>$this->db->f('bobi_year')
		));
		$this->form->add_element(array("type"=>"textarea",
		"rows"=>"10",
		"cols"=>'60',
		"name"=>"bobi_story",
		"value"=>$this->db->f('bobi_story')));


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
			$this->db->query(sprintf('select * from halloffame where id like "%s"',$id));

		}

		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->halloffame($id);

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
  		                <p align="center">Edit Hall of Fame</p>
					</td>
                  </tr>

                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Name</td>

                      <td class=light>
                      <?
                      $this->form->start();

                      $this->form->show_element("first");
                      echo ' ';

                      $this->form->show_element("last");

                      if($validate){
                      	echo $this->form->validate('',array('first'));
                      	echo $this->form->validate('',array('last'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>City</td>
                      <td class=light>
                      <?

                      $this->form->show_element("city");

                      if($validate){
                      	echo $this->form->validate('',array('city'));
                      }
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Deceased?</td>
                      <td class=light>
                      <?
                      $this->form->show_element("deceased");
                      $this->form->show_element("deceased_year");

                       ?></td>
                  </tr>
                  <tr>
                  		<td class="dark" style='text-align:center;' colspan="2">Builder Division</td>
                  	</tr>
                  	<tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Year</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("builder_year");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Story</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("builder_story");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class="dark" style='text-align:center;' colspan="2">Legend Division</td>
                  	</tr>
                  	<tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Year</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("legend_year");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Story</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("legend_story");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class="dark" style='text-align:center;' colspan="2">Player Division</td>
                  	</tr>
                  	<tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Year</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("player_year");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Story</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("player_story");
                      	?>
                      	</td>
                    </tr>
                     <tr>
                  		<td class="dark" style='text-align:center;' colspan="2">Builder of the Bowling Industry Division</td>
                  	</tr>
                  	<tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Year</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("bobi_year");
                      	?>
                      	</td>
                    </tr>
                    <tr>
                  		<td class=dark style='vertical-align:middle; text-align:right;'>Story</td>
                  		<td class=light>
                  		<?
                      		$this->form->show_element("bobi_story");
                      	?>
                      	</td>
                    </tr>
                   <tr>
                      <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $this->form->show_element("submitnu"); ?></td>
                  </tr>
                </table>
                <?
                $this->form->finish();

	}

	function validate(){

		// if($this->form->validate()){
			// $this->form->load_defaults();

			// $this->form(true);
		// }else{

			$query= sprintf('replace into halloffame (id, first, last, builder_story, builder_year, legend_story, legend_year, player_story, player_year, bobi_story, bobi_year, city, deceased, deceased_year, modified) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s");',$_POST['id'], $_POST['first'], $_POST['last'], $_POST['builder_story'], $_POST['builder_year'], $_POST['legend_story'], $_POST['legend_year'], $_POST['player_story'], $_POST['player_year'], $_POST['bobi_story'], $_POST['bobi_year'], $_POST['city'], isset($_POST['deceased']), $_POST['deceased_year'], time());
			//echo sprintf('replace into halloffame (id, first, last, builder_story, builder_year, legend_story, legend_year, player_story, player_year, bobi_story, bobi_year, city, deceased, deceased_year, modified) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s");',$_POST['id'], $_POST['first'], $_POST['last'], $_POST['builder_story'], $_POST['builder_year'], $_POST['legend_story'], $_POST['legend_year'], $_POST['player_story'], $_POST['player_year'], $_POST['bobi_story'], $_POST['bobi_year'], $_POST['city'], $_POST['deceased'], $_POST['deceased_year'], time());
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

		$this->db->query('select * from halloffame order by last, first;');
		echo '<h4>Edit Hall of Fame Entries</h4>';
		echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td colspan=1></td><th width=150>Name</th><th width=100>Photo?</th><th width=100>Builder</th><th width=100>Legend</th><th width=100>Player</th><th width=100>BOBI</th></tr>';
		$total=0;
		while($this->db->next_record()){
			$color = ($color=='dark') ? 'light' : 'dark';


			echo sprintf('<tr class=%s><td width=60><a href="?function=edit&id=%s">Edit</a></td><td>%s</td>',$color,$this->db->f('id'),$this->db->f('first').' '.$this->db->f('last'));

			if (file_exists($htdocsdir.'hallfame/pictures/'.$this->db->f('id').'.jpg')){
			echo sprintf('<td width=60>Y - <a href="hofpic.php?id=%s">New</a></td>',$this->db->f('id'));
			}else{
			echo sprintf('<td width=60>N - <a href="hofpic.php?id=%s">Add</a></td>',$this->db->f('id'));
			}

			echo ($this->db->f('builder_year')==0) ? '<td></td>' : '<td>'.$this->db->f('builder_year').'</td>';
			echo ($this->db->f('legend_year')==0) ? '<td></td>' : '<td>'.$this->db->f('legend_year').'</td>';
			echo ($this->db->f('player_year')==0) ? '<td></td>' : '<td>'.$this->db->f('player_year').'</td>';
			echo ($this->db->f('bobi_year')==0) ? '<td></td>' : '<td>'.$this->db->f('bobi_year').'</td>';


			echo sprintf('<td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&id=%s\');">Delete</a></td></tr>',$this->db->f('first').' '.$this->db->f('last'),$this->db->f('id'));

		}

		echo'<tr class=light><td colspan=6><a href="?function=new">Add Person</a></td></tr>';
		echo '</table>';
	}

	function delete($id){
		global $htdocsdir;

		$db=new DB;

		$db->query(sprintf('delete from halloffame where id like "%s";',$id));
		@ unlink($htdocsdir."hallfame/pictures/".$id.'.jpg');

		$this->plist();
	}



}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

$perm->check('halloffame');
$hof=new halloffame();

switch($function){

	case "new":
	$hof->form();
	break;
	case "validate":
	$hof->validate();
	break;
	case "delete":
	$hof->delete($_GET['id']);
	break;
	case "edit":
	$hof->edit($_GET['id']);
	break;
	case "list":
	$hof->plist();
	break;



	default:
	$hof->plist();

	break;
}

include('./includes/bottom.php');
@ page_close();
?>
