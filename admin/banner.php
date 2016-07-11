<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

function mycopy($in, $out){
	$src_img = imagecreatefromjpeg($in);
	if ($src_img) {
		if (imagesx($src_img)>150){
			$new_w = 150;
			$new_h = imagesy($src_img)*(150/imagesx($src_img));
			
			$dst_img = imagecreatetruecolor($new_w,$new_h);
			imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
		}else{
			$dst_img=$src_img;
		}
		imagejpeg($dst_img, $out);
	}
	
}

if (isset($_POST['submit'])){
	if (is_uploaded_file($_FILES['image1']['tmp_name'])){
		mycopy($_FILES['image1']['tmp_name'], $htdocsdir.'image1.jpg');
		echo 'Banner 1 added.<br/>';
      echo '<img src="../image1.jpg"/><br/>';
		logit('Changed front page banner 1');
	}
	if (is_uploaded_file($_FILES['image2']['tmp_name'])){
		mycopy($_FILES['image2']['tmp_name'], $htdocsdir.'image2.jpg');
		echo 'Banner 2 added.<br>';
      echo '<img src="../image2.jpg"/><br/>';
		logit('Changed front page banner 2');
	}
}elseif (isset($_POST['remove1'])){
	
	@ unlink($htdocsdir.'image1.jpg');
	echo 'Banner 1 removed.';
	
}elseif (isset($_POST['remove2'])){
	
	@ unlink($htdocsdir.'image2.jpg');
	echo 'Banner 2 removed.';
	
}else{
	?>
	<br><font style="font-size:8pt;">Notes:<br>-image should be 150 px wide max, if larger it will be reduced making it grainy<br>-Jpeg images only.</b></font>
	<br>
	<br>
	<form method="POST" enctype='multipart/form-data' >
	<table>
	<tr><td width='100'>Top banner:</td>
	<td>
	
	<input type='file' name='image1' size='30'> 
	<?
	if (file_exists($htdocsdir.'image1.jpg')){
		echo '<input type="submit" name="remove1" value="Remove"><br/>';
      echo '<img src="../image1.jpg"/><br/>';
	}
	?>
	</td></tr>
	</table>
	
	<br><br><br>
	<table>
	<tr><td width='100'>Bottom banner:</td>
	<td>
	
	<input type='file' name='image2' size='30'> 
	<?
	if (file_exists($htdocsdir.'image2.jpg')){
		echo '<input type="submit" name="remove2" value="Remove"><br/>';
      echo '<img src="../image2.jpg"/><br/>';
	}
	?>
	</td></tr>
	<tr><td>&nbsp;</td>
	<td><input type='submit' name='submit' value='Submit'></td></table>
	</form>
	<?
}


include('./includes/bottom.php');
page_close();

?>
