<?PHP

#####################################################################
##                                                                 ##
##                         Announce Mail                           ##
##                  http://www.phpwebscripts.com/                  ##
##                 e-mail: info@phpwebscripts.com                  ##
##                       copyright (c) 2003                        ##
##                                                                 ##
##                  > This script is freeware <                    ##
##                You may distribute it by any way                 ##
##                   BUT! You may not modify it!                   ##
## Removing the link to PHPWebScripts.com is a copyright violation.##
##   Altering or removing any of the code that is responsible, in  ##
##   any way, for generating that link is strictly forbidden.      ##
##   Anyone violating the above policy will have their license     ##
##   terminated on the spot.  Do not remove that link - ever.      ##
##                                                                 ##
#####################################################################

//error_reporting  (E_ERROR | E_PARSE);
if (ini_get("magic_quotes_sybase")) ini_set("magic_quotes_sybase",0);
if (!get_magic_quotes_gpc()) ini_set("magic_quotes_gpc",1);
if (!get_magic_quotes_runtime()) set_magic_quotes_runtime(1);
if ($s[kun]='UG93ZXJlZCBieSBBbm5vdW5jZSBNYWls') $x = 1; else $x = 0;
if (ini_get("register_globals")) ini_set("register_globals","Off");
include("./data/messages.php"); include("./data/data.php");

$m = strip_replace_array($m);
$linkid = db_connect();
if (!$linkid) problem(sql_error());
$f = fopen ("$s[phppath]/data/templates/_header.txt","r");
$s[header] = fread($f,filesize("$s[phppath]/data/templates/_header.txt")); fclose ($f); $r = $s[phprath].$s[phpruth].$s[kun].'PC9hPjxicj4='; if ((strlen($r))!=104) exit;
$f = fopen ("$s[phppath]/data/templates/_footer.txt","r"); $s[footer] = fread($f,filesize("$s[phppath]/data/templates/_footer.txt")); fclose ($f);

switch ($HTTP_GET_VARS[action]) {
	case 'login'		: login($HTTP_GET_VARS);
	case 'joined'		: joined($HTTP_GET_VARS);
	case 'unsubscribe'	: unsubscribe($HTTP_GET_VARS);
	case 'confirm'		: confirm($HTTP_GET_VARS,$HTTP_SERVER_VARS[REMOTE_ADDR]);
}
switch ($HTTP_POST_VARS[action]) {
	case 'login'		: login($HTTP_POST_VARS);
	case 'regconfirm'	: regconfirm($HTTP_POST_VARS);
	case 'changereg'	: submit_form($HTTP_POST_VARS);
	case 'joined'		: joined($HTTP_POST_VARS);
	case 'edited'		: edited($HTTP_POST_VARS);
}
if (!$HTTP_POST_VARS[username]) submit_form('');



function edited($data) {
	global $s,$m;
	$formular = form_control($data); $chyba = $formular[0]; $form = $formular[1];
	if ($chyba) { $s[info] = eot($m[errorsfound],implode('<br>',$chyba)); login($form); }
	write_to_db($form);
	$form[password] = $form[newpass]; login($form);
	exit;
}

function unsubscribe($data) {
	global $s;
	$q = dq("delete from $s[tblname] where username = '$data[username]' and password = '$data[password]'",0);
	if (!mysql_affected_rows()) problem('Your account can\'t be found.');
	parse_page('unsubscribed.html',$s);
}
function regconfirm ($data){
	global $s,$m;
	$formular = form_control($data);
	$chyba = $formular[0];
	$form = $formular[1];
	if ($chyba) {
		$form[info] = eot($m[errorsfound],implode('<br>',$chyba));
		submit_form ($form);
	}
	include ('./data/templates/_header.txt');
	echo '<table>';
	echo '<tr><td colspan=2> Below please find the data you entered to subscribe to our newsletter.</td></tr>';
	echo sprintf('<tr><TD align="left">Username:</TD><td>%s</td></TR>',$data['username']);
	echo sprintf('<tr><TD align="left">Password:</TD><td>%s</td></TR>',$data['password']);
	echo sprintf('<tr><TD align="left">Your name:</TD><td>%s</td></TR>',$data['name']);
	echo sprintf('<tr><TD align="left">Email:</TD><td>%s</td></TR>',$data['email']);
	echo '<tr><td colspan=2>Is this correct?</td></tr>';
	echo sprintf('<tr><td><form action="subscribe.php" method="post" name="add"><input type="hidden" name="action" value="joined"><input type="hidden" name="username" value="%s"><input type="hidden" name="password" value="%s"><input type="hidden" name="name" value="%s"><input type="hidden" name="email" value="%s"><input type="submit" name = "B1" value = "Yes"></form></td>',$data['username'],$data['password'],$data['name'],$data['email']);
	echo sprintf('<td><form action="subscribe.php" method="post" name="add"><input type="hidden" name="action" value="changereg"><input type="hidden" name="username" value="%s"><input type="hidden" name="password" value="%s"><input type="hidden" name="name" value="%s"><input type="hidden" name="email" value="%s"><input type="submit" name = "B1" value = "No"></form></td></tr>',$data['username'],$data['password'],$data['name'],$data['email']);

	echo '</table>';
	include('./data/templates/_footer.txt');

	exit;
}
function joined($data) {
	global $s,$m;

	$formular = form_control($data);
	$chyba = $formular[0];
	$form = $formular[1];
	if ($chyba) {
		$form[info] = eot($m[errorsfound],implode('<br>',$chyba));
		submit_form ($form);
	}
	$cas = write_to_db($form);
	$form[code] = md5($cas);
	send_email($form);
	show_thankyou($form);
}

function login_form() {
	global $s; parse_page('user_login.html',$s);
}

function login($form) {
	global $s,$m; if (!$form[username]) login_form();
	$q = dq("select * from $s[tblname] where username='$form[username]' AND password='$form[password]' AND confirm = 1",1);
	$result = mysql_fetch_array($q);
	if (!$result[number]){
		$s[info] = iot($m[w_user]);
		$s[username] =
		$form[username];
		login_form();
		exit;
	}
	$result[info] = $s[info];
	parse_page('user_edit.html',$result);
}

function confirm($data,$ip) {
	global $s,$m;
	if (!$ip) $ip = 1;
	$q = dq("select time,username,password,name,email from $s[tblname] where username = '$data[user]' AND password = '$data[password]'",1);
	$x = mysql_fetch_row($q);
	if (!$x[0]) {
		problem($m[no_account]);
	}
	$code = md5($x[0]); if ($code!=$data[code]) problem($m[w_confirm]);
	$q = dq("update $s[tblname] set ip = '$ip', confirm = 1 where username = '$data[user]' AND password = '$data[password]'",1);
	$info['username']=$x[1];
	$info['password']=$x[2];
	$info['name']=$x[3];
	$info['email']=$x[4];
	send_confirm_email($info);
	parse_page('user_confirmed.html',$form);
}

function show_thankyou($form) {
	global $s;
	$form = strip_replace_array($form);
	parse_page('user_joined.html',$form);
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

function write_to_db($form) {
	global $s,$HTTP_SERVER_VARS;
	$cas = time(); $form = add_slashes_array($form);
	if ($form[action]=='edited') dq("update $s[tblname] set password = '$form[newpass]', email = '$form[email]', name = '$form[name]' where username = '$form[username]' and password = '$form[password]'",1);
	else dq("insert into $s[tblname] values (NULL,'$form[username]','$form[password]','$form[name]','$form[email]','$HTTP_SERVER_VARS[REMOTE_ADDR]','$cas','0')",1);
	$cislo = mysql_insert_id();
	return $cas;
}

function form_control($form) {
	global $s,$m;
	if ($form[action]=='edited')
	{ $q = dq("select count(*) from $s[tblname] where username = '$form[username]' AND password = '$form[password]'",0);
	$x = mysql_fetch_row($q); if (!$x[0]) problem($m[no_auth]);
	if (!eregi("^[a-z0-9]{5,15}$",$form[newpass])) $chyba[] = $m[wrongpassword];
	}
	else
	{ if (!eregi("^[a-z0-9]{5,15}$",$form[username])) $chyba[] = $m[wrongusername];
	if (!eregi("^[a-z0-9]{5,15}$",$form[password])) $chyba[] = $m[wrongpassword];
	$q = dq("select count(*) from $s[tblname] where username = '$form[username]'",0); $x = mysql_fetch_row($q); if ($x[0]) $chyba[] = $m[use_username];
	$q = dq("select count(*) from $s[tblname] where email = '$form[email]'",0); $x = mysql_fetch_row($q); if ($x[0]) $chyba[] = $m[existing];
	}
	if (!trim($form[email])) $chyba[] = $m[m_email]; elseif (strlen($form[email]) > 100) $chyba[] = $m[l_email]; elseif ( !(eregi("^[a-z0-9_.-]+@[a-z0-9_-]+\.[a-z0-9.]+$",$form[email])) ) $chyba[] = $m[w_email];
	if (!trim($form[name])) $chyba[] = $m[m_name]; elseif (strlen($form[name]) > 100) $chyba[] = $m[l_name]; else $form[name] = ucwords(strtolower($form[name]));
	$form = strip_replace_array($form); return array ($chyba,$form);
}

function submit_form($form) {
	global $s; parse_page('user_join.html',$form);
}

function show_form($a) {
	global $s; $a[pageurl] = $s[pageurl];
	parse_page('add.html',$a);
}

function strip_replace_once ($x) { if (!$x) return $x; $x = stripslashes(ereg_replace("''","'",$x)); $x = eregi_replace('&amp;','&',$x); return $x; }

function sql_error() { global $dbe; if(empty($dbe[text])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); } return "$dbe[number]: $dbe[text]"; }
function problem ($error) { global $s; $s[info] = $error; parse_page('error.html', $s); }
function strip_replace_array ($a) { if (!$a) return $a; reset ($a); while (list ($k, $v) = each ($a)) { if (is_array($v)) continue; $a[$k] = ereg_replace("''","'",strip_tags($v)); $a[$k] = htmlspecialchars(ereg_replace("[\]",'',$a[$k])); $a[$k] = eregi_replace('&amp;','&',$a[$k]); } return $a; }
function add_slashes_array ($a) { if (!$a) return $a; reset ($a); while (list ($k, $v) = each ($a)) { if (is_array($v)) continue; $a[$k] = addslashes(ereg_replace("''","'",$v)); } return $a; }

function parse_page($template,$value) {
	global $s,$m;
	$template = "$s[phppath]/data/templates/$template";
	global $s,$m; if (!is_array($value)) $value = array();
	$value[adminemail] = $s[mail];
	$f = fopen($template,'r') or problem ("$m[erroropentmpl] $template");

	while (!feof($f)) $line .= fgets($f,4096); fclose ($f);
	$line1=base64_decode($s[phprath]).base64_decode($s[phpruth]).base64_decode($s[kun]).base64_decode('PC9hPjxicj4=');

	while (list($k,$v) = each($value)) $line = str_replace("#%$k%#",$v,$line); reset($value);
	$line = eregi_replace("#%[a-z0-9_]*%#",'',strip_replace_once($line));
	include ('./data/templates/_header.txt');
	echo $line;
	include('./data/templates/_footer.txt');
	exit;
}

	function parse_part($template,$value) { global $s,$m; if (!is_array($value)) $value = array(); $value[adminemail] = $s[mail];
	$fh = fopen( "$template", 'r' ) or problem ("$m[erroropentmpl] $template"); while (!feof($fh)) $line .= fgets ($fh,4096); fclose ($fh);
	while (list($key,$val) = each ($value)) $line = str_replace("#%$key%#",$val,$line); reset ($value); $line = eregi_replace("#%[a-z0-9]*%#",'',strip_replace_once($line));
	return $line;
	}

	function db_connect() {
		global $s,$m,$dbe;
		$link_id = mysql_connect($s[dbhost],$s[dbusername],$s[dbpassword]);
	if(!$link_id) { $dbe[number] = 0; $dbe[text] = "$m[dbconnecterror] $s[dbhost]."; return 0; }
	if(empty($s[dbname]) && !mysql_select_db($s[dbname])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); return 0; }
	if(!empty($s[dbname]) && !mysql_select_db($s[dbname])) { $dbe[number] = mysql_errno(); $dbe[text] = mysql_error(); return 0; }
	return $link_id;
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