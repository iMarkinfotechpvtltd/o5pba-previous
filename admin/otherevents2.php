<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));

include_once('./includes/menuitems.php');
include('./includes/top.php');

if(isset($function)){
	switch($function){

		case "tourn_add":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->showform();
		break;
		case "tourn_saveedit":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->validate();
		break;
		case "tourn_edit":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->edit($id);
		break;
		case "tourn_delete":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->delete($id);
		break;
		case "tourn_view":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->view($id);
		break;
		case "tournament_edit":
		$perm->check('other');
		$t=new tournaments('otherevents');
		$ts = new topicsystem('otherevents','Tournaments');
		$f = new jform;
		$t->edititems($time);
		break;


		
		default:
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->listing();
		break;
	}
}else{
		$perm->check('other');
		$t=new tournaments('otherevents');
		$t->listing();
}
include('./includes/bottom.php');
page_close();
?>
