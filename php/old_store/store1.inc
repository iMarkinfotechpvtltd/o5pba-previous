<?php

// Initiate connection to database
// userid and password should be stored in a separate, non-public file and be
// include()'d here.
function connect_db(){ 

	include_once("dbase.inc");
	MYSQL_CONNECT($db_server,$db_username,$db_password) OR DIE("Unable to connect to database");
	@MYSQL_SELECT_DB("$db_name") or die("Unable to select database");
}


function welcome(){ 
	echo "welcome";


}

function loginform($post=array(), $action, $goto=''){ 

if ($goto==''){
	$goto=$post["goto"];
}
if ($goto==''){
	$goto='checkout';
}


	echo '<table><tr><td>';
	$userid=str_replace ('%', '', $post["userid"]);
	$password=str_replace ('%', '', $post["password"]);
	if ($action=='login'){
		$query="select * from contact where E_MAIL like '".$userid."' and PASSWORD like '".$password."'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
						session_register('valid_user');
 						$valid_user=$userid;
		}else{
echo '<h3>Login Error</h3> Please try again.<br>';
}
	}

	if ($valid_user==''){

		$y = new form;
		$y->add_element(array("type"=>"text",
			"name"=>"userid",
			"value"=>$userid));
		$y->add_element(array("type"=>"password",
			"name"=>"password",
			"value"=>""));
		$y->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"login"));
		$y->add_element(array("type"=>"hidden",
			"name"=>"goto",
			"value"=>$goto));
		$y->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Login"));

		$y->start();
		echo 'Email Address:<br>';
		$y->show_element("userid");
		echo '<br>Password:<br>';
		$y->show_element("password");
		echo '<br>';
		$y->show_element("action");
		$y->show_element("submit");
		$y->finish();
echo '<br>Forgot your password? <a href="?action=getpasswd">Click Here</a>';
		echo '</td><td valign=bottom>';

		$l = new form;
		$l->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"checkout"));
		$l->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"New Customer"));

		$l->start();
		$l->show_element("action");
		$l->show_element("submit");
		$l->finish();
	}else{
		$y = new form;
		$y->add_element(array("type"=>"hidden",
			"name"=>"userid",
			"value"=>$valid_user));
		$y->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>$goto));
		$y->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Continue"));

		$y->start();
		echo 'Thank you for logging in '.$valid_user.'.<br><br>';
		$y->show_element("action");
		$y->show_element("userid");
		$y->show_element("submit");
		$y->finish();

	}
	echo '</td></tr></table>';
   return $valid_user;
}

function user_edit($post=array(), $userid, $action){ 

	if ($userid != ''){
		$query='select * from contact where E_Mail like "'.$userid.'"';
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
	}

 $elements=array("CUSTOMER_I", "CUSTOMER_N", "CUSTOMER_F", "STREET_ADD", "CITY", "STATE", "ZIP_CODE", "COUNTRY", "PHONE_NUMB", "E_MAIL", "SHIPPING_N", "SHIPPING_F", "SHIPPING_A", "SHIPPING_C", "SHIPPING_Z", "SHIPPING_Y", "NAME_ON_CA", "CARD_TYPE", "CARD_NUMBE", "EXPIRY_DAT", "pass1", "pass2", "password");

	$f = new form;
 $error=false;
 $reason="";
 $reason2="";

$provinces= array("Please Select"=>"",
        "Newfoundland"=>"Newfoundland",
        "Nova Scotia"=>"Nova Scotia",
        "New Brunswick"=>"New Brunswick",
        "Prince Edward Island"=>"Prince Edward Island",
        "Quebec"=>"Quebec",
        "Ontario"=>"Ontario",
        "Manitoba"=>"Manitoba",
        "Saskatchewan"=>"Saskatchewan",
        "Alberta"=>"Alberta",
        "British Columbia"=>"British Columbia",
        "Nunavut"=>"Nunavut",
        "Northwest Territories"=>"Northwest Territories",
        "Yukon Territory"=>"Yukon Territory");

$countrys= array("Canada"=>"Canada");

$card= array("Visa"=>"Visa",
        "Mastercard"=>"Mastercard");

 $f->add_element(array("type"=>"hidden",
                "name"=>"CUSTOMER_I",
                "value"=>$row["CUSTOMER_I"]
 ));

 $f->add_element(array("type"=>"text",
                "name"=>"CUSTOMER_N",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter First Name",
                "value"=>$row["CUSTOMER_N"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"CUSTOMER_F",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter Last Name",
                "value"=>$row["CUSTOMER_F"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"STREET_ADD",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter Address",
                "value"=>$row["STREET_ADD"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"CITY",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter City",
                "value"=>$row["CITY"]
 ));
 $f->add_element(array("type"=>"select",
                "name"=>"STATE",
                "minlength"=>"1",
                "length_e"=>"Please enter Province",
                "options"=>$provinces,
                "value"=>$row["STATE"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"ZIP_CODE",
                "size"=>"10",
                "minlength"=>"5",
                "length_e"=>"Please enter Postal Code",
                "value"=>$row["ZIP_CODE"]
 ));
 $f->add_element(array("type"=>"select",
                "name"=>"COUNTRY",
                "options"=>$countrys,
                "value"=>$row["COUNTRY"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"PHONE_NUMB",
                "size"=>"30",
                "valid_regex"=>"^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$",
                "valid_e"=>"Incorrect telephone number (xxx-xxx-xxxx).",
                "minlength"=>"1",
                "length_e"=>"Please enter Phone Number (xxx-xxx-xxxx)",
                "value"=>$row["PHONE_NUMB"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"E_MAIL",
                "size"=>"30",
                "valid_regex"=>"^[a-zA-Z0-9|\.|\-]*@[a-zA-Z0-9|\.|\-]*$",
                "valid_e"=>"Incorrect e-mail address.",
                "minlength"=>"1",
                "length_e"=>"Please enter Email Address",
                "value"=>$row["E_MAIL"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"SHIPPING_N",
                "size"=>"30",
                "value"=>$row["SHIPPING_N"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"SHIPPING_F",
                "size"=>"30",
                "value"=>$row["SHIPPING_F"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"SHIPPING_A",
                "size"=>"30",
                "value"=>$row["SHIPPING_A"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"SHIPPING_C",
                "size"=>"30",
                "value"=>$row["SHIPPING_C"]
 ));
 $f->add_element(array("type"=>"select",
                "name"=>"SHIPPING_S",
                "options"=>$provinces,
                "value"=>$row["SHIPPING_S"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"SHIPPING_Z",
                "size"=>"30",
                "value"=>$row["SHIPPING_Z"]
 ));
 $f->add_element(array("type"=>"select",
                "name"=>"SHIPPING_Y",
                "options"=>$countrys,
                "value"=>$row["SHIPPING_Y"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"NAME_ON_CA",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter Name on the card",
                "value"=>$row["NAME_ON_CA"]
 ));
 $f->add_element(array("type"=>"select",
                "name"=>"CARD_TYPE",
                "options"=>$card,
                "value"=>$row["CARD_TYPE"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"CARD_NUMBE",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter your card number",
                "value"=>$row["CARD_NUMBE"]
 ));
 $f->add_element(array("type"=>"text",
                "name"=>"EXPIRY_DAT",
                "size"=>"30",
                "value"=>$row["EXPIRY_DAT"]
 ));
 $f->add_element(array("type"=>"password",
                "name"=>"pass1",
                "size"=>"30",
                "value"=>''
 ));
 $f->add_element(array("type"=>"password",
                "name"=>"pass2",
                "size"=>"30",
                "value"=>''
 ));
 $f->add_element(array("type"=>"password",
                "name"=>"password",
                "size"=>"30",
                "minlength"=>"1",
                "length_e"=>"Please enter your Password",
                "value"=>''
 ));
 $f->add_element(array("type"=>"hidden",
                "name"=>"action",
                "value"=>'submitedit'
 ));
		$f->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Save"));

if ($action=='submitedit') {


    if ($userid == ''){

    $email=$post['E_MAIL'];
    $query = "select CUSTOMER_I from contact where E_MAIL like '$email'";
    $result = @mysql_query($query);

    $row=mysql_fetch_array($result);

    if (mysql_num_rows($result) != 0){
         echo 'There there is a current user with that emain address, please use a different one.';
         $error=true;
   }}



	if ($userid != ''){
		$query="select * from contact where E_MAIL like '".$userid."' and PASSWORD like '".$password."'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0)
		{
		  $result=mysql_query($query);
		  $row=mysql_fetch_array($result);
		}
  $oldpass=$row['PASSWORD'];
  if ($oldpass!=$post['password']){
				echo 'Password Incorrect';
				$error=true;
  }
  $newpass=$post['pass1'];
}else{
		$newpass=$post['password'];
}

if ($newpass!=''){
if ($newpass!=$post['pass2']){
echo 'Passwords do not match';
$error=true;
}
}

$c= new credit_card;
$card_ok=$c->check ($post['CARD_NUMBE']);
    $results=$f->validate("ok", $elements);

    if($results!="ok" or $card_ok['valid']!=true or $card_ok['type']!=$post['CARD_TYPE']){
        $f->load_defaults($elements);
        $error=true;
if ($card_ok['valid']!=true){
echo "Bad Credit Card Number<br>";
}
if ($card_ok['type']!=$post['CARD_TYPE']){
echo 'Your credit card identifies as a '.$card_ok['type'].' not a '.$post['CARD_TYPE'].'<br>';
}
        echo "Errors Detected. Please check the items below.";

    }

 }

if ($error==true || $action=='edit' || $action==''){
		$f->start();


        echo '<table width=100%>';
        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Customer Info</b></center></td><td>';
        echo '<tr><td width=10%></td><td>';
//Customer Info
echo '<table width=100%>';
echo '<tr><td width=25%>First Name:</td><td>';
 $f->show_element("CUSTOMER_I");
 $f->show_element("action");
 $f->show_element("CUSTOMER_N");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("CUSTOMER_N"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Last Name:</td><td>';
 $f->show_element("CUSTOMER_F");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("CUSTOMER_F"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Street Address:</td><td>';
 $f->show_element("STREET_ADD");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("STREET_ADD"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>City:</td><td>';
 $f->show_element("CITY");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("CITY"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Province:</td><td>';
 $f->show_element("STATE");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("STATE"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Postal Code:</td><td>';
 $f->show_element("ZIP_CODE");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("ZIP_CODE"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Country:</td><td>';
 $f->show_element("COUNTRY");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("COUNTRY"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td class="topLeft">Phone
Number:<br><i>(123-456-7890)</i></td><td>';
 $f->show_element("PHONE_NUMB");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("PHONE_NUMB"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Email Address:</td><td>';
 $f->show_element("E_MAIL");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("E_MAIL"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '</table>';
	echo '</td><td width=20%></td></tr>';
        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Shipping Info <i>(Leave BLANK if same as above)</i></b></center></td></tr>';
        echo '<tr><td width=10%></td><td>';
//Shipping Info
echo '<table width=100%>';
echo '<tr><td width=25%>First Name:</td><td>';
 $f->show_element("SHIPPING_N");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_N"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Last Name:</td><td>';
 $f->show_element("SHIPPING_F");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_F"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Address:</td><td>';
 $f->show_element("SHIPPING_A");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_A"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>City:</td><td>';
 $f->show_element("SHIPPING_C");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_C"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>State:</td><td>';
 $f->show_element("SHIPPING_S");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_S"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Zip:</td><td>';
 $f->show_element("SHIPPING_Z");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_Z"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Country:</td><td>';
 $f->show_element("SHIPPING_Y");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("SHIPPING_Y"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '</table>';
	echo '</td><td width=20%></td></tr>';
        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Payment Info</b></center></td><td>';
        echo '<tr><td width=10%></td><td>';
//PAYMENT INFO
echo '<table width=100%>';
echo '<tr><td width=25%>Name on Card:</td><td>';
 $f->show_element("NAME_ON_CA");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("NAME_ON_CA"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Card Type:</td><td>';
 $f->show_element("CARD_TYPE");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("CARD_TYPE"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '<tr><td>Card Number:</td><td>';
 $f->show_element("CARD_NUMBE");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("CARD_NUMBE"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
//echo '<tr><td colspan=2><i>Use 5412-3456-7890-1034 for MC<br>and 4500-3456-7890-1238 for V</i>';
//echo '</td></tr>';
echo '<tr><td>Exp. Date:</td><td>';
 $f->show_element("EXPIRY_DAT");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("EXPIRY_DAT"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
echo '</td></tr>';
echo '</table>';
	echo '</td><td width=20%></td></tr>';
        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Password</b></center></td><td>';
        echo '<tr><td width=10%></td><td>';
//Passwords
echo '<table width=100%>';
if ($userid!=''){
echo '<tr><td width=25%>Current Password:</td><td>';
}else{
echo '<tr><td width=25%>New Password:</td><td>';
}
 $f->show_element("password");
echo '</td><td>';
if($error==true){
        $test=$f->validate("OK",array("password"));
}
if($test!="OK"){
        print '<font color="red">'.$test.'</font>';
}elseif($reason2!=""){
        print '<font color="red">'.$reason2.'</font>';
}
if ($userid!=''){
echo '</td></tr>';
echo '<tr><td>New Password:</td><td>';
 $f->show_element("pass1");
echo '</td><td></td></tr>';
}
echo '<tr><td>Retype New Password:</td><td>';
 $f->show_element("pass2");
echo '</td><td></td></tr>';
echo '</table>';

	echo '</td><td width=20%></td></tr>';

        echo '</table>';
 $f->show_element("submit");
$f->finish();
}


if ($error==false && $action=='submitedit'){
	if ($post['CUSTOMER_I']!=''){

 $query = "update contact set CUSTOMER_N='".$post['CUSTOMER_N']."' , CUSTOMER_F='".$post['CUSTOMER_F']."' , 
STREET_ADD='".$post['STREET_ADD']."' , CITY='".$post['CITY']."' , STATE='".$post['STATE']."' , ZIP_CODE='".$post['ZIP_CODE']."' , COUNTRY='".$post['COUNTRY']."' , PHONE_NUMB='".$post['PHONE_NUMB']."' , E_MAIL='".$post['E_MAIL']."' ,CARD_TYPE='".$post['CARD_TYPE']."' , CARD_NUMBE='".$post['CARD_NUMBE']."' , NAME_ON_CA='".$post['NAME_ON_CA']."' , EXPIRY_DAT='".$post['EXPIRY_DAT']."' , SHIPPING_N='".$post['SHIPPING_N']."' , SHIPPING_F='".$post['SHIPPING_F']."' , SHIPPING_A='".$post['SHIPPING_A']."' , SHIPPING_C='".$post['SHIPPING_C']."' , SHIPPING_S='".$post['SHIPPING_S']."' , SHIPPING_Z='".$post['SHIPPING_Z']."' , SHIPPING_Y='".$post['SHIPPING_Y'];

if ($post['pass2'] != '') {
 $query=$query."' , PASSWORD='".$post['pass2'];
}
 $query=$query."' where CUSTOMER_I = '".$post['CUSTOMER_I']."'";

	}else{

$query= "insert into contact values ('', '".$post['CUSTOMER_N']."' , '".$post['CUSTOMER_F']."' , '".$post['STREET_ADD']."' , '".$post['CITY']."' , '".$post['STATE']."' , '".$post['ZIP_CODE']."' , '".$post['COUNTRY']."' , '".$post['PHONE_NUMB']."' , '".$post['E_MAIL']."' ,'".$post['password']."' , '', '".$post['CARD_TYPE']."' , '".$post['CARD_NUMBE']."' , '".$post['NAME_ON_CA']."' , '".$post['EXPIRY_DAT']."' , '".$post['SHIPPING_N']."' , '".$post['SHIPPING_F']."' , '".$post['SHIPPING_A']."' , '".$post['SHIPPING_C']."' , '".$post['SHIPPING_S']."' , '".$post['SHIPPING_Z']."' , '".$post['SHIPPING_Y']."' , '')";
	}
if ($error==false){
 $result = mysql_query($query) or die (print mysql_error());
$email=$post['E_MAIL'];
   $query = "select CUSTOMER_I from contact where E_MAIL like '$email'";
   $result = @mysql_query($query);

   $row=mysql_fetch_array($result);
   $usernum = $row["CUSTOMER_I"];


$fp = fopen ("/home/trg/contacts.txt", "a+");
$string = "W".$usernum.chr(9).$post['CUSTOMER_N'].chr(9).$post['CUSTOMER_F'].chr(9);
$string = $string.$post['STREET_ADD'].chr(9).$post['CITY'].chr(9).$post['STATE'].chr(9).$post['ZIP_CODE'];
$string = $string.chr(9).$post['COUNTRY'].chr(9).$post['PHONE_NUMB'].chr(9).$post['E_MAIL'].chr(9);
$string = $string.$post['CLASS'].chr(9).$post['CARD_TYPE'].chr(9).$post['CARD_NUMBE'].chr(9);
$string = $string.$post['NAME_ON_CA'].chr(9).$post['EXPIRY_DAT'].chr(9).$post['password'].chr(9)."\n";

$er = fwrite ($fp, $string);
$er = fclose ($fp);

$valid_user=$post['E_MAIL'];

		$y = new form;
		$y->add_element(array("type"=>"hidden",
			"name"=>"userid",
			"value"=>$valid_user));
		$y->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"newcheckout"));
		$y->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Go to checkout"));

		$y->start();
		echo 'Thank you for loging in '.$valid_user.'.<br><br>';
		$y->show_element("action");
		$y->show_element("userid");
		$y->show_element("submit");
		$y->finish();
}
}


}

function order_form($userid,$cart=array()){
		$a = new form;
		$b = new form;
		$c = new form;

		$a->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"browse"));
		$a->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Continue Shopping"));
		$b->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"cart"));
		$b->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Return to Cart"));
		$c->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"chooseship"));
		$c->add_element(array("type"=>"hidden",
			"name"=>"session_id",
			"value"=>session_id()));
		$c->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Place Order"));


		$query="select * from contact where E_MAIL like '".$userid."'";
		$result = mysql_query($query);
		$row=mysql_fetch_array($result);

        echo '<table width=100%>';

        display_userinfo($row,$userid);

        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Order Info</b></center></td></tr><tr><td colspan=3>';

display_cart($cart, false);

        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Action</b></center></td></tr>';
        echo '<tr><td width=30%><center>';
		$a->start();
		$a->show_element("action");
		$a->show_element("submit");
		$a->finish();

        echo '</center></td><td><center>';
		$b->start();
		$b->show_element("action");
		$b->show_element("submit");
		$b->finish();
        echo '</center></td><td width=20%><center>';
		$c->start();
		$c->show_element("session_id");
		$c->show_element("action");
		$c->show_element("submit");
		$c->finish();
        echo '</center></td></tr></table>';

}

function workwithcart ($cart=array(), $new, $save,$post=array()){
  if($new)
  {
    //new item selected
    if($cart[$new]){
      $cart[$new]++;
    }else {
      if(!$post[$new]){
            $cart[$new] = 1;
      }else{
            $cart[$new] = $post[$new];
      }
    }
    $cart['total_price'] = calculate_price($cart);
    $cart['items'] = calculate_items($cart);
    $cart['total_tax'] = calculate_tax($cart);
    $cart['total_shipping'] = calculate_shipping($cart);
    $cart['grand_total']=$cart['total_price']+$cart['total_tax']+$cart['total_shipping'];

  }
  if($save)
  {
    foreach ($cart as $prodid => $qty)
    {
      if($post[$prodid]=="0")
        unset($cart[$prodid]);
      else 
        $cart[$prodid] = $post[$prodid];
    }

    $cart['total_price'] = calculate_price($cart);
    $cart['items'] = calculate_items($cart);
    $cart['total_tax'] = calculate_tax($cart);
    $cart['total_shipping'] = calculate_shipping($cart);
    $cart['grand_total']=$cart['total_price']+$cart['total_tax']+$cart['total_shipping'];
  }
	return $cart;
}
function workcart ($cart=array()){
  if($cart['items']!=0){
    display_cart($cart,true);
}
  else  {
    echo "<p>There are no items in your cart";
    echo "<hr>";
  }
}

function get_item_details($prodid){

  // query database for all details for a particular book
  if (!$prodid || $prodid==""){
     return false;}
   $query = "select * from products where idcode like '$prodid'";
   $result = @mysql_query($query);
   if (!$result){
     return false;}
   $result = @mysql_fetch_array($result);
   return $result;
}

function display_cart($cart=array(),$change){
        echo '<center>';
    if ($change == true){
		$y = new form;
		$y->add_element(array("type"=>"hidden",
			"name"=>"action",
			"value"=>"auth"));
		$y->add_element(array("type"=>"hidden",
			"name"=>"session_id",
			"value"=>session_id()));
		$y->add_element(array("type"=>"submit",
			"name"=>"submit",
			"value"=>"Place order"));

echo '<form method = post>';
echo '<input type = hidden name = action value = cart>';
}
        echo '<table border=1><tr>';
    echo '<td align = center><b>Prod. ID</b></td>';
    echo '<td align = center><b>Name</b></td>';
    echo '<td align = center><b>Unit Price</b></td>';
    echo '<td align = center><b>Quantity</b></td>';
    echo '<td align = center><b>Total Price</b></td></tr>';

  //display each item as a table row
  foreach ($cart as $prodid => $qty)
  {
if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){
    $item = get_item_details($prodid);
    echo "<tr>";
    echo "<td align = center>".$item["item_id"].'</td>';
    echo "<td align = left>";
    echo '<a href="index.php?action=details&id='.$prodid.'">'.$item["item_name"].'</a>';
    echo "</td><td align = center>$".$item["item_price"];
    echo "</td><td align = center>";
    // if we allow changes, quantities are in text boxes
    if ($change == true){
      echo "<input type = \"text\" name = \"$prodid\" value = \"$qty\" size = \"3\">";
    }else{
      echo $qty;
}
    echo "</td><td align = center>$".number_format($item["item_price"]*$qty,2)."</td></tr>\n";
}
  }



        echo '<tr><td colspan=2 rowspan=4>&nbsp</td><td colspan=2>Sub total</td><td>$'.number_format($cart['total_price'],2).'</td></tr>';
        echo '<tr><td colspan=2>Tax total</td><td>$'.number_format($cart['total_tax'],2).'</td></tr>';
//        echo '<tr><td colspan=2>Shipping Cost<br><i>shipping costs may change based on your geographic location</i></td><td>$';
//echo number_format($cart['total_shipping'],2).'</td></tr>';
//        echo '<tr><td colspan=2>Grand total</td><td>$';
//        echo number_format($cart['grand_total'],2);
        echo '</td></tr>';
        echo '</table>';
if ($change == true){

echo '<input type = hidden name = save value = true>';
echo '<input type = submit name = submit value = "update totals"></form>';
//  This is for when the dns is working right.
		$y->start('','','https://secure.trgtech.com/index.php');
//  $y->start();
		$y->show_element("action");
		$y->show_element("session_id");
		$y->show_element("submit");
		$y->finish();

}
        echo '</center>';

}

function calculate_price($cart=array())
{
  // sum total price for all items in shopping cart
  $price = 0.0;
  if(is_array($cart))
  {
    foreach($cart as $prodid => $qty)
    {  
if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){
      $query = "select item_price from products where idcode='$prodid'";
      $result = mysql_query($query);
      if ($result)
      {
        $row=mysql_fetch_array($result);
        $item_price = $row["item_price"];
        $price +=$item_price*$qty;
}
      }
    }
  }
  return $price;
}

function calculate_tax($cart=array())
{
  // calculate taxes for all items in shopping cart
  $price = 0.0;
  if(is_array($cart))
  {
    foreach($cart as $prodid => $qty)
    {  
      $query = "select item_price, taxcode from products where idcode='$prodid'";
      $result = mysql_query($query);
      if ($result)
      {
        $row=mysql_fetch_array($result);
        $item_price = $row["item_price"];
        $taxcode = $row["taxcode"];
        $price +=(($item_price*$qty)*$taxcode);
      }
    }
  }
  return $price;
}

function calculate_shipping($cart=array())
{
  // calculate shipping for all items in shopping cart
  $price = 0.0;
  if(is_array($cart))
  {
    foreach($cart as $prodid => $qty)
    {  
if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){
      $query = "select shipcost from products where idcode='$prodid'";
      $result = mysql_query($query);
      if ($result)
      {
        $row=mysql_fetch_array($result);
        $shipping = $row["shipcost"];
        $price +=($shipping*$qty);
}
      }
    }
  }
  return $price;
}

function calculate_items($cart=array())
{
  // sum total items in shopping cart
  $items = 0;
  if(is_array($cart))
  {
    foreach($cart as $prodid => $qty)
    {  
if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){
      $items += $qty;
}
    }
  }
  return $items;
}

?>
