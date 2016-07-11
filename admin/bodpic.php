<?
set_time_limit(0);
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');


$upload_class = new Upload_Files;

if ($_POST['action']=='submit'){
	
	$upload_class->temp_file_name = trim($_FILES['upload']['tmp_name']);
	$upload_class->file_name = trim(strtolower($_FILES['upload']['name']));
	
	$upload_class->upload_dir = $htdocsdir."aboutus/".$_GET['class']."/pictures/";
	$upload_class->upload_log_dir = $htdocsdir."aboutus/".$_GET['class']."/pictures/";
	$upload_class->max_file_size = 1024000;
	$upload_class->banned_array = array("");
	$upload_class->ext_array = array(".jpg",".jpeg");
	
	$valid_ext = $upload_class->validate_extension();
	$valid_size = $upload_class->validate_size();
	$valid_user = $upload_class->validate_user();
	$max_size = $upload_class->get_max_size();
	$file_size = $upload_class->get_file_size();
	
	
	if (!$valid_ext) {
		$result = "The file extension is invalid, please try again!";
	}elseif (!$valid_size) {
		$result = "The file size is invalid, please try again! The maximum file size is: $max_size and your file was: $file_size";
	}elseif (!$valid_user) {
		$result = "You have been banned from uploading to this server.";
	} else {
		$file=$_POST['id'].'.jpg';
		$upload_file = $upload_class->upload_file_with_validation($file);
		
		if (!$upload_file) {
			$result = "Your file could not be uploaded!";
		} else {
			$result = "Your file has been successfully uploaded to the server.";
		}
	}
	
	
	if ($result != "Your file has been successfully uploaded to the server."){
		echo $result;
		
		$upload_class->bod_uploadform($_POST['path'],$_POST['status']);
		
	}else{
		
		$src_img = imagecreatefromjpeg($upload_class->upload_dir.$file);
		
		if (imagesx($src_img) > imagesy($src_img)){
			$aspect=imagesx($src_img);
			
		}else{
			$aspect=imagesy($src_img);
		}

		if ($aspect>'150'){
        	$aspect=150;
		}
		if (imagesx($src_img) > imagesy($src_img)){
			$new_w = $aspect;
			$new_h = imagesy($src_img)*($aspect/imagesx($src_img));
		}else{
			$new_w = imagesx($src_img)*($aspect/imagesy($src_img));
			$new_h = $aspect;
		}
		$dst_img = imagecreatetruecolor($new_w,$new_h);
		imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
		imagejpeg($dst_img, $upload_class->upload_dir.'t'.$file);
		
		
		echo $result;
	}
		
		
		
		
	
	
	
}else{
	if (isset($_GET['id'])){
		$upload_class->bod_uploadform($_GET['id']);
	}else{
		echo 'Error. Unknown file.';
	}
	
}
@ page_close();
include_once($htdocsdir.'includes/bottom.php');
?>
