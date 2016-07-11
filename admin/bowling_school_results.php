<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));

include_once('./includes/menuitems.php');
include('./includes/top.php');
echo "<pre>";
print_r($_POST);
echo "</pre>";
if(isset($_GET['function']))
{
	$function = $_GET['function'];
}
if(isset($_POST['function']))
{
	$function = $_POST['function'];
}
if(isset($function)){
	switch($function){

		case "tourn_add":
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->showform();
		break;
		case "tourn_saveedit":
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->validate();
		break;
		case "tourn_edit":
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->edit($id);
		break;
		case "tourn_delete":
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->delete($id);
		break;
		case "tourn_view":
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->view($id);
		break;
		case "tournament_edit":
		$perm->check('bowling_school');
		$t = new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$ts = new topicsystem('bowling_school_mini','Bowling School Results / Pictures');
		$f = new jform;
		$t->edititems($time);
		break;


		
		default:
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->listing();
		break;
	}
}else{
		$perm->check('bowling_school');
		$t=new tournmini('bowling_school_mini','Bowling School Results / Pictures');
		$t->listing();
}
include('./includes/bottom.php');
page_close();
?>
