<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
set_time_limit(0);

include_once('./includes/menuitems.php');
include('./includes/top.php');
$perm->check('news');

$upload_class = new Upload_Files;

if ($_POST['action']=='submit'){
	
	$upload_class->temp_file_name = trim($_FILES['upload']['tmp_name']);
	$upload_class->file_name = trim(strtolower($_FILES['upload']['name']));
	
	$upload_class->upload_dir = $htdocsdir.$_POST['status']."/posts/" . $_POST['path'].'/';
	$upload_class->upload_log_dir = $htdocsdir.$_POST['status']."/posts/";
	$upload_class->max_file_size = 5242880;
	$upload_class->banned_array = array("");
	$upload_class->ext_array = array(".zip");
	
	$valid_ext = $upload_class->validate_extension();
	$valid_size = $upload_class->validate_size();
	$valid_user = $upload_class->validate_user();
	$max_size = $upload_class->get_max_size();
	$file_size = $upload_class->get_file_size();
	$file_exists = $upload_class->existing_file();
	
	if (!$valid_ext) {
		$result = "The file extension is invalid, please try again!";
	}elseif (!$valid_size) {
		$result = "The file size is invalid, please try again! The maximum file size is: $max_size and your file was: $file_size";
	}elseif (!$valid_user) {
		$result = "You have been banned from uploading to this server.";
	}elseif ($file_exists) {
		$result = "This file already exists on the server, please try again.";
	} else {
		
		$upload_file = $upload_class->upload_file_with_validation();
		
		if (!$upload_file) {
			$result = "Your file could not be uploaded!";
		} else {
			$result = "Your file has been successfully uploaded to the server.";
		}
	}
	
	
	if ($result != "Your file has been successfully uploaded to the server."){
		echo $result;
		$upload_class->uploadform($_POST['path'],$_POST['status']);
	}else{
		//unlink($upload_class->upload_dir .'*.jpg');
		
		exec('unzip -j -o "'. $upload_class->upload_dir . $upload_class->file_name . '" -d '.$upload_class->upload_dir);
		
		unlink($upload_class->upload_dir . $upload_class->file_name);
		
		$db=new DB();
		$db->query(sprintf('select * from %s_items where created = "%s" order by `order1`',$_POST['status'],$_POST['path']));
		$current=array();
		$i=0;
		while ($db->next_record()){
			$current[]=array('ititle'=>$db->f('ititle'),'file'=>$db->f('file'),'order'=>$db->f('order1'),'discription'=>$db->f('discription'),'modified'=>$db->f('modified'));
			$i++;
		}
		
		echo $result;
		echo '<br><br>Refreshing Thumbnails:<br><Br>';
		$photo=new photo();
		$d = dir($upload_class->upload_dir);
		while($entry=$d->read()) {
			if (substr($entry,0,3)!="md_" && substr($entry,0,3)!="sm_" && substr($entry,0,2)!="t_" && substr($entry,0,1)!=".") {
				$new = true;
				
				foreach ($current as $id=>$vals){
					if ($vals['file']==$entry){
						$new=false;
					}
				}
				if ($new){
					$query=sprintf('insert into %s_items (`created`,`order1`,`ititle`,`file`,`modified`) values ("%s","%s","%s","%s","%s")',$_POST['status'],$_POST['path'],$i,$entry,$entry,time());
					$db->query($query);
					//echo $query.'<br>';
					$i++;
				}
				echo sprintf('&nbsp;-&nbsp;%s<br>',$entry);
				if (hasmodule('advancedphoto')){
					$photo->resize_img($upload_class->upload_dir,$entry,'t_');
					$photo->resize_img($upload_class->upload_dir,$entry,'sm_');
					$photo->resize_img($upload_class->upload_dir,$entry,'md_');
				}else{
					$photo->resize_img($upload_class->upload_dir,$entry,'t_');
				}
				
			}
		}
		logit('Batch upload to '.$_POST['status']);
		$db->query(sprintf('update %s set modified="%s", modifiedby="%s" where created="%s"', $_POST['status'],time(),$auth->auth['uname'],substr($_POST['path'],0,strpos($_POST['path'],"_"))));
		$u = new update;
		$u->dowhatsnew();
			
	}
	
	
}else{
	$upload_class->uploadform($_GET['path'],$_GET['status']);
	
	
}


include('./includes/bottom.php');
page_close();
?>
