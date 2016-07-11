<?PHP

require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');
?>
<table width=100% height=100%><tr><td width=100>
<table border=0 cellpadding=0 cellspacing=0 width="100%">
<tr><td align="center" valign="top"><br>
<table border=0 width=95% cellspacing=2 cellpadding=0>
<TR><TD align="left" nowrap><span class="text13blue">Menu<br>
<a href='newsletter.php?action=show_users&amp;confirmed=1'>Confirmed users</a><br>
<a href="newsletter.php?action=show_users&amp;confirmed=0">Unconf. users</a><br>
<a href="newsletter.php?action=new_user">New user</a><br>
<a href="newsletter-files.php?">Manage Files</a><br></span>
</td></tr></table>
</td><td>
<?

class newsletter_upload extends Upload_Files {
	function uploadform(){
		?>
		<SCRIPT LANGUAGE="JavaScript">
		function disableForm(theform) {
			if (theform.month.value ==''){
				alert('You must select a month!');
				return false;
			}
			if (theform.years.value == ''){
				alert('You must select a year!');
				return false;
			}
			if (theform.upload.value == ''){
				alert('You must select a file!');
				return false;
			}
			var month, year, number;
			
			month = theform.month.options[theform.month.selectedIndex].text;
			year = theform.years.value;
			number = theform.fileno.value;
			
			if (confirm('Upload newsletter ' + number + ' for ' + month + ', ' + year + '?')){
			
				for (i = 0; i < theform.length; i++) {
					var tempobj = theform.elements[i];
					if (tempobj.type.toLowerCase() == "submit"){
						tempobj.value = 'Sending';
					}
					if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset"){
						tempobj.disabled = true;
					}

				}
				return true;	
			}else{
				return false;
				
			}
		}

		</SCRIPT>
		<?

		$this->form->add_element(array("type"=>"file",
		"name"=>"upload",
		'MAX_FILE_SIZE'=>''));

		$this->form->add_element(array("type"=>"submit",
		"name"=>"Submit",
		'value'=>'Upload'));

		$this->form->add_element(array("type"=>"hidden",
		"name"=>"action",
		"value"=>"submit"));

		global $days,$months,$years;

		$this->form->add_element(array("type"=>"select",
		"name"=>"month",
		"value"=>$month,
		"options"=>$months
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"days",
		"value"=>$day,
		"options"=>$days
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"years",
		"value"=>$year,
		"options"=>$years
		));
		$this->form->add_element(array("type"=>"select",
		"name"=>"fileno",
		"options"=>array(
			array('value'=>'','label'=>'1'),
			array('value'=>'2','label'=>'2'),
			array('value'=>'3','label'=>'3'),
			array('value'=>'4','label'=>'4'))
		));


		$this->form->start('disableForm','POST','newsletter-files.php?function=upload',"' onSubmit='return disableForm(this);'",'form');
		?>

		<table class="listing">

		<tr><td style="text-align: right">Newsletter Date:<br>(mm/yyyy)</td><td style="text-align: left">
		<?
			$this->form->show_element('month');
			$this->form->show_element('years');
			$this->form->show_element('fileno');

		?>
		<tr><td style="text-align: right">Newsletter File:</td><td style="text-align: left">

		<?
			$this->form->show_element('upload');
		?>
		<br>
		<font style="font-size:8pt;">Notes:<br>
		- file must be in pdf<br>
		-<b>Do not use Safari to do uploads.  It will time out after 1 minute, even though the upload processing may take longer</b><br>
		</font></td></tr>
		<tr><td>&nbsp;</td><td style="text-align: left">
		<?
		$this->form->show_element('action');
		$this->form->show_element('id');

		$this->form->show_element('Submit');
		echo '</td></tr></table>';
		$this->form->finish();
	}
}

function listing(){
	global $htdocsdir;
		$d = dir($htdocsdir."newsletter/");
		
		echo '<table width="100%"><tr>';
		
		$start_year=2003;
		
		$i=1;
		if (date('m')>6){
			$nyear=date('Y')+1;
		}else{
			$nyear=date('Y');
		}
		
		if (isset($_GET['nyear'])){
			$year=$_GET['nyear'];
		}else{
			$year=$nyear;
		}
		
		for ($y=$nyear; $y>=$start_year; $y--){
			
			if ($_GET['nyear']!=$y && (($_GET['nyear']=='' && $nyear!=$y) || ($_GET['nyear']!=''))){
				echo sprintf('<td><a href="%s?nyear=%s">%s-%s</a></td>',$_SERVER['PHP_SELF'],$y,$y-1,substr($y,-2));
			}else{
				echo sprintf('<td>%s-%s</td>',$y-1,substr($y,-2));
			}
						$i++;
			if ($i==10){
				echo '</tr><tr>';
				$i=1;
			}
		}
		
		echo '</tr></table>';
		
		$start_date=mktime(0,0,0,6,31,$year-1)-1;
		$end_date=mktime(0,0,0,7,3,$year)-1;
		
		while($entry=$d->read()) {
			if (substr($entry,0,1)!="." && substr($entry,-3,3)=='pdf') {
				$dt_and_tm=mktime(0,0,0,substr($entry,5,2),1,substr($entry,0,4));
				$link=date("F Y",$dt_and_tm);
				
				if (substr($entry,7,1)=='-'){
					$link.='('.substr($entry,8,1).')';
				}

				if ($dt_and_tm > $start_date && $dt_and_tm < $end_date){
					$arry[$entry]= "<a href='/newsletter/$entry'>".$link."</a><br>\n";
				}

			}
		}
		if (is_array($arry)){
			arsort ($arry);
		}
		$i=1;
		?>

		<table width=300>
		<tr><td></td><td> Month </td><td></td></tr>
		<?
		foreach ($arry as $key => $val) {
			$color = ($color=='dark') ? 'light' : 'dark';

		    echo sprintf('<tr class="%s"><td><a href="?function=change&amp;id=%s"></a></td><td>%s</td><td><a href="javascript:confirmDelete(\'%s\',\'?function=delete&amp;id=%s\');">Delete</a></td></tr>',$color, $key, $val,$key,$key);

		    $dt_and_tm=mktime(0,0,0,substr($key,5,2),1,substr($key,0,4));
				$link=date("F Y",$dt_and_tm);
		    $months[] = array('label'=>$link, 'value'=>$link);
		}
		echo '<tr><td></td><td><a href="newsletter-files.php?function=upload">Add Newsletter</a></td></tr></table>';
 		$d->close();
 		/*
 		$form= new form();
 		$form->add_element(array("type"=>"select",
			"name"=>"month",
			"options"=>$months
			));

			$form->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"submit"));

			$form->add_element(array("type"=>"submit",
			"name"=>"Submit",
			'value'=>'Send'));
			$form->start('','POST',$_SERVER['PHP_SELF'].'?function=send');

			?>

		<table class="listing">

		<tr><td style="text-align: right">Send Posting Notification for:<br></td><td style="text-align: left">
		<?
			$form->show_element('month');
			$form->show_element('action');

			$form->show_element('Submit');
			echo '</td></tr></table>';
			$form->finish();
		*/

}
switch ($_GET['function']){
	case 'upload':
		$upload_class = new newsletter_upload();

		if ($_POST['action']=='submit'){

			$upload_class->temp_file_name = trim($_FILES['upload']['tmp_name']);
			$upload_class->file_name = trim(strtolower($_FILES['upload']['name']));

			$upload_class->upload_dir = $htdocsdir."newsletter/";
			$upload_class->upload_log_dir = $htdocsdir."newsletter/";
			$upload_class->max_file_size = 5242880;
			$upload_class->banned_array = array("");
			$upload_class->ext_array = array(".pdf");

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
			} elseif ($_POST['years']=='' || $_POST['month'] ==''){
				$result = '<B>You must set a date and re-upload.</b><br>';
			}else{
				
				if ($_POST['fileno']!=''){
					$file=sprintf('%04s-%02s-%s.pdf',$_POST['years'],$_POST['month'],$_POST['fileno']);
				}else{
					$file=sprintf('%04s-%02s.pdf',$_POST['years'],$_POST['month']);
				}
				$upload_file = $upload_class->upload_file_with_validation($file);

				if (!$upload_file) {
					$result = "Your file could not be uploaded!";
				} else {
					$result = "Your file has been successfully uploaded to the server.";
				}
			}


			if ($result != "Your file has been successfully uploaded to the server."){
				echo $result;

				$upload_class->uploadform();

			}else{

				echo $result;
				$newsletter=date('M Y',mktime(0,0,0,$_POST['month'],1,$_POST['years']));
				logit(sprintf('The %s newsletter has been posted',$newsletter));

	$body=sprintf('The %s newsletter has been posted on the O5PBA website.

Please goto the following link  -  http://www.o5pba.ca/newsletter/


O5 Newsletter Admin

To leave the list, please go to http://www.o5pba.ca/newsletter/', $newsletter);
	$subject=sprintf('%s Newsletter Posted', $newsletter);

	$db=new DB();
	$db->query('select * from mail where confirm= 1 order by email');
	echo '<br><Br>Sending to: <br><br>';
	flush();


	while ($db->next_record()){

		echo $db->f('name').', '.$db->f('email');
		if (mail($db->f('email'),$subject,$db->f('name')."\n\n".$body,"From: newsletter@o5pba.ca\r\n")){
			echo '....ok.<br>';
		}else{
			echo '....failed.<br>';
		}
		
		flush();
	}

echo $body;
	echo 'Finished sending notifications for '. $newsletter.' newsletter';
	$update=new update();
	$update->dowhatsnew();
			}

		}else{
			$upload_class->uploadform();

		}
	break;
	case 'change':
		if ($_POST['action']=='submit'){
			$path=$htdocsdir."newsletter/";
			rename($path.$_POST['id'], $path.sprintf('%04s-%02s.pdf',$_POST['years'],$_POST['month']));
			listing();
		}else{
			$year=substr($_GET['id'],0,4);
			$month=substr($_GET['id'],5,2);

			$form=new form();
			$form->add_element(array("type"=>"select",
			"name"=>"month",
			"value"=>$month,
			"options"=>$months
			));
			$form->add_element(array("type"=>"select",
			"name"=>"days",
			"value"=>$day,
			"options"=>$days
			));
			$form->add_element(array("type"=>"select",
			"name"=>"years",
			"value"=>$year,
			"options"=>$years
			));

			$form->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"submit"));
			$form->add_element(array("type"=>"hidden",
			"name"=>"id",
			"value"=>$_GET['id']));

			$form->add_element(array("type"=>"submit",
			"name"=>"Submit",
			'value'=>'Rename'));

			$form->start('','POST','newsletter.php?function=change');

			?>

		<table class="listing">

		<tr><td style="text-align: right">Newsletter Date:<br>(mm/yyyy)</td><td style="text-align: left">
		<?
			$form->show_element('month');
			$form->show_element('years');

			$form->show_element('action');
			$form->show_element('id');

			$form->show_element('Submit');
			echo '</td></tr></table>';
			$form->finish();

		}
	break;
	case 'delete':

		if (unlink($htdocsdir."newsletter/".$_GET['id'])){
			echo 'File Deleted';
			logit(sprintf('The %s newsletter has been deleted',$_GET['id']));
		}
		listing();
	break;
	default:

		listing();




	break;
}
@ page_close();
echo '</table></table>';
include_once($htdocsdir.'admin/includes/bottom.php');
?>
