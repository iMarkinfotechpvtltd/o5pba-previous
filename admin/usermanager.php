<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');
$um = new usermanager;
$form = new jform;
$function = $_GET['function'];
$id = $_GET['id'];
echo "<pre>";
print_r($_POST);
echo "</pre>";
$perms_final = "";
if(isset($_POST))
{
	if($_POST['submit']=="Process")
	{
		 if(isset($_POST['submit']))
		 {
			$submit = $_POST['submit'];
		 }
		 if(isset($_POST['iduser']))
		 {
			$iduser = $_POST['iduser'];
		 }
		 if(isset($_POST['pass']))
		 {
			$pass = $_POST['pass'];
		 }
		 if(isset($_POST['function']))
		 {
			$function = $_POST['function'];
		 }
		 if(isset($_POST['perms']))
		 {
			 foreach($_POST['perms'] as $test)
			 {
				 $perms_final .= $test.","; 
			 }
		 }
		 
		 $perms_check = substr($perms_final, 0, -1);
	}
}
function makestuff(){
	global $form,$function,$pass,$validate,$perms,$id,$um,$user;
	if($function=='edit'){
		$user = $um->getuser($id);
		$uname = $user->name;
		$user_id = $id;
		foreach ($user->perms as $val){
			$perms[$val]=$val;
		}
		$disabled = 'disabled';
	}
	$form->add_element(array("type"=>"text",
	"name"=>"iduser",
	"valid_regex"=>"^[a-z]*$",
	"valid_e"=>"Username is lowercase letters only.",
	"minlength"=>"1",
	"length_e"=>"Username length error.",
	"value"=>$uname,
	"icase"=>1,
	"extrahtml"=>$disabled));
	if($function!='edit'){
		
		$form->add_element(array("type"=>"text",
		"name"=>"pass",
		"valid_regex"=>"^[a-z0-9A-Z]*$",
		"valid_e"=>"Alphanumaric only.",
		"minlength"=>"1",
		"length_e"=>"Password length error.",
		"icase"=>1,
		"pass"=>1));
		$form->add_element(array("type"=>"text",
		"name"=>"pass2",
		"valid_regex"=>"^".$pass."$",
		"valid_e"=>"Passwords do not match.",
		"minlength"=>"1",
		"length_e"=>"Password length error.",
		"icase"=>1,
		"pass"=>1));
	}else{
		$form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id
		));
	}
	$form->add_element(array("type"=>"hidden",
	"name"=>"function",
	"value"=>$function));
	$form->add_element(array("type"=>"submit",
	"name"=>"submit",
	"value"=>"Process"
	));
		
		// $submit = $form->elements['submit'];
		
	$form->add_element(array("type"=>"submit",
	"name"=>"resetpass",
	"value"=>"Reset Password"
	));
	global $perm;
	
	foreach ($perm->permissions as $name=>$value){
		if(isset($perms)) $check = array_key_exists($name,$perms) ? 'checked' : '';
		$form->add_element(array("type"=>"checkbox",
		"name"=>'perms['.$name.']',
		"value"=>$name,
		"extrahtml"=>$check
		
		));
	}
}
switch($function){
	default:
	if ($perm->have_perm('users')){
		
		$um->userlist();
		
	}else{
		
		echo '<h4>Users</h4>';
		echo '<table border="0" cellpadding="2" cellspacing="0" width="500">';
		echo '<tr class=dark><td colspan=2 width=200>Username</td><td colspan=2 width=300>Able to Edit:</td></tr>';
		foreach ($um->users as $theuser){
			echo sprintf('<tr class=light><td width=60>Edit</td><td width=140>%s</td><td width=240>%s</td><td width=60>&nbsp;</td></tr>',$theuser->name,$um->permstostring($theuser->perms));
		}
		echo'<tr class=light><td colspan=4><a href="usermanager.php?function=add">Add User</a></td></tr>';
		echo '</table>';
		
		
	}
	break;
	
	case 'add':
	$perm->check('users');
	makestuff();
	$showform=true;
	if(isset($submit)){
		// if($form->validate()){
			// $validate=true;
			// $form->load_defaults();
		// }else{
			if($um->checkname($iduser)){
				echo '<font color="red">Username already exists.</font>';
			}else{
				$newuser = new auser();
				$newuser->name = $iduser;
				$newuser->pass = $pass;
				$newuser->perms = $perms_check;
				$um->adduser($newuser);
				echo "<h4>Added user '$iduser'</h4>";
				$showform=false;
				$um = new usermanager;
				$um->userlist();
			}
		// }
	}
	if($showform){
		$form->start();
	                ?>
	                <br>
	                <table border="0" cellpadding="2" cellspacing="0" width=300>
					  <tr>
					  	<td colspan=2 class=dark width=300>
	  		                <p align="center">Add User</p>
						</td>
	                  </tr>
	                
	                  <tr>
	                      <td class=dark style='vertical-align:middle; text-align:right;'>Username</td>
	                      <td class=light>
	                      <? 
	                      $form->show_element("iduser");
	                      if(($validate)&&($test=$form->validate(false,array('iduser')))){
	                      	echo '<br><font color="red">'.$test.'</font>';
	                      }
	                       ?></td>
	                  </tr>
	                  <tr>
	                      <td class=dark style='vertical-align:middle; text-align:right;'>Password</td>
	                      <td class=light>
	                      <?
	                       $form->show_element("pass");
	                       if(($validate)&&($test=$form->validate(false,array('pass')))){
	                       	echo '<br><font color="red">'.$test.'</font>';
	                       }
	                      ?></td>
	                  </tr>
	                  <tr>
	                      <td class=dark style='vertical-align:middle; text-align:right;'>Retype Password</td>
	                      <td class=light>
	                      <?
	                      $form->show_element("pass2");
	                      if(($validate)&&($test=$form->validate(false,array('pass2')))){
	                      	echo '<br><font color="red">'.$test.'</font>';
	                      }
	                  	   ?></td>
	                  </tr>
	                  <tr>
	                      <td class=dark style='vertical-align:middle; text-align:right;'>Permissions</td>
	                      <td class=light>
	
	                  <?
	                  	   $perms=new O5Perm();
	                  	   foreach ($perms->permissions as $name=>$value){
	                  	   	if ($name!='admin'){
	                  	   		
	                  	   		$n='perms['.$name.']';
	                  	   		
	                  	   		$form->show_element($n);
	                  	   		echo $name.'<br>';
	                  	   	}
	                  	   }
	                  	   $form->show_element("function");
	                      ?></td>
	                  </tr>
	                  <tr>
	                      <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $form->show_element("submit"); ?></td>
	                  </tr>
	                </table>
	                <?
	                      $form->finish();
	                      
	}
	
	break;
	
	case 'edit':
	
	$perm->check('users');
	$showform=true;
	if($submit){
		$auser = $um->getuser($id);
		if(md5($auser->name)==$auser->pass){
			echo "<h3>Sorry</h3> User '".$auser->name."' has not changed his/her password since it was last reset. You cannot give this user permissions untill they change their password.";
			break;
		}else{
			$auser->perms = array_values($perms);
			$um->updateuser($auser);
			echo "<h4>Updated user '".$auser->name."'</h4>";
			logit("Updated user '".$auser->name."'");
			$showform=false;
			$um->userlist();
		}
	}elseif($resetpass){
		$auser = $um->getuser($id);
		echo "<h4>Password reset for '".$auser->name."'. The user will need to change their password the next time they log in, and after this has happened you will need to give them their permissions back.";
		$auser->pass = md5($auser->name);
		$auser->perms = array('reset');
		$um->updateuser($auser);
		$showform=false;
		logit("Password reset : ".$auser->name);
		$um->userlist();
	}
	makestuff();
	if($showform){
		$form->start();
            ?>
            <br>
            <table border="0" cellpadding="2" cellspacing="0" width=300>
			  <tr>
			  	<td colspan=2 class=dark width=300>
		                <p align="center">Edit User</p>
				</td>
              </tr>
            
              <tr>
                  <td class=dark style='vertical-align:middle; text-align:right;'>Username</td>
                  <td class=light>
                  <? 
                  $form->show_element("iduser");
                  
                   ?></td>
              </tr>
              <tr>
                  <td class=dark style='vertical-align:middle; text-align:right;'>Password</td>
                  <td class=light>
                  <? 
                  $form->show_element("resetpass");
                  
                   ?></td>
              </tr>
              <tr>
                  <td class=dark style='vertical-align:middle; text-align:right;'>Permissions</td>
                  <td class=light>

              <?
                   $perms=new O5Perm();
                   foreach ($perms->permissions as $name=>$value){
                   	if ($name!='admin' && $name!='reset'){
                   		
                   		$n='perms['.$name.']';
                   		
                   		$form->show_element($n);
                   		if ($name=='users'){
                   			echo 'admin - NOTE: this will allow the user to control other users.  Only one or two people per site should have this privlage.<br>';
                   		}else{
                   			echo $name.'<br>';
                   		}
                   	}
                   }
                   $form->show_element("function");
                  ?></td>
              </tr>
              <tr>
                  <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $form->show_element("submit"); ?></td>
              </tr>
            </table>
            <?
                  $form->finish();
	}
	break;
	
	case 'delete':
	$perm->check('users');
	$auser = $um->getuser($id);
	echo "<h4>User '".$auser->name."' has been removed.</h4>";
	$um->deleteuser($id);
	$um = new usermanager;
	$um->userlist();
	break;
	
	case 'changepassword':
	makestuff();
	if($submit){
		// if(!(($form->validate('ok',array('pass','pass2')))=='ok')){
			// $validate=true;
		// }else{
			$um->changepass($auth->auth['uid'],$pass);
			echo "<h4>Password Changed!</h4>";
			$um->userlist();
			break;
		// }
	}
	$form->start();
		?>
		<br>
		<table border="0" cellpadding="2" cellspacing="0" width=300>
			  <tr>
			  	<td colspan=2 class=dark width=300>
		                <p align="center">Change Password</p>
				</td>
              </tr>
            
              <tr>
                  <td class=dark style='vertical-align:middle; text-align:right;'>New Password</td>
                  <td class=light>
                  <? 
                  $form->show_element("pass");
                  if(($validate)&&($test=$form->validate(false,array('pass')))){
                  	echo '<br><font color="red">'.$test.'</font>';
                  }
                   ?></td>
              </tr>
              <tr>
                  <td class=dark style='vertical-align:middle; text-align:right;'>Re-type Password</td>
                  <td class=light>
                  <? 
                  $form->show_element("pass2");
                  if(($validate)&&($test=$form->validate(false,array('pass2')))){
                  	echo '<br><font color="red">'.$test.'</font>';
                  }
                   ?></td>
              </tr>
              <tr>
                  <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $form->show_element("submit"); ?></td>
              </tr>
        </table>
        <?
                   $form->finish();
                   break;
}
include('./includes/bottom.php');
page_close();

?>
