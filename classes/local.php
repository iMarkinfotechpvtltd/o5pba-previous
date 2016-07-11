<?php
//for Logging

/**
 * This is date (relative to today) of the oldest item to display in the main changes history.  If 0 
 * history items will not be limited by date.  The date is a positive duration expressed in epoch seconds.
 */
$oldestDateToShowHistory = 30 * 24 * 60 * 60;
/**
 * This is the maximum number of items to display in the changes history on the main page.  If 0
 * all history items that pass other filters will be shown.
 */
$maxDaysToShowHistory = 10;
/**
 * The path to the average.csv file, used to determine when the averages book was last modified.
 */
$averagesPath = $htdocsdir.'average.csv';

function logit($what){
	$db = new DB;
	global $auth;
	$db->query(sprintf('INSERT INTO _transaction VALUES("%s","%s","%s")',time(),$auth->auth['uname'],$what));
	return $what;
}

function hasmodule($name){
	$db=new DB;
	$db->query(sprintf('SELECT * FROM _modules where name = "%s"',md5($db->Database.'-'.$name)));
	return $db->num_rows();
}

function linkit($url, $text){
	global $webpath;
	$temp=substr($url,-10);
	while (strpos($temp,'.')){
		$temp = strtolower(substr($temp,strpos($temp,".")+1));
	}
	$url_arr = explode ("/", $url);
	$url_arr[count($url_arr)-1] = urlencode($url_arr[count($url_arr)-1]);
	$url_new = implode("/", $url_arr);
	$extension=$temp;
	$result=sprintf('<a href="%s">%s',str_replace("+", "%20", $url_new),$text);
	
	$accepted_types = array("gif"=>"image.gif", "jpg"=>"image.gif","txt"=>"txt.gif");
	
	
	//if($accepted_types[$extension]){
		
		//$result.=sprintf ('<img src="%s/fileicons/%s" border=0>',$webpath,$accepted_types[strtolower($extension)]);
		
	//}
	return $result.'</a>';
}
//function scandir ($directory, $sorting_order = 0){
//        if (!is_string($directory)) {
 //           trigger_error('scandir() expects parameter 1 to be string, ' . gettype($directory) . ' given', E_USER_WARNING);
  //          return null;
  //      }
//
//        if (!is_int($sorting_order)) {
//            trigger_error('scandir() expects parameter 2 to be long, ' . gettype($sorting_order) . ' given', E_USER_WARNING);
//            return null;
 //       }
//
 //       if (!is_dir($directory) || (false === $fh = @opendir($directory))) {
  //          trigger_error(sprintf('scandir() failed to open "%s": Invalid argument',$directory), E_USER_WARNING);
   //         return false;
    //    }
//
 //       $files = array ();
  //      while (false !== ($filename = readdir($fh))) {
   //         $files[] = $filename;
    //    }

     //   closedir($fh);

       // if ($sorting_order == 1) {
      //      rsort($files);
     //   } else {
     //       sort($files);
     //   }

       // return $files;
//}



function lastupdated(){
	
	
	if (strpos($_SERVER['PHP_SELF'], "newsletter/")){
		global $htdocsdir;
		$files=scandir($htdocsdir.'newsletter/');
		foreach ($files as $name){
			if (filemtime($htdocsdir.'newsletter/'.$name) > $date){
				$date=filemtime($htdocsdir.'newsletter/'.$name);
			}
		}
		
	}elseif (strpos($_SERVER['PHP_SELF'], "news")){
		$db=new DB();
		if ($_GET['expand']==""){
			$db->query('select modified from news order by modified DESC');
		}else{
			$db->query(sprintf('select modified from news where created="%s"',$expand));
		}
		$db->next_record();
		
		$date=$db->f("modified");
	}elseif (strpos($_SERVER['PHP_SELF'], "coach")){
		$db=new DB();
		if ($_GET['expand']==""){
			$db->query('select modified from coach order by modified DESC');
		}else{
			$db->query(sprintf('select modified from coach where created="%s"',$expand));
		}
		$db->next_record();
		$date=$db->f("modified");
	}elseif (strpos($_SERVER['PHP_SELF'], "hallfame")){
		$db=new DB();
		$db->query('select modified from halloffame order by modified DESC');
		$db->next_record();
		$date=$db->f("modified");
	}elseif (strpos($_SERVER['PHP_SELF'], "perfect.php")){
		$db=new DB();
		$db->query('select modified from perfect order by modified DESC');
		$db->next_record();
		$date=$db->f("modified");
	}elseif (strpos($_SERVER['PHP_SELF'], "otherevents")){
		$db=new DB();
		
		if (isset($_GET['id'])){
			$db->query(sprintf('select modified from otherevents where id = "%s"',$_GET['id']));
		}elseif (isset($_GET['expand'])){
			$db->query(sprintf('select modified from otherevents where id = "%s"',$_GET['expand']));
		}else{
			$db->query('select modified from otherevents order by modified DESC');
		}
		$db->next_record();
		$date=$db->f("modified");
	}elseif (strpos($_SERVER['PHP_SELF'], "kidshelpphone")){
		$db=new DB();
		if ($_GET['expand']==""){
			$db->query('select modified from khpevents order by modified DESC');
			$db->next_record();
			$date=$db->f("modified");
			$db->query('select modified from khp order by modified DESC');
			$db->next_record();
			if ($db->f("modified")>$date){
				$date=$db->f("modified");
			}
		}else{
			$db->query(sprintf('select modified from khpevents where id="%s"',$id));
			$db->next_record();
			$date=$db->f("modified");
		}
		
		
	}elseif (strpos($_SERVER['PHP_SELF'], "tournament")){
		$db=new DB();
		if (isset($_GET['id'])){
			$db->query(sprintf('select modified from tournament where id = "%s"',$_GET['id']));
		}elseif (isset($_GET['expand'])){
			$db->query(sprintf('select modified from tournament where id = "%s"',$_GET['expand']));
		}else{
			$db->query('select modified from tournament order by modified DESC');
		}
		$db->next_record();
		$date=$db->f("modified");
	}elseif($_SERVER['PHP_SELF']=='/bowlinglinks/index.php'){
		$db=new DB();
		$db->query('select modified from link order by modified desc');
		$db->next_record();
		$date=$db->f("modified");
		
	}elseif($_SERVER['PHP_SELF']=='/index.php'){
		$date=filemtime('newest.php');
		
	}elseif(is_file('updated.php')){
		include('updated.php');
		
	}else{
		$date=filemtime(basename($_SERVER['PHP_SELF']));
	}
	if ($date==0){
		$date=time();
	}
	echo date('M j, Y',$date);
}

//Other stuff
class DB extends DB_Sql {
	var $Host     = "localhost";
	var $Database = "o5pba";
	var $User     = "root";
	var $Password = "";
	
}

class sessDB extends CT_Sql {
	var $database_class = "DB";
	var $database_table = "active_sessions";
}

class O5Auth extends Auth {
	
	var $classname      = "O5Auth";
	
	var $lifetime       = 15;
	
	var $database_class = "DB";
	var $database_table = "auth";
	
	
	function auth_loginform() {
		// global $sess;
		// echo "<pre>";
		print_r($sess);
		global $_PHPLIB;
		global $classdir;
		include($classdir.'loginform.ihtml');
	}
	
	function auth_validatelogin() {
		
		global $username, $password;
		$username = $_POST['username'];
		$password = $_POST['password'];
		if(isset($username)) {
			$this->auth["uname"]=$username;        ## This provides access for "loginform.ihtml"
		}
		
		$uid = false;
		
		$this->db->query(sprintf("select user_id, perms ".
		"        from %s ".
		"       where username = '%s' ".
		"         and password = '%s'",
		$this->database_table,
		addslashes($username),
		md5(addslashes($password))));
		
		
		while($this->db->next_record()) {
			$uid = $this->db->f("user_id");
			$this->auth["perm"] = $this->db->f("perms");
		}
		return $uid;
	}
}

class O5Session extends Session {
	var $classname = "O5Session";
	
	var $cookiename     = "";                ## defaults to classname
	var $magic          = "bluepill";        ## ID seed
	var $mode           = "cookie";          ## We propagate session IDs with cookies
	var $fallback_mode  = "get";
	var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
	var $that_class     = "sessDB";          ## name of data storage container class
	var $gc_probability = 5;
	var $allowcache     = "no";              ## "public", "private", or "no"
}

class O5Perm extends Perm {
	
	var $classname = "O5Perm";
	
	var $permissions = array();
	
	function O5Perm(){
		
		$bin=1;
		$this->permissions["reset"] =$bin;
		$this->permissions["tournament"] =$bin*=2;
		$this->permissions["forms"] =$bin*=2;
		$this->permissions["bowling_school"] =$bin*=2;
		$this->permissions["other"] =$bin*=2;
		$this->permissions["coach"] =$bin*=2;
		$this->permissions["news"] =$bin*=2;
		$this->permissions["khp"] =$bin*=2;
		$this->permissions["perfect"] =$bin*=2;
		$this->permissions["calendar"] =$bin*=2;
		$this->permissions["logs"] =$bin*=2;
		$this->permissions["banner"] =$bin*=2;
		$this->permissions["halloffame"] =$bin*=2;
		$this->permissions["newsletter"] =$bin*=2;
		$this->permissions["bod"] =$bin*=2;
		$this->permissions["links"] =$bin*=2;
		$this->permissions["averagebook"] =$bin*=2;
		$this->permissions["users"] =$bin*=2;
		
		$this->permissions["admin"] =$bin*2-1;
		
	}
	
	
	function perm_invalid($does_have, $must_have) {
		global $perm, $auth, $sess;
		global $_PHPLIB;
		global $classdir,$htdocsdir;
		include($classdir.'perminvalid.ihtml');
	}
}

class update{
	
	var $db;
	var $htdocsroot = '/www/htdocs/planetspace.net/o5pba/public_html';
	var $days=array();
	var $newest = array();
	
	function get_def($conn, $dbname, $table) {
		$def = "";
		$def .= "DROP TABLE IF EXISTS $table;#%%\n";
		$def .= "CREATE TABLE $table (\n";
		$result = mysql_db_query($dbname, "SHOW FIELDS FROM $table",$conn) or die("Table $table not existing in database");
		while($row = mysql_fetch_array($result)) {
			$def .= "    $row[Field] $row[Type]";
			if ($row["Default"] != "") $def .= " DEFAULT '$row[Default]'";
			if ($row["Null"] != "YES") $def .= " NOT NULL";
			if ($row[Extra] != "") $def .= " $row[Extra]";
			$def .= ",\n";
		}
		$def = ereg_replace(",\n$","", $def);
		$result = mysql_db_query($dbname, "SHOW KEYS FROM $table",$conn);
		while($row = mysql_fetch_array($result)) {
			$kname=$row[Key_name];
			if(($kname != "PRIMARY") && ($row[Non_unique] == 0)) $kname="UNIQUE|$kname";
			if(!isset($index[$kname])) $index[$kname] = array();
			$index[$kname][] = $row[Column_name];
		}
		while(list($x, $columns) = @each($index)) {
			$def .= ",\n";
			if($x == "PRIMARY") $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
			else if (substr($x,0,6) == "UNIQUE") $def .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
			else $def .= "   KEY $x (" . implode($columns, ", ") . ")";
		}
		
		$def .= "\n);#%%";
		return (stripslashes($def));
	}
	
	function get_content($conn, $dbname, $table) {
		$content="";
		$result = mysql_db_query($dbname, "SELECT * FROM $table",$conn);
		while($row = mysql_fetch_row($result)) {
			
			$insert = "INSERT INTO $table VALUES (";
			for($j=0; $j<mysql_num_fields($result);$j++) {
				if(!isset($row[$j])) $insert .= "NULL,";
				else if($row[$j] != "") $insert .= "'".addslashes($row[$j])."',";
				else $insert .= "'',";
			}
			$insert = preg_replace("/,$/","",$insert);
			$insert .= ");#%%\n";
			$content .= $insert;
		}
		return $content;
	}
	
	function update(){
		global $absdir;
		$this->db=new DB;
		$dbhost=$this->db->Host;
		$dbuser=$this->db->User;
		$dbpass=$this->db->Password;
		$dbname=$this->db->Database;
		$path=$absdir.'classes/backup/';
		
		
		$path = $path . "dump/";
		
		$cur_time=date("Y-m-d");
		
		if (!file_exists($path.'backup-'.$cur_time. ".sql")){
			$conn = @mysql_connect($dbhost,$dbuser,$dbpass);
			if ($conn==false)  die("password / user or database name wrong");
			
			
			
			$tables = mysql_query($dbname,$conn);
			$num_tables = @mysql_num_rows($tables);
			$i = 0;
			$fp = fopen ($path .'backup-'.$cur_time. ".sql","w");
			while($i < $num_tables) {
				$table = mysql_tablename($tables, $i);
				$newfile .= $this->get_def($conn, $dbname,$table);
				$newfile .= "\n\n";
				$newfile .= $this->get_content($conn, $dbname,$table);
				$newfile .= "\n\n";
				fwrite ($fp,$newfile);
				$newfile='';
				$i++;
			}
			
			
			fclose ($fp);
		}
		
	}
	
	function mostRecent($table){
		if ($table=="average") {
			$date = getdate(filectime($averagesPath));
			if(!in_array(mktime(0,0,0,$date['mon'],$date['mday'],$date['year']),$this->newest)){
				array_push($this->newest,mktime(0,0,0,$date['mon'],$date['mday'],$date['year']));
			}
		}
		else
		{
		$this->db->query(sprintf('SELECT modified FROM %s',$table));
		while($this->db->next_record()){
			$date = getdate(strtotime($this->db->f('modified')));
			if(!in_array(mktime(0,0,0,$date['mon'],$date['mday'],$date['year']),$this->newest)){
				array_push($this->newest,mktime(0,0,0,$date['mon'],$date['mday'],$date['year']));
			}
		}
		
	}
	rsort($this->newest);
	}
	function dowhatsnew(){
		global $htdocsdir;
      global $oldestDateToShowHistory,$maxDaysToShowHistory;

		
		set_time_limit(0);
		
		$this->createIndex('news');
		$this->createIndex('coach');
		$this->createIndex('tournament');
		$this->createIndex('bowling_school_mini');
		$this->createIndex('otherevents');
		$this->createIndex('bowling_school');
		$this->createIndex('khp');
		$this->createIndex('average');
		$this->createIndex('forms');
		$this->createIndex('khpevents');
		$this->createIndex('perfect');
		$this->createIndex('halloffame');
		$this->createIndex('link');
		$this->createIndexNewsletter();
		krsort($this->days);
		
		$whatsnewdate = join ('', file ($htdocsdir.'whatsnew/update.bak'));
		
		if (!strpos(' '.$whatsnewdate,date("m.d.y"))){
			$ft = fopen($htdocsdir.'whatsnew/update.bak','w+');
			fwrite($ft,date("m.d.y"));
			fclose($ft);
			$oldcrap= implode ('', file ($htdocsdir.'whatsnew/newest.php'));
			$oldcrap.= implode ('', file ($htdocsdir.'whatsnew/older.php'));
			$fb = fopen($htdocsdir.'whatsnew/older.php','w+');
			fwrite($fb,$oldcrap);
			fclose($fb);
		}
		$fb = fopen($htdocsdir.'whatsnew/updated.php','w+');
			fwrite($fb,sprintf('<? $date=%s; ?>',time()));
			fclose($fb);
		
		$fb = fopen($htdocsdir.'whatsnew/newest.php','w+');
		$fp = fopen($htdocsdir.'newest.php','w+');
		fwrite($fp,'<table class="newest" cellspacing="0" cellpadding="2">'."\n");
		$first=false;
		$daysShown = 0;
		foreach($this->days as $day => $item){
			$date = getdate($day);
			if ($first==false){
				$ft = fopen($htdocsdir.'updated.php','w+');
				fwrite($ft,'<? $date='.$day.'; \?>');
				fclose($ft);
				
				
				fwrite($fb,"<tr><th>".date('M d, Y',$day)."</th></tr>\n");
				$swap = "light";
				foreach($item as $t){
					$swap = ($swap == "dark") ? "light" : "dark";
					fwrite($fb,"<tr><td class=\"$swap\"><a href=\"/".$t[1]."?expand=".$t[2]."\">".$t[0]."</a></td></tr>\n");
				}
				
				fclose($fb);
				$first=true;
			}

			if ((!$oldestDateToShowHistory || ((time() - $day) < $oldestDateToShowHistory)) && (!$maxDaysToShowHistory || ($daysShown++ < $maxDaysToShowHistory))) {
				fwrite($fp,"<tr><th>".date('M d, Y',$day)."</th></tr>\n");
				$swap = "light";
				foreach($item as $t){
					$swap = ($swap == "dark") ? "light" : "dark";
					fwrite($fp,"<tr><td class=\"$swap\"><a href=\"/".$t[1]."?expand=".$t[2]."\">".$t[0]."</a></td></tr>\n");
				}
			}
		}
		fwrite($fp,'</table>');
		fclose($fp);
	}
	
	function createIndexNewsletter() {
		global $htdocsdir;
		$files=scandir($htdocsdir.'newsletter/');
		foreach ($files as $name){
			
			if ($name!='.' && $name!='..' && strpos ($name,'.pdf') && filemtime($htdocsdir.'newsletter/'.$name)>mktime(0,0,0,date('m'),date('j')-5,date('Y'))){
				$date=getdate(filemtime($htdocsdir.'newsletter/'.$name));

				$data = array(sprintf('Newsletter - %s Newsletter Posted',date('M Y',mktime(0,0,0,substr($name,strpos($name,'-')+1,2),1,substr($name,0,strpos($name,'-'))))),'newsletter/index.php',$name);
				$time = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
				if(isset($this->days["$time"])){
					array_push($this->days["$time"],$data);
				}else{
									
					$this->days["$time"]=array($data);
				}
			}
		}	
	}
	
	function createIndex($table){
		$this->mostRecent($table);
		
		$tablename['tournament']='Tournaments';
		$tablename['leaguestandings']='League Standings';
		$tablename['calendar']='Upcoming Events';
		$tablename['news']='News';
		$tablename['forms']='Forms';
		$tablename['average']='Average';
		$tablename['bowling_school']='Bowling School';
		$tablename['bowling_school_mini']='Bowling School';
		$tablename['coach']='Coach\'s Corner';
		$tablename['otherevents']='Other Events';
		$tablename['khpevents']='KHP Event';
		$tablename['link']='Bowling Links';
		
		if ($table=='khp' || $table=='perfect' || $table=='halloffame'){
			$this->db->query(sprintf("SELECT modified FROM %s ORDER BY modified DESC LIMIT 0,1",$table));
			if($this->db->num_rows()==1){
				$this->db->next_record();
				$date = getdate($this->db->f('modified'));
				$start = $this->newest[3];
				
				$this->db->query(sprintf('SELECT distinct modified FROM %s WHERE modified >= "%s" group by modified ORDER BY modified DESC',$table,$start));
				if($this->db->num_rows()>0){
					while($this->db->next_record()){
						$date = getdate(strtotime($this->db->f('modified')));
						$time = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
						if ($time!==$oldtime){
							if ($table=='khp'){
								$data = array('Kids Help Phone - Donations Updated','kidshelpphone/index.php',$this->db->f('modified'));
							}elseif($table=='halloffame'){
								$data = array('Hall of Fame - Updated','hallfame/',$this->db->f('modified'));
							}else{
								$data = array('Perfect Games - Updated','aboutus/perfect.php',$this->db->f('modified'));
							}
							if(isset($this->days["$time"])){
								array_push($this->days["$time"],$data);
							}else{
								
								$this->days["$time"]=array($data);
							}
							$oldtime=$time;
						}
					}
				}
			}
		}
		else if ($table=="average")
		{
         global $averagesPath;
			$start = $this->newest[3];
			$date = getdate(filectime($averagesPath));
			$time = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);

			if ($time > $start) {
				$data = array('Average Book Updated','average/');
            if(isset($this->days["$time"])){
               array_push($this->days["$time"],$data);
            }else{
               $this->days["$time"]=array($data);
            }
			}
		}
		else{
			
			$this->db->query(sprintf("SELECT modified FROM %s ORDER BY modified DESC LIMIT 0,1",$table));
			if($this->db->num_rows()==1){
				$this->db->next_record();
				$date = getdate($this->db->f('modified'));
				$start = $this->newest[3];
				$this->db->query(sprintf('SELECT * FROM %s WHERE modified >= "%s" ORDER BY modified DESC',$table,$start));
				if($this->db->num_rows()>0){
					while($this->db->next_record()){
						$date = getdate(strtotime($this->db->f('modified')));
						$time = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
						$data=array();
						if($table=='tournament'||$table=='otherevents'){
							$data = array($tablename[$table].' - '.$this->db->f('title'),$table.'/',$this->db->f('ID')."&function=tourn_view");
						}elseif($table=='khpevents'){
							$data = array($tablename[$table].' - '.$this->db->f('title'),'kidshelpphone/',$this->db->f('ID')."&function=tourn_view");
						}elseif($table=='bowling_school_mini'){
							$data = array($tablename[$table].' - '.$this->db->f('title'),'bowling_school/index2.php',"&function=tourn_view&id=".$this->db->f('ID'));
						}elseif($table=='calendar'){
							$data = array($tablename[$table].' - '.$this->db->f('title'),'whatsnew/calday.php',$this->db->f('modified'));
						}elseif($table=='link'){
							$data = array($tablename[$table].' - '.$this->db->f('title'),'bowlinglinks/index.php',$this->db->f('modified'));
						}else{
							$data = array($tablename[$table].' - '.$this->db->f('title'),$table.'/',$this->db->f('created'));
						}
						if(isset($this->days["$time"])){
							array_push($this->days["$time"],$data);
						}else{
							
							$this->days["$time"]=array($data);
						}
					}
				}
			}
		}
	}
}
?>
