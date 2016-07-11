<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
if(isset($_GET['function']))
{
	$function = $_GET['function'];
}
if(isset($_POST['function']))
{
	$function = $_POST['function'];
	if($function=="perfect_validate")
	{
		$function = "";
		?>
		<script>
	window.location.href = 'http://o5pba.ca/admin/index.php?function=perfect'; 
	</script>
		<?php
	}
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
if(isset($_POST['center']))
{
	$center = $_POST['center'];
}	
if(isset($_POST['league']))
{
	$league = $_POST['league'];
}	
if(isset($_POST['submitnu']))
{
	$submitnu = $_POST['submitnu'];
}	
if(isset($_POST['id']))
{
	$id = $_POST['id'];
}
if ($function=='logout'){
	
	$auth->logout();
	page_close();
?>
<SCRIPT language="javascript">
top.location='<? echo $webpath; ?>';
</SCRIPT>
<?
	exit;
}
include_once('./includes/menuitems.php');
include('./includes/top.php');
function motd(){
	global $auth,$perm;
	global $_SERVER;
   ?>
   <p>
   Welcome to the O5PBA Admin section <b><? echo $auth->auth['uname']; ?></b>.<br>
   <br>
   Broswer Info:<br>
   <?
   echo $_SERVER['HTTP_USER_AGENT']."\n<br><br><b>Here you can perform a number of website updates.</b><br></p>";
   
   if ($perm->have_perm('admin')){
   ?>
   <p><b>Admin</b><br>
   You are like unto god.
   <?
   }
   if ($perm->have_perm('news')){
   ?>
   <p><b>News Admin</b><br>
   You can add/edit/delete news items to the news section of the website.
   <?
   }
   if ($perm->have_perm('tournament')){
   ?>
   <p><b>Tournament Admin</b><br>   
   You can add/edit/delete to the tournaments section of the website.
   <?
   }
   if ($perm->have_perm('coach')){
   ?>
   <p><b>Coach's Corner Admin</b><br>
   You can add/edit/delete to the coach's corner section of the website.
   <?
   }
/*   if ($perm->have_perm('store')){
   ?>
   <p><b>Store Admin</b><br>
   You can add/edit/delete to the store section of the website.
   <?
   }
*/
   if ($perm->have_perm('perfect')){
   ?>
   <p><b>Perfect Game Admin</b><br>
   You can add/edit/delete to the perfect game section of the website.
   <?
   }
   if ($perm->have_perm('calendar')){
   ?>
   <p><b>Upcoming Events</b><br>
   You can add/edit/delete to the Upcoming Events Calendar.
   <?
   }
   if ($perm->have_perm('users')){
   ?>
   <p><b>Admin</b><br>
   Here you can add/edit/delete other admins to sections. You can also customize their access.
   </p>
   <?
   }   
   if ($perm->have_perm('reset') && !$perm->have_perm('admin')){
   ?>
   <p><h1>NOTICE: Your password has been reset</h1><br>
   You will have to change your password before you will be able to do anything.  After your password has been changed, contact the user administrator to be given access to control sections again.
   </p>
   <?
   }
   ?>
   
   </p><br><br><br>
   <?
   
}
if(isset($function)){
	switch($function){
		
		case "store_new":
		$store=new store();
		$perm->check('store');
		if ($action=='validate'){
			$store->validate();
		}else{
			$store->form();
		}
		break;
		case "store_edit":
		$perm->check('store');
		$store=new store();
		$store->edit($_GET['id']);
		break;
		case "store_delete":
		$perm->check('store');
		$store=new store();
		$store->delete($_GET['id']);
		break;
		case "store":
		$perm->check('store');
		$store=new store();
		$store->ilist();
		break;
		
		
		case "perfect_new":
		$perm->check('perfect');
		$perfect=new perfect;
		$perfect->form();
		break;
		case "perfect_validate":
		$perm->check('perfect');
		$perfect=new perfect;
		$perfect->validate();
		break;
		case "perfect_delete":
		$perm->check('perfect');
		$perfect=new perfect;
		$perfect->delete($_GET['id']);
		break;
		case "perfect_edit":
		$perm->check('perfect');
		$perfect=new perfect;
		$perfect->edit($_GET['id']);
		break;
		case "perfect":
		case "perfect_list":
		$perm->check('perfect');
		$perfect=new perfect;
		$perfect->plist();
		break;
		
		
		case "logout":
		echo'<h5>You are now logged out.</h5>';
		break;
		
		default:
		motd();
		break;
	}
}else{
	motd();
}

include('./includes/bottom.php');
page_close();
?>
