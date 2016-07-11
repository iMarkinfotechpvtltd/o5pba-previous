<?
class sponsors{
	var $form;
	var $db;

	function sponsors($id=''){
		global $auth;

		if (!$this->db){
			$this->db=new DB;
		}

		$this->form= new form;
		//LOGO
		$this->form->add_element(array("type"=>"file",
		"name"=>"logo",
		"value"=>''
		));

			
		$this->form->add_element(array("type"=>"text",
		"name"=>"company_name",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Title length error.</font>",
		"value"=>$this->db->f('company_name'),
		"size"=>"50"
		));

		$this->form->add_element(array("type"=>"text",
		"name"=>"contact_name",
		"minlength"=>"1",
		"length_e"=>"<br><font color='red'>Title length error.</font>",
		"value"=>$this->db->f('contact_name'),
		"size"=>"50"
		));
									
		$this->form->add_element(array("type"=>"text",
		"name"=>"url",
		"value"=>$this->db->f('url'),
		"size"=>"100"
		));
		
		// business card
		$this->form->add_element(array("type"=>"file",
		"name"=>"business_card",
		"value"=>''
		));

		$sects=array();
		if ($id){
		
			$this->db->query(sprintf('select * from sponsors where id = "%s"', $id));			
			$result = $this->db->query(sprintf('select * from sponsorship_level where id = "%s"', $this->db->f('sponsorship_level')) );	
			$v = mysql_fetch_row($result);			
			array_push($sects,array('label'=>$v[1],'value'=>$v[0]));
			
		} else {
			array_push($sects,array('label'=>'','value'=>''));
		}
		
		$this->db->query('select * from sponsorship_level order by id');
		while ($this->db->next_record()){
			array_push($sects,array('label'=>$this->db->f('name'),'value'=>$this->db->f('id')));
		}
	
		$this->form->add_element(array("type"=>"select",
		"name"=>"sponsorship_level",
		"value"=>$this->db->f('sponsorship_level'),
		"options"=>$sects
		));
	
		$this->form->add_element(array("type"=>"text",
		"name"=>"new_sponsorship_level",
		"value"=>'',
		"size"=>"50"
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
			$this->db->query(sprintf('select * from sponsors where id like "%s"',$id));

		}

		if ($this->db->num_rows()||$id==''){
			$this->db->next_record();
			$this->sponsors($id);

			$this->form();
		}else{

			$this->plist($id);

		} 
	}

	function form($validate=false){
		global $_POST;

                ?>
                <table border="0" cellpadding="2" cellspacing="0" width=500>
				  <tr>
				  	<TH colspan=2 class=dark width=500>
  		                <p align="center">Edit Sponsors</p>
					</TH>
                  </tr>

                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>LOGO</td>

                      <td class=light>
                      <? 
                      $this->form->start('','POST',$_SERVER['PHP_SELF'].'?function=validate&id='.$_GET['id']);

                      $this->form->show_element("logo"); 
					  
                      ?></td>
                  </tr> 
                  <tr>
                      <td class=light colspan = 2>The logo type will allow .jpg, .jpeg, .gif, .png only, the maximum size of the image is 500KB.</td>

                      <td class=light>
  						
                      </td>
                  </tr>
                  <tr>
                      <td class=light colspan = 2>Make the LOGO size: Platinum:150p x 75p, Gold:120p x 60p, Silver:100p x 50p , Others:80p x 30p, otherwise the logo will not be best shown. </td>

                      <td class=light>
  						
                      </td>
                  </tr> 
                  <tr>
                      <td class=light colspan = 2>If you have a LOGO existed already, and don't want to change it, leave it blank. </td>

                      <td class=light>
  						
                      </td>
                  </tr> 				  				   				  			  
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Company_Name</td>

                      <td class=light>
                      <?
                      $this->form->show_element("company_name");
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Contact_Name</td>

                      <td class=light>
                      <?
                      $this->form->show_element("contact_name");
                       ?></td>
                  </tr>
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Link_Address</td>

                      <td class=light>
                      <?
                      $this->form->show_element("url");
	                  if($validate){
                      	echo $this->form->validate('',array('url'));
                      }				  
                       ?></td>
                  </tr>	
	                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Business Card</td>

                      <td class=light>
                      <?
                      $this->form->show_element("business_card");	  
                       ?> If you don't have a web link, please upload you business card here.</td>
                  </tr>	  			    
                  <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Sponsorship_Level</td>

                      <td class=light>
                      <?
                      $this->form->show_element("sponsorship_level");
                       ?></td>
                  </tr>				  			  
                   <tr>
                      <td class=dark style='vertical-align:middle; text-align:right;'>Add New Sponsorship_Level Input Here</td>

                      <td class=light>
                      <?
                      $this->form->show_element("new_sponsorship_level");
                       ?> Unless you want to add new sponsorship level, leave it blank.</td>
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
		
		if($this->form->validate()){
			$this->form->load_defaults();

			$this->form(true);
		}else{
		
			if ($_POST['company_name'] == '') {
				echo "<font color='red'>Please input the company name !</font>"; 
				return;
			} else if ($_POST['contact_name'] == '') {
				echo "<font color='red'>Please input the contact name !</font>"; 
				return;
			} else if (($_POST['url'] == '') && ($_FILES["business_card"]['tmp_name'] == '') ){
				echo "<font color='red'>Please either input the link address or upload a business card!</font>"; 
				return; 
			} else if ($_POST['url'] && $_FILES["business_card"]['tmp_name'] ) {
				echo "<font color='red'>You can only input one of them, either a link or a business card!</font>"; 
				return;
			} else if ($_POST['sponsorship_level'] == '') {
				echo "<font color='red'>Please select the sponsorship level !</font>"; 
				return;
			}

			if ($_POST['id']!=''){
				if ($_POST['new_sponsorship_level'] != ''){
					$query1 = sprintf('insert into sponsorship_level set name = "%s";', $_POST['new_sponsorship_level']);
					$this->db->query($query1);
					$query2 = sprintf('select * from sponsorship_level where name like "%s";', $_POST['new_sponsorship_level']);						
					$this->db->query($query2);
					
					while ($this->db->next_record()){
						$new_sponsorship_id = $this->db->f('id');
					}
					 
					$query = sprintf('update sponsors set url = "%s", company_name = "%s", contact_name = "%s", sponsorship_level = "%s" where id = "%s";', $_POST['url'],$_POST['company_name'],$_POST['contact_name'], $new_sponsorship_id, $_POST['id']);
					$this->db->query($query);
				} else {
					if ($_POST['url'] != ''){			
						$query = sprintf('update sponsors set url = "%s", company_name = "%s", contact_name = "%s", sponsorship_level = "%s" where id = "%s";', $_POST['url'],$_POST['company_name'],$_POST['contact_name'], $_POST['sponsorship_level'], $_POST['id']);
						$this->db->query($query);
					} else {
						$query = sprintf('update sponsors set business_card = "%s", company_name = "%s", contact_name = "%s", sponsorship_level = "%s" where id = "%s";', $_FILES["business_card"]['name'],$_POST['company_name'],$_POST['contact_name'], $_POST['sponsorship_level'], $_POST['id']);
						$this->db->query($query);
					}
				}
			}else{
				if ($_POST['new_sponsorship_level'] != ''){
					$query1 = sprintf('insert into sponsorship_level set name = "%s";', $_POST['new_sponsorship_level']);
					$this->db->query($query1);
					$query2 = sprintf('select * from sponsorship_level where name like "%s";', $_POST['new_sponsorship_level']);						
					$this->db->query($query2);
					
					while ($this->db->next_record()){
						$new_sponsorship_id = $this->db->f('id');
					}
					
					$query = sprintf('insert into sponsors ( logo, url, company_name, contact_name, sponsorship_level, business_card ) VALUES ("%s","%s","%s","%s", "%s", "%s");',$_FILES["logo"]["name"], $_POST['url'], $_POST['company_name'], $_POST['contact_name'], $new_sponsorship_id, $_FILES['business_card']['name']);
					$this->db->query($query);
				} else {
					$query = sprintf('insert into sponsors ( logo, url, company_name, contact_name, sponsorship_level, business_card ) VALUES ("%s","%s","%s","%s", "%s", "%s");',$_FILES["logo"]["name"], $_POST['url'], $_POST['company_name'], $_POST['contact_name'], $_POST['sponsorship_level'], $_FILES['business_card']['name']);
					$this->db->query($query);
				}
			}
//			echo $query;
			
			// process the img file 
			$uptypes=array('image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'); 
			$max_file_size=5000000; 
			$destination_folder="../images/sponsors/"; 

			if (!is_uploaded_file($_FILES["logo"]["tmp_name"])) { 
				if ($_POST['id'] == '') {		
					echo "<font color='red'>Logo Image file not exists !</font>"; 
				}
				
			} else {
						
				if ($max_file_size < $_FILES["logo"]["size"]) { 
					echo "<font color='red'>Logo Image file size too large, maxium 500KB !</font>"; 
					exit; 
				} 

				if (!in_array($_FILES["logo"]["type"], $uptypes)) { 
					echo "<font color='red'>Only image files allowed </font>"; 
					exit; 
				} 
				
				if(!move_uploaded_file ($_FILES["logo"]['tmp_name'] , $destination_folder.$_FILES["logo"]['name']) ){ 
					echo "<font color='red'>Error on upload Logo image file! </a>"; 
					exit; 
				} 
					
	        	if ($_POST['id'] != '') {								
					$query5 = sprintf('update sponsors set logo = "%s" where id = "%s";', $_FILES["logo"]["name"], $_POST['id']);
					$this->db->query($query5); 
				}

			} 
	
			// process the business card
			$business_card_folder = "../images/business_card/"; 
						
			if ($_POST['url'] == '') {
			
				if (!is_uploaded_file($_FILES["business_card"]["tmp_name"])) { 
					if ($_POST['id'] == '') {		
						echo "<font color='red'>Image file not exists !</font>"; 
					}
				
				} else {
						
					if ($max_file_size < $_FILES["business_card"]["size"]) { 
						echo "<font color='red'>Image file size too large !</font>"; 
						exit; 
					} 

					if (!in_array($_FILES["business_card"]["type"], $uptypes)) { 
						echo "<font color='red'>Only image files allowed </font>"; 
						exit; 
					} 
		
					if(!move_uploaded_file ($_FILES["business_card"]['tmp_name'] , $business_card_folder.$_FILES["business_card"]['name']) ){ 
						echo "<font color='red'>Error on upload image file! </a>"; 
						exit; 
					} 
					
	        		if ($_POST['id'] != '') {								
						$query5 = sprintf('update sponsors set business_card = "%s" where id = "%s";', $_FILES["business_card"]["name"], $_POST['id']);
						$this->db->query($query5); 
					}
				}
			}
			
			logit(str_replace('"','&quot;',$query));
			global $page;

			$u = new update;
			$u->dowhatsnew();

			$this->plist($_POST['id']);
		}
	}
	
	function plist($id=''){
		global $auth,$htdocsdir;
		
		$ary=array();
		$this->db->query('select * from sponsorship_level order by id');
		while ($this->db->next_record()){
			array_push($ary,$this->db->f('name'));
		}		

		if($id) 
			$this->db->query(sprintf("select * from sponsors"));
		else 
		    $this->db->query(sprintf('select * from sponsors where id like "%s"', $id));
		
		$this->db->next_record();
				
		
		echo sprintf('<h4>Edit Sponsors - </h4>');
		
		$this->db->query(sprintf('select * from sponsors order by id;'));
		echo '<table width=600 cellspacing=0 cellpadding=2>';
		echo '<tr><td colspan=1></td><th width=150>Logo</th><th width=150>Company_Name</th><th width=150>Contact_Name</th><th width=150>URL</th><th width=150>Business_Card</th><th width=150>Sponsonship_Level</th><td></td></tr><tr><td colspan=7>&nbsp;</td></tr>';
		$total=0;
		while($this->db->next_record()){
			$color = ($color=='dark') ? 'light' : 'dark';


			echo sprintf('<tr class=%s><td width=60><a href="?function=edit&id=%s">Edit</a></td><td>%s</td>',$color,$this->db->f('id'),$this->db->f('logo'));
			echo sprintf('<td>%s</td>',$this->db->f('company_name'));
			echo sprintf('<td>%s</td>',$this->db->f('contact_name'));			
			echo sprintf('<td>%s</td>',$this->db->f('url'));
			echo sprintf('<td>%s</td>',$this->db->f('business_card'));
			echo sprintf('<td>%s</td>',$ary[$this->db->f('sponsorship_level')-1]);			

			echo sprintf('<td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=delete&id=%s\');">Delete</a></td></tr>',$this->db->f('company_name'),$this->db->f('id'));

		}

		echo sprintf('<tr class=light><td colspan=6><a href="?function=new">Add Sponsors</a></td></tr>');
		echo '</table>';
	}

	function delete($id){
		global $htdocsdir;

		$db=new DB;

		$db->query(sprintf('delete from sponsors where id like "%s";',$id));
		
		$this->plist($_GET['sext']);
	}


}




require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

$sponsors=new sponsors();

switch($function){

	case "new":
	$sponsors->form();
	break;
	case "validate":
	$sponsors->validate();
	break;
	case "delete":
	$sponsors->delete($_GET['id']);
	break;
	case "edit":
	$sponsors->edit($_GET['id']);
	break;
	case "list":
	$sponsors->plist($_GET['id']);
	break;



	default:
	$sponsors->plist();

	break;
}

include('./includes/bottom.php');
@ page_close();
?>
