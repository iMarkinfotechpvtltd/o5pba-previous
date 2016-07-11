<?PHP
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
$perm->check('newsletter');


include_once('./includes/menuitems.php');
include('./includes/top.php');

// error_reporting  (E_ERROR | E_PARSE); if (ini_get("magic_quotes_sybase")) ini_set("magic_quotes_sybase",0); if (!get_magic_quotes_gpc()) ini_set("magic_quotes_gpc",1); if (!get_magic_quotes_runtime()) set_magic_quotes_runtime(1); if ($s[kun]='UG93ZXJlZCBieSBBbm5vdW5jZSBNYWls') $x = 1; else $x = 0;
// if (ini_get("register_globals")) ini_set("register_globals","Off"); $dbe[number] = $dbe[text] = ''; include("../newsletter/data/data.php"); $linkid = db_connect(); if (!$linkid) problem(sql_error()); $r = $s[phprath].$s[phpruth].$s[kun].'PC9hPjxicj4='; if ((strlen($r))!=104) exit;
if(isset($_GET['action']))
{
	$action = $_GET['action'];
}
if(isset($_GET['confirmed']))
{
	$confirmed = $_GET['confirmed'];
}
echo '<table width=100% height=100%><tr><td width=100>';
left_frame();
echo '</td><td>';

switch ($HTTP_GET_VARS[action]) {
	case 'newsletter'	: newsletter_form('');
	case 'new_user'		: new_user_form('');
	case 'show_users'	: show_users($HTTP_GET_VARS[confirmed]);
	case 'delete_user'	: delete_user($HTTP_GET_VARS);
	case 'home'			: home();
	case 'password'		: password('');
	case 'config'		: config_form();
}
switch ($HTTP_POST_VARS[action]) {
	case 'newsletter'	: newsletter($HTTP_POST_VARS);
	case 'password'		: password($HTTP_POST_VARS);
	case 'config'		: save_config($HTTP_POST_VARS);
	case 'new_user'		: new_user($HTTP_POST_VARS);
}
echo '</td></tr></table></table>';
page_close();
include('./includes/bottom.php');
exit();
##################################################################################

function delete_user($data) {
	global $s;
	include("head.txt");
	$q = dq("delete from $s[tblname] where number = '$data[user]'",0);
	show_users($data[conf]);
	exit;
}

##################################################################################

function show_users($confirmed) {
	global $s;
	if (!$confirmed) { $conf = "confirm = '0'"; $info = 'Unconfirmed'; }
	else { $conf = "confirm = '1'"; $info = 'Confirmed'; }
	include("head.txt");
	$q = dq("select username,name,email,password,time,number from $s[tblname] where $conf",1);
	$num_rows = mysql_num_rows($q);
	if (!$num_rows) {
		echo iot(sprintf('No %s users found',$info));
		echo '</td></tr></table></table>';
		page_close();
		include('./includes/bottom.php');
		exit;
	}
	echo iot($info.' Users');
	echo '
<table border="0" width="550" cellspacing="0" cellpadding="5" class="table1"><tr><td align="center" nowrap>
<table border="0" width="500" cellspacing="0" cellpadding="2">

<tr>
<td align="center" nowrap><span class="text10blue">Username</span></td>
<td align="center" nowrap><span class="text10blue">Name/email</span></td>
<td align="center" nowrap><span class="text10blue">Joined</span></td>
<td align="center" nowrap><span class="text10blue">&nbsp;</span></td></tr>';
	while ($r = mysql_fetch_row($q))
	{ $joined = datum($r[4],0);
	echo '<tr><td align="center" nowrap><span class="text10">'.$r[0].'</span></td>
  <td align="center" nowrap><a class="link10" href="mailto:'.$r[2].'">'.$r[1].'</a></td>
  <td align="center" nowrap><span class="text10">'.$joined.'</span></td>
  <td align="center" nowrap><a class="link10" href="newsletter.php?action=delete_user&user='.$r[5].'&conf='.$confirmed.'">Delete user</a></td></tr>'; 
	}
	echo '</table></td></tr></table>';
	echo '</td></tr></table></table>';
	@ page_close();
	include('./includes/bottom.php');
	
	exit;
}

##################################################################################

function new_user_form($data) {
	global $s;
	include("./head.txt");
	echo $s[info];
	echo iot('Create A New Subscriber');
?>
<table border="0" width="350" cellspacing="0" cellpadding="5" class="table1"><tr><td align="center">
<table border="0" width="300" cellspacing="0" cellpadding="2">
<form action="newsletter.php" method="post">
<input type="hidden" name="action" value="new_user">
<tr><td align="left"><span class="text13">Username</span></td><td align="left"><input class="field1" type="text" size="15" name="username" value="<?PHP echo $data[username]; ?>"></span></td></tr>
<tr><td align="left"><span class="text13">Password</span></td><td align="left"><input class="field1" type="text" size="15" maxlength="15" name="password" value="<?PHP echo $data[password]; ?>"></span></td></tr>
<tr><td align="left"><span class="text13">Name</span></td><td align="left"><input class="field1" type="text" size="50" maxlength="50" name="name" value="<?PHP echo $data[name]; ?>"></span></td></tr>
<tr><td align="left"><span class="text13">Email</span></td><td align="left"><input class="field1" type="text" size="50" maxlength="100" name="email" value="<?PHP echo $data[email]; ?>"></span></td></tr>
<tr><td align="center" colspan="2"><input type=submit name=xx value="Submit" class="button1"></td></tr></form></table></td></tr></table><br>
<?PHP
echo '</td></tr></table></table>';
 page_close();
include('./includes/bottom.php');

exit;
}

##################################################################################

function new_user($data) {
	global $s;
	if ((!$data[username]) OR (!$data[password]) OR (!$data[name]) OR (!$data[email]))
	{ $s[info] = iot('All fields are required. Please try again.'); new_user_form($data); }
	$cas = time();
	$sql =  "select count(*) from $s[tblname] where username = '$data[username]'";
	echo $q = dq("select count(*) from $s[tblname] where username = '$data[username]'",0);
	$r = mysql_fetch_row($q); if ($r[0]) { $s[info] = iot('This username is already in use. Please try again.'); new_user_form($data); }
	$q = dq("insert into $s[tblname] values (NULL,'$data[username]','$data[password]','$data[name]','$data[email]','1','$cas','1')",1);
	include("./head.txt");
	echo iot('User \''.$data[username].'\' has been created');
	send_confirm_email($data);
	echo '</td></tr></table></table>';
	page_close();
	include('./includes/bottom.php');
	
	exit;
}


function send_confirm_email($form){
	global $s;
	$form[to] = $form[email];
	sendemail("$s[phppath]/data/templates/email_confirmed.txt",$form);
}
function send_email($form) {
	global $s;
	$form = strip_replace_array($form);
	$form[to] = $form[email];
	$form[confirm_url] = "$s[phpurl]/subscribe.php?action=confirm&user=$form[username]&password=$form[password]&code=$form[code]";
	sendemail("$s[phppath]/data/templates/email_confirm.txt",$form);
}

		function sendemail($template,$value) {
			global $s,$m;
			$fd = fopen($template,'r') or problem ("$m[erroropentmpl] $template");
			while ($line = fgets($fd,4096)) $emailtext .= $line; fclose($fd); eregi("Subject: +([^\n\r]+)", $emailtext, $regs); $sub = $regs[1];
			$emailtext = eregi_replace("Subject: +([^\n\r]+)[\r\n]+",'', $emailtext); reset ($value); while (list($key, $val) = each ($value)) $emailtext = str_replace("#%$key%#",$val,$emailtext);
			$emailtext = eregi_replace("#%[a-z0-9_]*%#",'', $emailtext); $emailtext = strip_replace_once($emailtext);
			//echo "To: $value[to]<br>From: $s[email]<br>Sub: $sub<br>$emailtext<br><br><br>"; $ok = 1;
			$ok = mail($value[to], $sub, $emailtext, "From: $s[email]"); return $ok;
		}

##################################################################################

function newsletter_form($data) {
	global $s;
	include("./head.txt");
	echo $s[info];
	if ($data[text]) $text = $data[text];
	else $text = join ('',file("$s[phppath]/data/templates/newsletter.txt"));
	$data = strip_replace_array($data);
	echo iot('Send A Newsletter');
?>
<table border="0" width="600" cellspacing="15" cellpadding="2" class="table1">
<form action="newsletter.php" method="post">
<input type="hidden" name="action" value="newsletter">
<tr><td align="center" nowrap><span class="text13">
In the text field may be used these variables:<br>#%name%# for name of the user<br>#%login%# for the URL where the user can modify his/her profile<br><br>
Subject: <input class="field1" type="text" size="80" name="subject" value="<?PHP echo $data[subject] ?>"><br><br>
Text:<br><textarea class="field1" cols=90 rows=20 name="text"><?PHP echo $text; ?></textarea><br><br>
<input type=submit name=xx value="Submit" class="button1">
</td></tr></form></table><br>
<?PHP 
echo '</td></tr></table></table>';
page_close();
include('./includes/bottom.php');

exit;
}

##################################################################################

function newsletter($data) {
	global $s;
	if ((!$data[text]) OR (!$data[subject]))
	{ $s[info] = iot('Both fields are required'); newsletter_form($data); }
	$emails = dq("select username,name,email,password from $s[tblname] where confirm = '1'",0);
	$num_rows = mysql_num_rows($emails);
	include("head.txt");
	if (!$num_rows) { echo iot('No subscribers found'); echo '</td></tr></table></table>'; page_close();include('./includes/bottom.php'); exit; }
	
	$time1 = time(); echo '<span class="text10">';
	while ($address = mysql_fetch_row($emails))
	{ if (time()>($time1+5)) { $time1=time(); echo 'Working ...'.str_repeat (' ',4000); flush(); }
	$line = $data[text]; $subject = $data[subject]; $value[name] = $address[1]; $value[email] = $address[2]; $value[username] = $address[0]; $value[password] = $address[3]; $value[login] = "$s[phpurl]/newsletter.php?action=login&username=$address[0]&password=$address[3]"; $value = strip_replace_array($value);
	reset ($value); foreach($value as $k => $v)
	{ $v  = strip_replace_once($v); $line = str_replace("#%$k%#",$v,$line); $subject = str_replace("#%$k%#",$v,$subject); }
	$line = eregi_replace("[\]",'',$line); $subject = eregi_replace("[\]",'',$subject);
	set_time_limit(50);
	$uspech = mail($address[2],$subject,$line,"From: $s[from_name] <$s[email]>$html_head");
	//echo "$address[2]<br>Subject: $data[subject]<br>Text: $line<br>From: $s[email]<br><br>"; $uspech = 1;
	$seznam .= "<br>$address[2]\n";
	}
	if ($uspech)
	{ echo eot('Mass email has been successfully sent to:',$seznam); echo '</td></tr></table></table>'; page_close();include('./includes/bottom.php'); }
	else problem('Cannot send emails. Please contact server administrator for help.');
	
	exit;
}

##################################################################################

function left_frame() {
	global $s;
?>
<table border=0 cellpadding=0 cellspacing=0 width="100%">
<tr><td align="center" valign="top"><br>
<table border=0 width=95% cellspacing=2 cellpadding=0>
<TR><TD align="left" nowrap><span class="text13blue">Menu<br>
<a href="newsletter.php?action=show_users&confirmed=1">Confirmed users</a><br>
<a href="newsletter.php?action=show_users&confirmed=0">Unconf. users</a><br>
<a href="newsletter.php?action=new_user">New user</a><br>
<a href="newsletter-files.php?">Manage Files</a><br>
<!--<a target="right" href="newsletter.php?action=newsletter">Send newsletter</a><br>
<a target="right" href="newsletter.php?action=config">Configure</a><br>
<a target="right" href="newsletter.php?action=password">Username/pass</a><br><br>
<a target="right" href="newsletter.php?action=logout">Log out</a><br>-->
</td></tr></table></center>
<?PHP 
//exit;
}

##################################################################################

function config_form() { global $info; include("../data/data.php"); include("./head.txt");
reset ($s); while (list ($key, $val) = each ($s)) { $s[$key] = ereg_replace ("[\]",'',$val); $s[$key] = htmlspecialchars($s[$key]); if (!$s[$key]) $s[$key] = ''; } echo $info; ?>
<span class="text13blue"><b>Configuration</b></span><br><span class="text10blue">Do not use these characters: <b> \ $</b> in any of your values</span><br>
<form method="POST" action="newsletter.php"><input type="hidden" name="action" value="config">
<table border="0" width="620" cellspacing="0" cellpadding="5" class="table1"><tr><td align="center">
<table border="0" width="600" cellspacing="0" cellpadding="2">
<tr><td align="left"><span class="text13">Mysql database host</span></td><td align="left"><INPUT maxLength=30 size=30 name="dbhost" value="<?PHP echo $s[dbhost]; ?>" class="field1"></td></tr>
<tr><td align="left"><span class="text13">Your mysql database username</span></td><td align="left"><INPUT maxLength=30 size=30 name="dbusername" value="<?PHP echo $s[dbusername]; ?>" class="field1"></td></tr>
<tr><td align="left"><span class="text13">Mysql database password</span></td><td align="left"><INPUT maxLength=30 size=30 name="dbpassword" value="<?PHP echo $s[dbpassword]; ?>" class="field1"></td></tr>
<tr><td align="left"><span class="text13">Name of your mysql database</span></td><td align="left"><INPUT maxLength=30 size=30 name="dbname" value="<?PHP echo $s[dbname]; ?>" class="field1"></td><input type="hidden" name="phprath" value="PGNlbnRlcj4="></tr>
<tr><td align="left"><span class="text13">Table name</span></td><td align="left"><INPUT maxLength=30 size=30 name="tblname" value="<?PHP echo $s[tblname]; ?>" class="field1"></td><input type="hidden" name="phpruth" value="PGEgaHJlZj0iaHR0cDovL3BocHdlYnNjcmlwdHMuY29tLyI+"></tr>
<tr><td align="left"><span class="text13">Your name ("From")</span></td><td align="left"><INPUT maxLength=30 size=30 name="from_name" value="<?PHP echo $s[from_name]; ?>" class="field1"></td></tr>
<tr><td align="left"><span class="text13">Your email</span></td><td align="left"><INPUT maxLength=30 size=30 name="email" value="<?PHP echo $s[email]; ?>" class="field1"></td></tr>
<tr><td align="left"><span class="text13">Full path to the folder where the scripts live. No trailing slash.</span></td><td align="left"><INPUT maxLength=100 size=50 name="phppath" value="<?PHP echo $s[phppath]; ?>" class="field1"><br><span class="text10">Sample: /htdocs/sites/user/html/mail</span></td></tr>
<tr><td align="left"><span class="text13">URL of the directory where your php scripts are installed. No trailing slash.</span></td><td align="left"><INPUT maxLength=100 size=50 name="phpurl" value="<?PHP echo $s[phpurl]; ?>" class="field1"><br><span class="text10">Sample: http://www.yourdomain.com/mail</span></td></tr>
<tr><td align="center" colspan=2><INPUT type=submit value="Save all values" name=D1 class="button1"></td></tr></TABLE></td></tr></TABLE><br></FORM></center><?PHP echo '</td></tr></table></table>'; page_close();include('./includes/bottom.php'); exit(); }

##################################################################################

function db_connect() { global $s,$dbe; $link_id = mysql_connect($s[dbhost],$s[dbusername],$s[dbpassword]);
if(!$link_id) { $dbe[number] = 0; $dbe[text] = "Unable to connect to database host $s[dbhost]."; return 0; }
if(empty($s[dbname]) && !mysql_select_db($s[dbname])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); return 0; }
if(!empty($s[dbname]) && !mysql_select_db($s[dbname])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); return 0; } return $link_id; }

##################################################################################

function save_config($form) { global $info; set_magic_quotes_runtime(0); unset ($form[submit],$form[action],$form[D1]); reset ($form);
while (list ($key, $val) = each ($form)) { $val = strip_replace_once($val); $val = ereg_replace('"','\"',$val); if ($val=='on') $val = 1; $data .= "\$s[$key] = \"$val\";"; } $data = "<?PHP $data ?>";
if (!$sb = fopen("$form[phppath]/data/data.php","w")) problem ("Cannot write to file 'data.php' in your data directory. Please make sure that your data directory exists and has 777 permission and the file 'data.php' inside has permission 666. Cannot continue."); $zapis = fwrite ($sb, $data); fclose($sb);
if (!$zapis) $info = "<span class=\"text13blue\"><b>Can not write to file 'data.php'.<br>Please make sure that your data directory exists and has 777 permission and the file 'data.php' inside has permission 666. Cannot continue.</b></span><br><br>"; else $info = "<span class=\"text13blue\"><b>Your setting has been successfully updated</b></span><br><br>"; config_form(); }

##################################################################################

function sql_error() { global $dbe; if(empty($dbe[text])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); } return "$dbe[number]: $dbe[text]"; }
function home() {global $s; include("head.txt");?><table border=0 cellpadding=0 cellspacing=0 width="100%"><tr><td width=750 align="center" valign="top"><br><br><br><br><br><span class="text13blue"><b>Welcome to the Admin Area</b></span><br><br><span class="text13">Please select a function from the menu on the left</span></td></tr></table></center><?PHP exit; }
function log_out() { global $s; session_destroy(); if (!$s[info]) $s[info] = '<span class="text13"><font color="red"><b>You have been logged out</b></font></span><br><br>'; login (0); exit;  }
function problem ($error) { include("head.txt"); echo '<br><br><font color="FF0000" size=3 face="Verdana,arial"><b>ERROR</b></font><br><br><span class="text13blue"><b>'.$error.'</b></span><br><br>'; echo '</td></tr></table></table>'; page_close();include('./includes/bottom.php'); exit; }
function strip_replace_array ($a) { if (!$a) return $a; reset ($a); while (list ($k, $v) = each ($a)) { if (is_array($v)) continue;$a[$k] = ereg_replace("''","'",strip_tags($v));$a[$k] = htmlspecialchars(ereg_replace("[\]",'',$a[$k]));$a[$k] = eregi_replace('&amp;','&',$a[$k]); } return $a; }
function strip_replace_once ($x) { if (!$x) return $x; $x = ereg_replace("''","'",$x);$x = stripslashes($x);$x = eregi_replace('&amp;','&',$x);return $x; }
function check_session($data) { global $s;$a = file("$s[phppath]/data/.htpasswd");$b = split (':',trim($a[0])); if ($data[admuser]!=$b[0]) { session_destroy();$s[info] = '<span class="text13"><font color="red"><b>An error has occurred. Please login again.</b></span><br><br>';login(0); } }

##################################################################################

function password($a) { global $s; include("./head.txt");
if (($a[newuser]) AND ($a[newpass])) { $sb = fopen("$s[phppath]/data/.htpasswd","w"); $zapis = fwrite ($sb, "$a[newuser]:" . MD5($a[newpass])); fclose($sb);@chmod(".htpasswd", 0666);if (!$zapis) problem ("Can not write to the .htpasswd file. Please make sure that the data directory has 777 permission and the .htaccess file has 666 permission.");echo '<br><br><center><span class="text13blue"><b>Admin username and password have been updated.<br>If you have modified your username, you now have to log in again.</b></span><br><br>'; echo '</td></tr></table></table>'; page_close();include('./includes/bottom.php'); exit(); }
if (($a[newuser]) OR ($a[newpass])) echo '<div align=center><center><br><span class="text13blue"><b>Both fields are required</b></span><br>';
echo iot('Modify Admin\'s Username/Password');
?>
<table border="0" width="200" cellspacing="0" cellpadding="5" class="table1"><tr><td align="center">
<table border="0" width="180" cellspacing="0" cellpadding="2">
<form action="newsletter.php" method="post"><input type="hidden" name="action" value="password">
<TR><td align="right" nowrap><span class="text13">New username </span></td>
<td align="left"><input class="field1" size="15" name="newuser" value=<?PHP echo $a[newuser]; ?>></td></tr>
<tr><td align="right" nowrap><span class="text13">New password </span></td>
<td align="left"><input class="field1" size="15" name="newpass" value=<?PHP echo $a[newpass]; ?>></td></tr>
<tr><td align="center" colspan=2><input type="submit" name="A1" value="Submit" class="button1"></td></tr></table></td></tr></table></form></center></div>
 <?PHP echo '</td></tr></table></table>'; page_close(); include('./includes/bottom.php'); exit; }
 
 ##################################################################################
 
 function datum ($cas) {
 	return date ("m-d-Y",$cas);
 }
 
 ##################################################################################
 
 function iot($info) {
 	return '<span class="text13blue"><b>'.$info.'</b></span><br><br>';
 }
 
 ##################################################################################
 
 function eot($info,$errors) {
 	return '<span class="text13blue"><b>'.$info.'</b></span><br><span class="text13">'.$errors.'</span><br><br>';
 }
 
 ##################################################################################
 
 function dq($query,$check) {
 	global $s;
 	$q = mysql_query($query);
 	if ( ($check) AND (!$q) ) problem(mysql_error());
 	return $q;
 }
 
 ##################################################################################
 
 
?>
