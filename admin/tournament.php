<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));

include_once('./includes/menuitems.php');
include('./includes/top.php');

$db = new DB;
$function = $_GET['function'];
if(isset($_GET['id']))
{
$id = $_GET['id'];	
}
if(isset($_GET['time']))
{
$time = $_GET['time'];	
}
if(isset($_POST['function']))
{
	$function = $_POST['function'];
}
if(isset($function)){
	switch($function){

		case "tourn_add":
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->showform();
		break;
		case "tourn_saveedit":
		$function = $_POST['function'];
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->validate();
		break;
		case "tourn_edit":
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->edit($id);
		break;
		case "tourn_delete":
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->delete($id);
		break;
		case "tourn_view":
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->view($id);
		break;
		case "tournament_edit":
		$perm->check('tournament');
		$t = new tournaments('tournament');
		$ts = new topicsystem('tournament','Tournaments');
		$f = new jform;
		$t->edititems($time);
		break;


		
		default:
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->listing();
		break;
	}
}else{
		$perm->check('tournament');
		$t=new tournaments('tournament');
		$t->listing();
}
include('./includes/bottom.php');
page_close();
?>
