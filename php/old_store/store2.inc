<?PHP

function creditmask($cardnum){
//('^({a-z}|{A-Z})+', $cc_no)
$last=substr($cardnum, -4);

return '****-****-****-'.$last;
}

function pastdetail($valid_user,$id){

echo '<h1>Order History Details:</h1>';
echo '<h3>Order W'.$id.'</h3>';

echo '<h3>Your Items:</h3><br>';

echo '<table width=100%>';
echo '<tr><td width=80%>Item Name</td>';
echo '<td width=10%>Qty.</td>';
echo '<td width=10%>Price</td></tr>';
$query = "select * from lineitems where invoice_id like 'W$id'";
$result = @mysql_query($query);
    while($items=mysql_fetch_array($result)){

  // query database for all details for a particular book

   $idetail = @mysql_query('select * from products where item_id like "'.$items['product_id'].'"');
   $item = @mysql_fetch_array($idetail);

if ($items['quantity'] != '0'){
echo '<tr><td width=80%>'.$item['item_name'].'</td>';
echo '<td width=10%>'.$items['quantity'].'</td>';
echo '<td width=10%>'.$item['item_price'].'</td></tr>';
}
}

echo '</table>';

$query = "select * from invoices where invoice_id like '$id'";
$result = @mysql_query($query);
$ship=mysql_fetch_array($result);

$query = 'select * from contact where CUSTOMER_I like "'.$ship['customer_id'].'"';
$result = @mysql_query($query);
$customer=mysql_fetch_array($result);

echo '<hr>';
echo '<table width=100%>';
echo '<tr><td width=50%>Billing Information</td><td>Payment Information</td></tr>';
echo '<tr><td>';
echo $customer['CUSTOMER_N'].' '.$customer['CUSTOMER_F'].'<br>';
echo $customer['STREET_ADD'].'<br>';
echo $customer['CITY'].', '.$customer['STATE'].' '.$customer['ZIP_CODE'].'<br>';
echo $customer['COUNTRY'];
echo '</td><td>';
echo '<b>Your order will appear on the following credit card:</b><br>';
echo $customer['NAME_ON_CA'].'<br>';
echo creditmask($customer['CARD_NUMBE']).'<br>';
echo $customer['CARD_TYPE'].'<br>';
echo $customer['EXPIRY_DAT'];

echo '</td></tr>';


echo '<tr><td>Shipping Information</td><td></td></tr>';
echo '<tr><td>';
echo $ship['ship_first'].' '.$ship['ship_last'].'<br>';
echo $ship['ship_address'].'<br>';
echo $ship['ship_city'].', '.$ship['ship_state'].' '.$ship['ship_zip'].'<br>';
echo $ship['ship_country'].'<br>';
echo $customer['PHONE_NUMB'].'<br>';
echo $customer['E_MAIL'];
echo '</td><td></td></tr>';
echo '</table>';
}


function display_userinfo($row=array(),$userid){



		$f = new form;

 $f->add_element(array("type"=>"hidden",

                "name"=>"userid",

                "value"=>$userid

 ));

 $f->add_element(array("type"=>"hidden",

                "name"=>"action",

                "value"=>'edit'

 ));

		$f->add_element(array("type"=>"submit",

			"name"=>"submit",

			"value"=>"Change"

));



        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Customer Info</b></center></td><td>';

        echo '<tr><td width=7%></td><td>';

//Customer Info

echo '<table width=100%>';

echo '<tr><td>Name:</td><td>'.$row["CUSTOMER_N"].' '.$row["CUSTOMER_F"].'</td></tr>';

echo '<tr><td>Billing Address:</td><td>'.$row["STREET_ADD"].'<br>';

echo $row["CITY"].', '.$row["STATE"].'<br>';

echo $row["ZIP_CODE"].'<br>';

echo $row["COUNTRY"].'</td></tr>';

echo '<tr><td>Phone Number:</td><td>'.$row["PHONE_NUMB"].'<br>';

echo '<tr><td>email Address:</td><td>'.$row["E_MAIL"].'</td></tr>';

echo '</table>';

	echo '</td><td width=20%>';

		$f->start();

 $f->show_element("userid");

 $f->show_element("action");

 $f->show_element("submit");

$f->finish();

echo '</td></tr>';

        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Shipping Info</b></center></td><td>';

        echo '<tr><td width=10%></td><td>';

//Shipping Info

$first = $row["SHIPPING_N"];

$last = $row["SHIPPING_F"];

$address = $row["SHIPPING_A"];

$city = $row["SHIPPING_C"];

$state = $row["SHIPPING_S"];

$zip = $row["SHIPPING_Z"];

$country = $row["SHIPPING_Y"];





if ($first==''){

   $first=$row['CUSTOMER_N'];

}

if ($last==''){

   $last=$row['CUSTOMER_F'];

}

if ($address==''){

   $address=$row['STREET_ADD'];

}

if ($city==''){

   $city=$row['CITY'];

}

if ($state==''){

   $state=$row['STATE'];

}

if ($zip==''){

   $zip=$row['ZIP_CODE'];

}

if ($country==''){

   $country=$row['COUNTRY'];

}
echo '<table width=100%>';

echo '<tr><td>Name:</td><td>'.$first.' '.$last.'</td></tr>';

echo '<tr><td>Billing Address:</td><td>'.$address.'<br>';

echo $city.', '.$state.'<br>';

echo $zip.'<br>';

echo $country.'</td></tr>';

echo '</table>';

	echo '</td><td width=20%>';

		$f->start();

 $f->show_element("userid");

 $f->show_element("action");

 $f->show_element("submit");

$f->finish();

echo '</td></tr>';

        echo '<tr><td colspan=3 bgcolor="#C3C3C3"><center><b>Payment Info</b></center></td><td>';

        echo '<tr><td width=10%></td><td>';

//PAYMENT INFO

echo '<table width=100%>';

echo '<tr><td>Name on Card:</td><td>'.$row["NAME_ON_CA"].'</td></tr>';

echo '<tr><td>Card Type:</td><td>'.$row["CARD_TYPE"].'</td></tr>';

echo '<tr><td>Card Number:</td><td>'.creditmask($row["CARD_NUMBE"]).'</td></tr>';

echo '<tr><td>Expiry:</td><td>'.$row["EXPIRY_DAT"].'</td></tr>';

echo '</table>';

	echo '</td><td width=20%>';

		$f->start();

 $f->show_element("userid");

 $f->show_element("action");

 $f->show_element("submit");

$f->finish();

echo '</td></tr>';

}





function getpassword(){

		$l = new form;

		$l->add_element(array("type"=>"text",

			"name"=>"email",

			"value"=>""));

		$l->add_element(array("type"=>"hidden",

			"name"=>"action",

			"value"=>"passwd"));



		$l->add_element(array("type"=>"submit",

			"name"=>"submit",

			"value"=>"Retrive Password"));

		$l->start();

echo '<table width=100%><tr><td width="30%" rowspan=3>Retrive Password</td>';

echo '<td>Email Address';

		$l->show_element("email");

		$l->show_element("action");

echo '</td></tr>';

echo '<tr><td>';

		$l->show_element("submit");

echo '</td></tr></table>';

		$l->finish();

}

function send_password($recipient){

   $query = "select * from contact where E_MAIL like '$recipient'";

   $result = @mysql_query($query);

	if (mysql_num_rows($result) > 0){

   $row=mysql_fetch_array($result);
   $passwd = $row["PASSWORD"];
   $inst='User ID: '.$recipient."\n";
   $inst .='Password: '.$passwd."\n";
   $msg = join ('', file ('password.txt'));
   $msg=str_replace ('<<PASSWD HERE>>', $inst, $msg);
   $msg="$msg";
   $sender = 'support@trgtech.com';
   $subject = 'Your Password'; 
   $mailheaders = "From: $sender<> \n"; 
   $mailheaders .= "Reply-To: $recipient\n\n"; 
   mail($recipient, $subject, $msg, $mailheaders); 
   
   $query = 'update contact set CARD_TYPE="" , CARD_NUMBE="" , NAME_ON_CA="" , EXPIRY_DAT="" where E_MAIL like "'.$recipient.'"';
   echo 'Your password has been sent to your email address. Also, your payment info has been cleared for security reasons.';
 }else{
   echo 'You do not seem to have purchased through our online store before. Please press the button below to create a new account.<br><br><br><center>';
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
   echo '</center>';
  }


}



function send_confirm($recipient){



$msg = join ('', file ('confirm.txt'));



$sender = 'sales.desk@trgtech.com';



$subject = 'Your Web Order'; 





$mailheaders = "From: $sender<> \n"; 

$mailheaders .= "Reply-To: $recipient\n\n"; 



mail($recipient, $subject, $msg, $mailheaders); 





}



function showlist($search, $start){

if ($start==''){

$start="0";

}



      $query='select * from categories where catid like "'.$search.'"';

      $result = @mysql_query($query);



      $row=mysql_fetch_array($result);

      $val=$row['catname'];



      $query='select * from products where category_id like "'.$val.'" order by item_price desc  LIMIT '.$start.', 10';



$result = @mysql_query($query) or die (print mysql_error());

echo 'Click Name/Picture for more details.<br>';

            while($row=mysql_fetch_array($result)){

browsedetail($row);

}
echo 'For more items in this category, click these pages:<br>';
page_list('select * from products where category_id like "'.$val.'"','browseat', $start, $search);



}

        function page_list($table,$action, $inbound="0",$search){

                 $page=0;

                 $end=false;

                 $results = mysql_query($table);

                 $max_records = mysql_num_rows($results);

                 for($i=(-80);$i<=80;$i=($i+10)){

                     $page=((($inbound+$i)/10)+1);

                     if($i==-80 && $page > 1){echo '...';}

                     if($i==0){echo ' | '.$page;}

                     elseif($page > 0 && $inbound+$i < $max_records){

                          echo ' | <a href="index.php?action='.$action.'&inbound='.($inbound+($i)).'&cat='.$search.'">'.$page.'</a>';

                     }

                     if($inbound+$i < $max_records && $i==80){

                           echo ' | ...';

                     }

                 }

        }



function file_order($userid, $cart=array(), $ref){

   $query = "select * from contact where E_MAIL like '$userid'";

   $result = @mysql_query($query);



   $row=mysql_fetch_array($result);

   $usernum = $row["CUSTOMER_I"];



  $date = date("Y-m-d");



$first = $row["SHIPPING_N"];

$last = $row["SHIPPING_F"];

$address = $row["SHIPPING_A"];

$city = $row["SHIPPING_C"];

$state = $row["SHIPPING_S"];

$zip = $row["SHIPPING_Z"];

$country = $row["SHIPPING_Y"];





if ($first==''){

   $first=$row['CUSTOMER_N'];

}

if ($last==''){

   $last=$row['CUSTOMER_F'];

}

if ($address==''){

   $address=$row['STREET_ADD'];

}

if ($city==''){

   $city=$row['CITY'];

}

if ($state==''){

   $state=$row['STATE'];

}

if ($zip==''){

   $zip=$row['ZIP_CODE'];

}

if ($country==''){

   $country=$row['COUNTRY'];

}



$sess=trim(session_id());


$total=number_format($cart["grand_total"],2);

$query="insert into invoices values ('', '".$usernum."','','','','','','".$ref."','','".$date."','".$total."', '".$sess."','".$first."','".$last."','".$address."','".$city."','".$state."','".$zip."','".$country."')";



$result = @mysql_query($query) or die (print mysql_error());


  $query = "select invoice_id from invoices where sessid like '$sess' and amount like '$total'";
//echo "select invoice_id from invoices where sessid like '$sess' and amount like '$total'";


$result = @mysql_query($query) or die (print mysql_error());



   $result = @mysql_query($query);



   $row=mysql_fetch_array($result);

    $orderid = $row["invoice_id"];



  $orderid='W'.$orderid;



$fp = fopen ("/home/trg/invoice.txt", "a+");

$string = $orderid.chr(9).'W'.$usernum.chr(9).chr(9).$row["ship_method"].chr(9).$row["ship_piece"];

$string = $string.chr(9).$row["ship_person"].chr(9).$row["ref_site"].chr(9).$row["salesperson"];

$string = $string.chr(9).$date.chr(9).$first.chr(9).$last;

$string = $string.chr(9).$address.chr(9).$city.chr(9).$state;

$string = $string.chr(9).$zip.chr(9).$country."\n";



$er = fwrite ($fp, $string);

$er = fclose ($fp);





$fp = fopen ("/home/trg/line_items.txt", "a+");

  foreach($cart as $prodid => $quantity)

  {

if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){

    $item = get_item_details($prodid);
$id=ereg_replace('&gt;', '>', $item['item_id']);
$id=ereg_replace('&lt;', '<', $id);
$id=ereg_replace('&quot;', "\"", $id);
$id=ereg_replace('&amp;', '&', $id);

//    $id=$item['item_id'];

if ($id==''){
        $id=$prodid;
}


    $query = "delete from lineitems where  

              invoiceid = '$orderid' and product_id =  '$id'";

    $result = mysql_query($query);

    $query = "insert into lineitems values

              ('$orderid', '$id', $quantity)";




    $text=$orderid.chr(9).$id.chr(9).$quantity."\n";

    $er = fwrite ($fp, $text);

    $result = mysql_query($query) or die (print mysql_error());

   }

  }

$shipcst["Newfoundland"]="NFLD";        
$shipcst["Nova Scotia"]="NS";   
$shipcst["New Brunswick"]="BC";   
$shipcst["Prince Edward Island"]="PEI";   
$shipcst["Quebec"]="PQ";   
$shipcst["Ontario"]="ON";   
$shipcst["Manitoba"]="MB";   
$shipcst["Saskatchewan"]="SK";   
$shipcst["Alberta"]="AB";   
$shipcst["British Columbia"]="BC";   
$shipcst["Nunavut"]="NUN";   
$shipcst["Northwest Territories"]="NWT";   
$shipcst["Yukon Territory"]="YT";   

if ($cart['mail']!=''){
    $text=$orderid.chr(9).'Mail'.$shipcst[$state].chr(9).$quantity."\n";
}elseif($cart['xpress']!=''){
    $text=$orderid.chr(9).'Xpress'.$shipcst[$state].chr(9).$quantity."\n";
}elseif($cart['puro']!=''){

$text=$orderid.chr(9).'Priority'.$shipcst[$state].chr(9).$quantity."\n";
}
//  foreach($cart as $prodid => $quantity){
//	echo $prodid.'<br>';
//  }

  foreach($cart as $prodid => $quantity){
   $secret = @mysql_fetch_array(@mysql_query('select * from products where item_id like "'.$items['product_id'].'"'));
   
   if ($temp = stristr($secret['category_id'],"computer")){
        $computer='Your order contains a computer.  You need to call TRG at either 905.841.9966 or 1.877.941.9966 for special details about your order.';}
  }
$er = fclose ($fp);


send_confirm($userid);



      session_destroy ();
return $computer;
}

function suckinfile($action){



      $file = join ('', file ($action));

      $contact='<center>For order information contact<br><font class=trg> TRG Computer Solutions</ font><br> by phone toll free at<br><font class=phone> 1.877.941.9966</font><br>or by email at <br><a href="mailto:sales.desk@trgtech.com">sales.desk@trgtech.com</a>  .<br></center>';

//include ($action);

//      $file=str_replace ('<--contact-->', $contact, $file);
      $file=str_replace ('<html>', '<tony>', $file);
      $file=str_replace ('</html>', '</tony>', $file);

      $file=str_replace ('<body', '<bdy', $file);
      $file=str_replace ('</body>', '</bdy>', $file);
      $file=str_replace ('<head>', '<winston>', $file);
      $file=str_replace ('</head>', '</winston>', $file);

      echo $file;


}

function cleanupfile($file){

$file=str_replace ('&#38;', '_', $file);
$file=str_replace ('&#38', '_', $file);
$file=str_replace ('&', '_', $file);
$file=str_replace (';', '_', $file);
$file=str_replace ('/', '_', $file);
$file=str_replace ('(', '_', $file);
$file=str_replace ('\\', '_', $file);
$file=str_replace (')', '_', $file);
$file=str_replace (' ', '_', $file);

return $file;

}
function cleanupdetails($text){

$text=str_replace ('&lt;', '<', $text);
$text=str_replace ('&gt;', '>', $text);
$text=str_replace ('&amp;', '&', $text);

return $text;

}



function itemdetail($post=array()){



$prodid=$post["id"];



$item = get_item_details($prodid);



echo '<table width=100% border=1><tr><td width="30%">';



$file = '/products/'.cleanupfile($item["item_id"]).'.jpg';

//echo $file;

if(file_exists('/www/htdocs'.$file)){
 $size = GetImageSize ('/www/htdocs'.$file);

	echo '<img src="'.$file.'" '.$size[3].'">';

}else{
echo '<center><font size=2><b>Image Currently Unavailable</b></font></center>';
}
echo '</td><td>';

    echo 'Product Name: '.$item["item_name"].'<br>';

    echo 'Product #: '.$item["item_id"].'<br>';

if ($item['item_price']=='0.00'){
    echo 'Price / Unit: <a href="http://www.trgtech.com/browse/index.php?action=our_location.html">Contact Us for pricing</a><br>';
}else{
    echo 'Price / Unit: :'.$item['item_price'].':<br>';
}
if ($item['shipcost']!='0.00'){
echo 'Shipping / Unit: '.$item['shipcost'].'<br>';
}
quickadd($item["item_id"]);
echo '</td></tr>';

$file = '/www/htdocs/store/products/'.cleanupfile($item["item_id"]).'.txt';

if(file_exists($file)){

    echo '<tr><td colspan=2>Description:<br>';
    include ($file);
    echo '</td></tr>';

}
echo '</table>';
quickadd($item["item_id"]);
echo '</td></tr>';

}



function searchform(){

		$l = new form;

		$l->add_element(array("type"=>"text",

			"name"=>"value",

			"value"=>""));

		$l->add_element(array("type"=>"hidden",

			"name"=>"action",

			"value"=>"showsearch"));

		$l->add_element(array("type"=>"submit",

			"name"=>"submit",

			"value"=>"Search"));

		$l->start();

echo '<table width=100%><tr><td width="30%" rowspan=3>Search</td>';

echo '<td>Search For';

		$l->show_element("value");

echo '</td></tr>';

echo '<tr><td>';

		$l->show_element("action");

		$l->show_element("submit");

echo '</td></tr></table>';

		$l->finish();

}



function cleanuplable($current){



		if ($current == '0'){

				$new = 'item_id';

		}elseif ($current == '1'){

				$new = 'category_id';

		}elseif ($current == '2'){

				$new = 'item_name';

		}elseif ($current == '3'){

				$new = 'item_description';

		}elseif ($current == '4'){

				$new = 'vendor_id';

		}elseif ($current == '5'){

				$new = 'vendor_part';

		}elseif ($current == '6'){

				$new = 'item_price';

		}elseif ($current == '7'){

				$new = 'shipcost';

		}elseif ($current == '8'){

				$new = 'taxcode';

		}elseif ($current == '9'){

				$new = 'status';

		}elseif ($current == '10'){

				$new = 'deleted';

		}



				return $new;

}





function browsedetail($post=array()){



$prodid=$post["id"];
if ($prodid==0){
$prodid=$post["idcode"];
}

$item = get_item_details($prodid);

      $query='select * from categories where catname like "'.$item["category_id"].'"';
      $result = @mysql_query($query);
      $row=mysql_fetch_array($result);
      $catid=$row['catid'];

echo '<table width=100% border=0>';
echo '<tr><td rowspan=4 width=75><a href="index.php?action=details&id='.$item["idcode"].'">';

$file = '/products/'.cleanupfile($item["item_id"]).'.jpg';

if(file_exists('/www/htdocs'.$file)){
//echo $file;
 $size = GetImageSize ('/www/htdocs'.$file);

  if ($size[0]>$size[1]){
		$width=72;
		$height=72/$size[0]*$size[1];

  }else{
		$height=72;
		$width=72/$size[1]*$size[0];

  }

	echo '<img src="'.$file.'" width='.$width.' height='.$height.'>';
}else{
echo '<center><font size=2><b>Image Currently Unavailable</b></font></center>';
}


echo '</a></td>';

echo '<td width = 300 class=topLeft><a
href="index.php?action=details&id='.$item["idcode"].'">'.cleanupdetails($item["item_name"]).'</a></td>';

echo '<td width=20%><center>$'.$item['item_price'].'</center></td><td>';
quickadd($item["item_id"]);
echo '</td></tr>';

echo '</table><hr>';

}



function searchquery($post=array()){

$wher='(item_name like "%'.$post['value'].'%")';
$wher .=' or ';
$wher .='(item_id like "'.URLDECODE($post['value']).'")';
$wher .=' or ';
$wher .='(vendor_part like "'.URLDECODE($post['value']).'")';
$wher .=' or ';
$wher .='(vendor_part like "%'.$post['value'].'%")';
$wher .=' or ';
$wher .='(item_id like "%'.$post['value'].'%")';
$wher .=' or ';
$wher .='(category_id like "%'.$post['value'].'%")';


$wher=' where'.$wher;
//echo $wher;
return 'select * from products'.$wher.' order by item_price desc;';

}



 function create_table($file, $tablename){

          $a = dbase_open($file, 0);

          $rec = dbase_get_record_with_names($a, 1);

          echo "create table $tablename(";

          for($i=0;$i <= dbase_numfields($a);$i++){

              list ($key, $val) = each ($rec);

              echo "$key varchar(255)";

              if($i != dbase_numfields($a)){

                 echo ",<br>";

              }else{

                 echo ')\\g';

              }

          }

 }



 function import_dbase($table, $file){

$inserted="0";

$deleted="0";

$updated="0";
//create_table($file, $table);
          $a = dbase_open($file, 0);

echo dbase_numrecords($a).' records to edit.<br>';

          for($i=1; $i <= dbase_numrecords($a); $i++){

              $rec = dbase_get_record_with_names($a, $i);

              list ($key, $val) = each ($rec);

              $val1='"'.htmlspecialchars (trim($val), ENT_QUOTES).'"';

              $delete=$val1;

              for($j=1; $j <= dbase_numfields($a); $j++){

                  list ($key, $val) = each ($rec);

                  $key=cleanuplable($j);

                  if ($key=='status'){

                        $job=trim($val);

                  }

                  if ($key!='deleted'){

                        $val1=$val1.', "'.htmlspecialchars(trim($val), ENT_QUOTES).'"';

}

              }

          if ($job=='INSERT' || $job=='UPDATE'){

                  $count++;

                  $inserted++;

                  $val1='"", '.$val1;

                  $rSQL =mysql_query("INSERT INTO $table values ($val1)") or die (print mysql_error());

          }elseif ($job=='DELETE'){

                  $count++;

                  $deleted++;

                  $rSQL =mysql_query("DELETE FROM $table where item_id like $delete") or die (print mysql_error());

          }elseif ($job=='UPDATE2'){

                  $count++;

                  $updated++;

                  $rSQL =mysql_query("INSERT INTO $table values ($val1)") or die (print mysql_error());

          }else{

                  $count++;

                  $inserted++;

                  $val1='"", '.$val1;

                  $rSQL =mysql_query("INSERT INTO $table values ($val1)") or die (print mysql_error());
	  }

          }

echo $inserted.' records added.<br>';

echo $deleted.' records deleted.<br>';

echo $updated.' records updated.<br>';

echo 'In total '.$count.' records changed.<br>';

unlink ($file);

 }

 

function do_update(){
if (file_exists ('/home/trg/products.dbf')){
	$rSQL =mysql_query("delete from products;") or die (print mysql_error());
	     import_dbase('products', '/home/trg/products.dbf');
}


   // query database for a list of categories

   $query = "select category_id from products"; 

   $result = @mysql_query($query);

   if (!$result)

     return false;

$cats=array();

            while($row=mysql_fetch_array($result)){

$val=$row['category_id'];

$cats[$val]=$val;

}

$result = @mysql_query('DELETE FROM categories');

    foreach($cats as $key => $val)

    {  

$query='insert into categories values ("'.$key.'", "")';

$result = @mysql_query($query) or die (print mysql_error());

}

texts();
//images(0,1000);

      echo 'Databases imported<br>';

}



 function browsemenu(){



   $query = "select * from categories"; 

   $result = @mysql_query($query);

   if (!$result)

     return false;





    while($row=mysql_fetch_array($result)){



echo '<a href="index.php?action=browseat&cat='.$row['catid'].'">'.$row['catname'].'</a><br>';



    }

}



function showsearch($query){



$result = @mysql_query($query) or die (print mysql_error());


		if (mysql_num_rows($result) > 0)
		{

            while($row=mysql_fetch_array($result)){

browsedetail($row);

}

}else{

echo 'No Results Found. Please Search again.';

      searchform();

}



}

function pastorders($valid_user){



echo 'past orders:';





      $query='select * from contact where E_MAIL like "'.$valid_user.'"';

      $result = @mysql_query($query);



      $row=mysql_fetch_array($result);

      $val=$row['CUSTOMER_I'];



      $query='select * from invoices where customer_id like "'.$val.'"';

      $result = @mysql_query($query);



echo '<table><tr><td>Order Number</td><td>Date</td></tr>';



    while($row=mysql_fetch_array($result)){



echo '<tr><td>';

echo '<a href="index.php?action=orderdetail&id='.$row['invoice_id'].'">';

echo 'W'.$row['invoice_id'].'</a></td>';

echo '<td>'.$row['date'].'</td></tr>';

    }



echo '</table>';



}

function winston(){
echo 'This is to certify that the site you are viewing uses software coded by Winston.';
}


function getimage($image, $url){

$f1 = fopen($url, "r");

$stuff = fread($f1,"1000000");

fclose($f1);

$thefile = fopen('/www/htdocs/products/'.$image.'.jpg',"wb+");

fwrite($thefile, $stuff);

fclose($thefile);

}
function images($start,$count){

$file='http://10.5.1.2/FMPro?-db=Web+Products_.fp5&-lay=Products&-format=images.htm&-max='.$count.'&-skip='.$start.'&-find';

$fcontents = join ('', file ($file));
$fcontents = stristr ($fcontents, '<start>');
$fcontents =substr($fcontents, 7);
$pos = strpos($fcontents, "<break>");
//echo $pos;
while ($pos != 0){
    $code=substr($fcontents, 0, $pos);
    $fcontents =substr($fcontents, $pos+7);
    $pos = strpos($fcontents, "<break>");
    $img=substr($fcontents, 0, $pos);
    $fcontents =substr($fcontents, $pos+7);
    $pos = strpos($fcontents, "<break>");
    $pic = strpos($img, "key=&");
    if ($pic == false) {
        $img=trim($img);
        getimage(cleanupfile($code), 'http://10.5.1.2/'.$img);
    //    echo 'http://10.5.1.2/'.$img.'<br>';
    }
}
}
function texts(){
$file='http://10.5.1.2/FMPro?-db=Web+Products_.fp5&-lay=Products&-format=texts.htm&-max=9999999&-find';

$fcontents = join ('', file ($file));
$fcontents = stristr ($fcontents, '<start>');
$fcontents =substr($fcontents, 7);

$pos = strpos($fcontents, "<break>");
while ($pos != 0){
    $code=substr($fcontents, 0, $pos);
    $fcontents =substr($fcontents, $pos+7);
    $pos = strpos($fcontents, "<break>");
    $stuff=substr($fcontents, 0, $pos);
    $fcontents =substr($fcontents, $pos+7);
    $pos = strpos($fcontents, "<break>");
    $img=trim($img);
    $code=trim($code);
    if ($code!=''){
        $stuff=str_replace(chr(10).chr(13),'<br><br>',$stuff);
        $stuff=str_replace(chr(13).chr(10),'<br><br>',$stuff);
        $stuff=str_replace(chr(10),'<br>',$stuff);
        $stuff=str_replace(chr(13),'<br>',$stuff);
 //       echo $code.'<br>';
        $code=cleanupfile($code);
        $thefile = fopen('/www/products/'.$code.'.txt',"wb+");
        fwrite($thefile, $stuff);
        fclose($thefile);
    }
}

}
function dnsfix($cart=array()){
echo '<table width=597><tr><td>';
echo 'Due to technical difficulties, we cannot currently process your order online.';
echo 'To place your order, please copy everything below the line into an email, complete';
echo 'the contact information at the top of the message and send it to';
echo 'us at sales.desk@trgtech.com .';

        echo '<hr>';
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Customer Info</b><br>\n";
echo "First Name:<br>\n";
echo "Last Name:<br>\n";
echo "Street Address:<br>\n";
echo "City:<br>\n";
echo "Province:<br>\n";
echo "Postal Code:<br>\n";
echo "Country:<br>\n";
echo "Phone Number:<br>\n";
echo "Email Address:<br>\n<br>\n";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Shipping Info (Leave BLANK if same as above)</b><br>\n";
echo "First Name:<br>\n";
echo "Last Name:<br>\n";
echo "Address:<br>\n";
echo "City:<br>\n";
echo "State:<br>\n";
echo "Zip:<br>\n";
echo "Country:<br>\n<br>\n";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Payment Info</b><br>\n";
echo "Name on Card:<br>\n";
echo "Card Type<Visa or Mastercard>:<br>\n";
echo "Card Number:<br>\n";
echo "Exp. Date:<br>\n<br>\n";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Affiliate Program</b><br>\n";
echo "If you are a member of an organization which has a discount agreement with us, please fill in this section, otherwise leave it blank.  Current affiliates are: 5-pin bowling association.<br>\n";
echo "Affiliate organization:<br>\n";
echo "Membership number:<br>\n<br>\n<br>\n";

    echo "Prod. ID  -|-  Name  -|-  Unit Price  -|-  Quantity  -|-  Total Price<br>\n";

  //display each item as a table row
  foreach ($cart as $prodid => $qty)
  {
if ($prodid !='items' && $prodid!='total_price' && $prodid!='total_tax' && $prodid!='total_shipping' && $prodid!='grand_total' && $prodid!='parts'){
    $item = get_item_details($prodid);
    echo $item["item_id"].'  -|-  ';
    echo $item["item_name"].'  -|-  ';
    echo $item["item_price"].'  -|-  ';
    echo $qty.'  -|-  ';
    echo number_format($item["item_price"]*$qty,2)."  -|-  <br>\n";
}
  }
echo "<br>\n<br>\n";


        echo 'Sub total:  $'.number_format($cart['total_price'],2)."<br>\n";
        echo 'Tax total:  $'.number_format($cart['total_tax'],2)."<br>\n";
//        echo 'Shipping Cost:  $'.number_format($cart['total_shipping'],2)."<br>\n";
//        echo 'Grand total:  $'.number_format($cart['grand_total'],2)."<br>\n";
        echo '</td></tr></table>';
}

function quickadd($item_id){
   $query = 'select idcode from products where item_id like "'.$item_id.'"'; 

   $result = @mysql_query($query);

   if (!$result){

   }else{
        $row=mysql_fetch_array($result);
        echo '<form method = post>';
        echo '<input type = hidden name = action value = cart>';
        echo '<input type = "text" name = "'.$row['idcode'].'" size = "3">';
        echo '<input type = hidden name = new value = "'.$row['idcode'].'">';
        echo '<input type = submit name = submit value = "Buy Now"></form>';
   }

}
function chooseship(){
echo '<h3>Please select shipping method: </h3>';
echo '<center>';
echo '<table><tr><td>';
   echo '<form method = post>';
   echo '<input type = hidden name = action value = order>';
   echo '<input type = hidden name = "mail" value = "1">';
   echo '<input type = hidden name = new value = "mail">';
   echo '<input type = submit name = submit value = "Postal Mail"></form>';

echo '</td><td>';
   echo '<form method = post>';
   echo '<input type = hidden name = action value = order>';
   echo '<input type = hidden name = "xpress" value = "1">';
   echo '<input type = hidden name = new value = "xpress">';
   echo '<input type = submit name = submit value = "Xpress Post"></form>';
echo '</td><td>';
   echo '<form method = post>';
   echo '<input type = hidden name = action value = order>';
   echo '<input type = hidden name = "puro" value = "1">';
   echo '<input type = hidden name = new value = "Puro">';
   echo '<input type = submit name = submit value = "Courier"></form>';

echo '</td></tr></table>';
echo '<br><br><br><br><Br><i>';
echo "Title and risk of loss to all Product will pass to the customer upon
shipment from TRG's shipping location.  TRG will not issue credits or
replace Products returned due to damage in transit or that are lost in
transit.</i>";

}

?>
