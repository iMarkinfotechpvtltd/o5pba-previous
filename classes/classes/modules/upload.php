<?php
class Upload_Files {
	
	var $temp_file_name;								//Declare the temp directory
	var $file_name;										//Declare the file name
	var $upload_dir;										//Declare the upload directory
	var $upload_log_dir;									//Declare the upload log directory
	var $max_file_size;									//Declare the max file size
	var $banned_array;									//Declare the banned array
	var $ext_array;										//Declare the extensions array
	var $form;
	
	function Upload_Files(){
		$this->form=new form;
	}
	
	
	/***************************************************************************
	*									Function: validate_extension()
	****************************************************************************
	Instructions: This function is used to validate the file extension of the file that is trying to be
	uploaded. You can call it by itself, or never call it, and just call the upload_file_with_validate()
	function and it will call it for you. If this functon is going to be called by either you or from
	within, then the file_name variable must be passed or the outcome will return false.<br>
	****************************************************************************
	Variables Required: $file_name, $ext_array
	****************************************************************************/
	function validate_extension() {											//Start Validate Extension Function
	
	$file_name = trim($this->file_name);								//Trim File Name
	$extension = strtolower(strrchr($file_name,"."));					//Get the file extension
	$ext_array = $this->ext_array;										//Declare Extension Array
	$ext_count = count($ext_array);									//Count the number of elements
	if ($file_name) {														//If file name is present, continue
	if (!$ext_array) {													//If no extensions found
	return true;													//Return true
	} else {															//Else
	foreach ($ext_array as $key => $value) {					//Start extension loop
	$first_char = substr($value,0,1);						//Get first character
	if ($first_char <> ".") {								//If not a period,
	$extensions[] = ".".strtolower($value);			//Write value with a period to a new array
	} else {												//Else
	$extensions[] = strtolower($value);				//Write the value to a new array
	}
	}
	foreach ($extensions as $key => $value) {				//Start extract loop of valid extensions
	if ($value == $extension) {								//If extension is equal to any in the array
	$valid_extension = "TRUE";							//Set valid extension to TRUE
	}
	}
	if ($valid_extension) {										//Check to see if extension is valid
	return true;												//Return true if it is
	} else {														//Else
	return false;												//Return false
	}
	}
	} else {																//Else
	return false;														//Return False
	}
	}
	
	
	/***************************************************************************
	*										Function: validate_size()
	****************************************************************************
	Instructions: This function is used to validate the file's size. You can call it by itself, or never
	call it, and just call the upload_file_with_validate() function and it will call it for you. If this
	function is going to be called by either you or from within, then the temp_file_name and the
	max_file_size variables must be passed or the outcome will return false.
	****************************************************************************
	Variables Required: $temp_file_name, $max_file_size
	****************************************************************************/
	function validate_size() {											//Start Validate File Size Array
	$temp_file_name = trim($this->temp_file_name);			//Trim Temp File Name
	$max_file_size = trim($this->max_file_size);					//Trim Max File Size
	if ($temp_file_name) {											//If file is present
	$size = filesize($temp_file_name);							//Get the size of the file
	if ($size > $max_file_size) {								//Set over limit statement
	return false;											//Set to false for over the limit
	} else {													//Else
	return true;											//Set to true for under limit
	}
	} else {															//Else
	return false;													//Return false
	}
	
	}
	
	/***************************************************************************
	Function: existing_file()
	****************************************************************************
	Instructions: This function will check to see if the file exists. If the file already exists on the
	server in the upload directory, the function returns true, otherwise it returns false. No renaming
	conventions where added because everyone has there own renaming techniques. You should
	honestly come up with a system for systematically naming your files on the server. Such as
	dropping the file name and adding the id number from the database to it and keeping the file
	extension. You should pass unique names into the class, but if you don't it will be caught and
	the file will not get uploaded.
	****************************************************************************
	Variables Required: $file_name, $upload_dir
	****************************************************************************/
	function existing_file() {
		$file_name = trim($this->file_name);				//Extract the file name
		$upload_dir = $this->get_upload_directory();		//Extract the upload directory
		
		if ($upload_dir == "ERROR") {						//If directory not found
		return false;										//Return false
		} else {												//Else
		$file = $upload_dir . $file_name;				//Set file and file path
		if (file_exists($file)) {							//If file exists
		unlink( $upload_dir . $file_name);
		return false;									//delete it and Return false
		} else {											//Else
		return false;									//Return false
		}
		}
	}
	
	/***************************************************************************
	*										Function: get_file_size()
	****************************************************************************
	Instructions: This function is used by the class when either upload_file function is called. You
	may also call the function on your own to set the file's size to a variable and then use it on
	your page. Whether you use this function or not, the temp_file_name variable must be passed
	in order to upload files.
	****************************************************************************
	Variables Required: $temp_file_name
	****************************************************************************/
	function get_file_size() {												//Start get file size function
	$temp_file_name = trim($this->temp_file_name);				//Trim Temp File Name
	$kb = 1024;															//Set KB
	$mb = 1024 * $kb;													//Set MB
	$gb = 1024 * $mb;													//Set GB
	$tb = 1024 * $gb;													//Set TB
	if ($temp_file_name) {											//If temp file name is present
	$size = filesize($temp_file_name);							//Get the file's size
	if ($size < $kb) {											//If file's size is less than 1 KB
	$file_size = "$size Bytes";							//Set file_size in bytes, if applicable
	}
	elseif ($size < $mb) {									//If file's size is less than 1 MB
	$final = round($size/$kb,2);						//Find final size
	$file_size = "$final KB";								//Set file_size in kilo-bytes, if applicable
	}
	elseif ($size < $gb) {									//If file's size is less than 1 GB
	$final = round($size/$mb,2);						//Find final size
	$file_size = "$final MB";								//Set file_size in mega-bytes, if applicable
	}
	elseif($size < $tb) {										//If file's size is less than 1 TB
	$final = round($size/$gb,2);						//Find final size
	$file_size = "$final GB";								//Set file_size in giga-bytes, if applicable
	} else {
		$final = round($size/$tb,2);							//Else find final size in TB
		$file_size = "$final TB";								//Set file_size in tera-bytes, if applicable
	}
	} else {
		$file_size = "ERROR: NO FILE PASSED TO get_file_size()";
	}
	return $file_size;
	}
	
	/***************************************************************************
	*										Function: get_max_size()
	****************************************************************************
	Instructions: This function will only be used if you call it. You can call the function from the class
	and assign it to a variable so that you can display the actual maximum file size on your web
	site. If you call this function you must pass the max_file_size or the function will return an error.
	****************************************************************************
	Variables Required: $max_file_size
	****************************************************************************/
	function get_max_size() {											//Start get max file size function
	$max_file_size = trim($this->max_file_size);					//Trim Max File Size
	$kb = 1024;														//Set KB
	$mb = 1024 * $kb;												//Set MB
	$gb = 1024 * $mb;												//Set GB
	$tb = 1024 * $gb;												//Set TB
	if ($max_file_size) {
		if ($max_file_size < $kb) {									//If file's size is less than 1 KB
		$max_file_size = "max_file_size Bytes";				//Set file_size in bytes, if applicable
		}
		elseif ($max_file_size < $mb) {								//If file's size is less than 1 MB
		$final = round($max_file_size/$kb,2);					//Find final size
		$max_file_size = "$final KB";							//Set file_size in kilo-bytes, if applicable
		}
		elseif ($max_file_size < $gb) {								//If file's size is less than 1 GB
		$final = round($max_file_size/$mb,2);					//Find final size
		$max_file_size = "$final MB";							//Set file_size in mega-bytes, if applicable
		}
		elseif($max_file_size < $tb) {								//If file's size is less than 1 TB
		$final = round($max_file_size/$gb,2);					//Find final size
		$max_file_size = "$final GB";							//Set file_size in giga-bytes, if applicable
		} else {
			$final = round($max_file_size/$tb,2);					//Else find final size in TB
			$max_file_size = "$final TB";							//Set file_size in tera-bytes, if applicable
		}
	} else {
		$max_file_size = "ERROR: NO SIZE PARAMETER PASSED TO  get_max_size()";
	}
	return $max_file_size;
	
	}
	
	/***************************************************************************
	*										Function: validate_user()
	****************************************************************************
	Instructions: This function is used by the class when you call the upload_file_with_validation
	function, or you can just call this function. This function will check to see if the user that is
	trying to upload a file is in your banned users array list. If they are then they are not permitted
	to upload files. If you call either this function or the upload_file_with_validation you will need
	to pass the banned_array or the outcome will return false. If there are no users on your banned
	list, then you can either send an empty array or not send the variable.
	****************************************************************************
	Variables Required: $banned_array
	****************************************************************************/
	function validate_user() {												//Start the validate user funciton
	$banned_array = $this->banned_array;							//Get banned array
	$ip = trim($_SERVER['REMOTE_ADDR']);							//Get IP Address
	$cpu = gethostbyaddr($ip);
	$count = count($banned_array);									//Count the number of banned users
	if ($count < 1) {													//Are there users in the list???
	return true;													//If not user is valid, if so check em!
	} else {
		foreach($banned_array as $key => $value) {				//Start extraction of banned users from the array
		if ($value == $ip ."-". $cpu) {							//If the user's IP address is found in list, continue
		return false;											//Function returns false if user is on list
		} else {													//Else
		return true;											//the function returns true
		}
		}
	}
	}
	
	/***************************************************************************
	*									Function: get_upload_directory()
	****************************************************************************
	Instructions: This function was written to be used internally, but it can be called to be assigned
	to a variable. Whether or not you call this function directly or not you are required to pass the
	upload_dir variable. If the directory is invalid the $upload_dir will return "ERROR".
	****************************************************************************
	Variables Required: $upload_dir
	****************************************************************************/
	function get_upload_directory() {									//Start Upload Directory Function
	$upload_dir = trim($this->upload_dir);							//Trim Upload Directory
	if ($upload_dir) {													//If upload directory is present
	$ud_len = strlen($upload_dir);								//Get upload directory size
	$last_slash = substr($upload_dir,$ud_len-1,1);			//Get Last Character
	if ($last_slash <> "/") {									//Check to see if the last character is a slash
	$upload_dir = $upload_dir."/";						//Add a backslash if not present
	} else {													//Else
	$upload_dir = $upload_dir;							//If backslash is present, do nothing
	}
	$handle = @opendir($upload_dir);						//Check to see if directory exists
	if ($handle) {
		$upload_dir = $upload_dir;						//Yes it exists
		closedir($handle);								//Close the directory
	} else {
		echo $upload_log_dir.' does not exist...  Creating <br>';
		#$upload_dir = "ERROR";							//No it does not exist
		mkdir($this->upload_dir, 07750);
	}
	} else {															//Else
	$upload_dir = "ERROR";										//Make Upload directory blank
	}
	return $upload_dir;												//Return Upload Directory
	}
	
	/***************************************************************************
	*									Function: get_upload_log_directory()
	****************************************************************************
	Instructions: This function was written to be used internally, but it can be called to be assigned
	to a variable. Whether or not you call this function directly or not you are required to pass the
	upload_log_dir variable. If the directory is invalid the $upload_log_dir will return "ERROR".
	****************************************************************************
	Variables Required: $upload_log_dir
	****************************************************************************/
	function get_upload_log_directory() {									//Start Upload Log Directory Function
	$upload_log_dir = trim($this->upload_log_dir);					//Trim Upload Log Directory
	if ($upload_log_dir) {													//If upload log directory is present
	$ud_len = strlen($upload_log_dir);								//Get upload log directory size
	$last_slash = substr($upload_log_dir,$ud_len-1,1);			//Get Last Character
	if ($last_slash <> "/") {										//Check to see if the last character is a slash
	$upload_log_dir = $upload_log_dir."/";					//Add a backslash if not present
	} else {														//Else
	$upload_log_dir = $upload_log_dir;						//If backslash is present, do nothing
	}
	$handle = @opendir($upload_log_dir);						//Check to see if directory exists
	if ($handle) {
		$upload_log_dir = $upload_log_dir;					//Yes it exists
		closedir($handle);									//Close the directory
	} else {
		
		$upload_log_dir = "ERROR";							//No it does not exist
	}
	} else {																//Else
	$upload_log_dir = "ERROR";										//Make Upload log directory blank
	}
	return $upload_log_dir;												//Return Upload Log Directory
	}
	
	/***************************************************************************
	*									Function: upload_file_no_validation()
	****************************************************************************
	Instructions: This function is used to actually upload the file to the server without any kind
	of file or user validation. If this function is called, the file is not checked for size, extension, or
	if the user is banned or not, the file is just uploaded to the correct upload directory, and a log
	is written about the file. You must pass the following variables: temp_file_name, file_name,
	upload_dir, and upload_log_dir. If these variables are not passed the file will not be uploaded
	and the outcome will return false.
	****************************************************************************
	Variables Required: $temp_file_name, $file_name, $upload_dir, $upload_log_dir
	****************************************************************************/
	function upload_file_no_validation() {
		$temp_file_name = trim($this->temp_file_name);			//Trim Temp File Name
		$file_name = trim(strtolower($this->file_name));				//Trim File Name
		$upload_dir = $this->get_upload_directory();					//Trim Upload Directory
		$upload_log_dir = $this->get_upload_log_directory();			//Trim Upload Log Directory
		$file_size = $this->get_file_size();								//Get File Size
		$ip = trim($_SERVER['REMOTE_ADDR']);						//Get IP Address
		$cpu = gethostbyaddr($ip);
		$m = date("m");													//Get month
		$d = date("d");													//Get day
		$y = date("Y");													//Get year
		$date = date("m/d/Y");											//Get today's date
		$time = date("h:i:s A");											//Get now's time
		
		if (($upload_dir == "ERROR") OR ($upload_log_dir == "ERROR")) {		//Check to see if directories exist
		return false;
		} else {
			if (is_uploaded_file($temp_file_name)) {								//Check if file is uploaded
			if (move_uploaded_file($temp_file_name,$upload_dir . $file_name)) {
				$log = $upload_log_dir.$y."_".$m."_".$d.".txt";					//Log File Name
				$fp = fopen($log,"a+");											//Set File Pointer
				fwrite($fp,"\n$ip-$cpu | $file_name | $file_size | $date | $time");		//Write File
				fclose($fp);														//Close File Pointer
				return true;														//Return true after upload and written log
			} else {																//Else
			return false;														//Return false
			}
			} else {																	//Else
			return false;															//Return false
			}
		}
	}
	
	/***************************************************************************
	*								Function: upload_file_with_validation()
	****************************************************************************
	Instructions: This function will do the same as above except it will validate every aspect of the
	file. If verfies the file's size, type, and if the user is on the banned list or not. If any of these
	three parameters are not met, the file is not uploaded and the outcome returns false. If it
	passes all the verification processes then the file is uploaded to the correct folder on the server,
	a log is written, and the outcome returns true. You must pass all the variables if you call this
	function.
	****************************************************************************
	Variables Required: $temp_file_name, $file_name, $upload_dir, $upload_log_dir, $banned_array
	$ext_array, $max_file_size
	****************************************************************************/
	function upload_file_with_validation($outname='') {
		$temp_file_name = trim($this->temp_file_name);			//Trim Temp File Name
		$file_name = trim(strtolower($this->file_name));				//Trim File Name
		$upload_dir = $this->get_upload_directory();					//Trim Upload Directory
		$upload_log_dir = $this->get_upload_log_directory();			//Trim Upload Log Directory
		$file_size = $this->get_file_size();								//Get File Size
		$ip = trim($_SERVER['REMOTE_ADDR']);						//Get IP Address
		$cpu = gethostbyaddr($ip);
		$m = date("m");													//Get month
		$d = date("d");													//Get day
		$y = date("Y");													//Get year
		$date = date("m/d/Y");											//Get today's date
		$time = date("h:i:s A");											//Get now's time
		$valid_user = $this->validate_user();							//Validate the user
		$valid_size = $this->validate_size();							//Validate the size
		$valid_ext = $this->validate_extension();						//Validate the extension
		$existing_file = $this->existing_file();							//File Existing
		
		
		if (($upload_dir == "ERROR") OR ($upload_log_dir == "ERROR")) {
			return false;
		}
		if ((((!$valid_user) OR (!$valid_size) OR (!$valid_ext) OR ($existing_file)))) {
			return false;
		} else {
			if (is_uploaded_file($temp_file_name)) {								//Check if file is uploaded
			if ($outname!=''){
				$file_name=$outname;
				$logname=$this->file_name.'->'.$outname;	
			}else{
				$logname=$this->file_name;	
			}
			if (move_uploaded_file($temp_file_name,$upload_dir . $file_name)) {
				
				$log = $upload_log_dir.$y."_".$m."_".$d.".txt";				//Log File Name
				$fp = fopen($log,"a+");											//Set File Pointer
				fwrite($fp,"\n$ip-$cpu | $logname | $file_size | $date | $time");		//Write File
				fclose($fp);														//Close File Pointer
				return true;														//Return true after upload and written log
			} else {																//Else
			return false;														//Return False
			}
			} else {																	//Else
			return false;															//Return False
			}
		}
	}
	function uploadform($path='',$status='results'){
		?>
		<SCRIPT LANGUAGE="JavaScript">
		function disableForm(theform) {
			if (document.all || document.getElementById) {
				for (i = 0; i < theform.length; i++) {
					var tempobj = theform.elements[i];
					if (tempobj.type.toLowerCase() == "submit"){
						tempobj.value = 'Sending';
					}
					if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset"){
						tempobj.disabled = true;
					}
					
				}
			}
			return true;
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
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"status",
		"value"=>$status));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"path",
		"value"=>$path));
		
		$this->form->start('disableForm','POST','batch.php',"' onSubmit='return disableForm(this);'",'form');
		echo '<table class="listing">';
		echo '<tr><td style="text-align: right">Image Zip file:</td><td style="text-align: left">';
		$this->form->show_element('upload');
		echo '<br><font style="font-size:8pt;">Notes:<br>-5mb Max Size!<br>-Jpeg images only.<br>-<b>Do not use Safari to do batch uploads.  It will time out after 1 minute, even though the upload processing may take longer</b><br>-<b>This will OVERWRITE any existing files with same name as files in archive.<br><br>You have been warned</b></font></td></tr>';
		echo '<tr><td>&nbsp;</td><td style="text-align: left">';
		$this->form->show_element('action');
		$this->form->show_element('status');
		$this->form->show_element('path');
		
		$this->form->show_element('Submit');
		echo '</td></tr></table>';
		$this->form->finish();
		
}
	function hof_uploadform($id=''){
		?>
		<SCRIPT LANGUAGE="JavaScript">
		function disableForm(theform) {
			if (document.all || document.getElementById) {
				for (i = 0; i < theform.length; i++) {
					var tempobj = theform.elements[i];
					if (tempobj.type.toLowerCase() == "submit"){
						tempobj.value = 'Sending';
					}
					if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset"){
						tempobj.disabled = true;
					}
					
				}
			}
			return true;
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
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id));
		
		$this->form->start('disableForm','POST','hofpic.php',"' onSubmit='return disableForm(this);'",'form');
		echo '<table class="listing">';
		echo '<tr><td style="text-align: right">Image file:</td><td style="text-align: left">';
		$this->form->show_element('upload');
		echo '<br><font style="font-size:8pt;">Notes:<br>- image should be around 150x150 px<br>-Jpeg format only.<br>-<b>Do not use Safari to do batch uploads.  It will time out after 1 minute, even though the upload processing may take longer</b><br>-<b>This will OVERWRITE existing image.<br><br>You have been warned</b></font></td></tr>';
		echo '<tr><td>&nbsp;</td><td style="text-align: left">';
		$this->form->show_element('action');
		$this->form->show_element('id');
		
		$this->form->show_element('Submit');
		echo '</td></tr></table>';
		$this->form->finish();
		
}
	function bod_uploadform($id=''){
		?>
		<SCRIPT LANGUAGE="JavaScript">
		function disableForm(theform) {
			if (document.all || document.getElementById) {
				for (i = 0; i < theform.length; i++) {
					var tempobj = theform.elements[i];
					if (tempobj.type.toLowerCase() == "submit"){
						tempobj.value = 'Sending';
					}
					if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset"){
						tempobj.disabled = true;
					}
					
				}
			}
			return true;
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
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id));
		
		$this->form->start('disableForm','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return disableForm(this);'",'form');
		echo '<table class="listing">';
		echo '<tr><td style="text-align: right">Image file:</td><td style="text-align: left">';
		$this->form->show_element('upload');
		echo '<br><font style="font-size:8pt;">Notes:<br>- image should be around 150x150 px<br>-Jpeg format only.<br>-<b>Do not use Safari to do batch uploads.  It will time out after 1 minute, even though the upload processing may take longer</b><br>-<b>This will OVERWRITE existing image.<br><br>You have been warned</b></font></td></tr>';
		echo '<tr><td>&nbsp;</td><td style="text-align: left">';
		$this->form->show_element('action');
		$this->form->show_element('id');
		
		$this->form->show_element('Submit');
		echo '</td></tr></table>';
		$this->form->finish();
		
}
}
?>