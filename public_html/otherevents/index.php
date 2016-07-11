<?php
require_once('../../classes/prepend.php');


include('../includes/top.php');
$t=new viewtournaments('otherevents');
$t->postpath='otherevents/posts/';

switch ($function){
	
	case "show":
	$t->view_media($key, $file);
	break;
	
	case "photo":
	$t->view_folder($key, $id);
	break;
	
	case "tourn_view":	
	if (isset($id)){
		$t->view($id);
	}elseif(isset($expand)){
		$t->view($expand);
	}else {
		$t->listing();
	}
		
	break;
	
	default:
	$t->listing();
	break;
}
include('../includes/bottom.php');
