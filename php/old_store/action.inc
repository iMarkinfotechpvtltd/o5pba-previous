<?PHP

if ( $HTTP_POST_VARS['session_id'] != '' ) {  
                
session_id($HTTP_POST_VARS['session_id']);
        
}
         
session_start();
            
connect_db();

if ($action=='update'){

      do_update();

}elseif ($action=='text'){
texts(); 
}elseif ($action=='images'){

        if ($start==0){
                $start=1;  
        }
        if ($count==0){
                $count=10;
        }
      images($start,$count);

}else{
if (rand(1,20)==4){
	@images(rand(1,2000),2);
}
      if(!session_is_registered("valid_user"))

      {

            $valid_user='';

            session_register('valid_user');

      }




if ($action=='') {

	$action=$HTTP_GET_VARS["action"];

}

   if(!session_is_registered("cart"))

    {

      $cart = array();

      session_register("cart");

      $cart['items'] = "0";

      $cart['total_price'] = "0.00";

      $cart['total_tax'] = "0.00";

      $cart['total_shipping'] = "0.00";
      $cart['parts'] = "0";
    }


//Process actions before writing page output

if ($action=='cart'){
	if(!file_exists('/www/htdocs/browse/closestore.txt')){
	    $cart = workwithcart ($cart, $new, $save,$HTTP_POST_VARS);
	}
}elseif ($action=='order'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){                
	    $cart = workwithcart ($cart, $new, $save,$HTTP_POST_VARS);

	      $computer=file_order($valid_user, $cart, $ref);

	      $cart['items'] = "0";

	      $cart['total_price'] = "0.00";
	}
}



	include_once("top.inc");

if ($ref=='') {
      session_register("ref");
      $ref=$HTTP_REFERER;

}


if (strpos("w".strtolower($HTTP_HOST),"secure")!=false){

    echo '<b>This is a SECURE Server</b><br>';

}


//Process actions while writing page output

if ($action==''){

      suckinfile('home.html');


}elseif ($action=='login'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      $valid_user=loginform($HTTP_POST_VARS, $action);

	}

}elseif ($action=='edit'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      user_edit($HTTP_POST_VARS, $valid_user,'edit');
}


}elseif ($action=='submitedit'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      user_edit($HTTP_POST_VARS, $valid_user,$action);

}

}elseif ($action=='auth'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      loginform($HTTP_POST_VARS, $action);
}


}elseif ($action=='checkout'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      if ($valid_user==''){

            user_edit($HTTP_POST_VARS,'','');

      }else{

            order_form($valid_user, $cart);

      }
}
}elseif ($action=='chooseship'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
	chooseship();

}
}elseif ($action=='details'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      itemdetail($HTTP_GET_VARS);
}


}elseif ($action=='cart'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
    workcart ($cart);
}
}elseif ($action=='newcheckout'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      $valid_user=$HTTP_POST_VARS['userid'];

      order_form($valid_user, $cart);
}


}elseif ($action=='order'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      echo $computer;
      echo 'Order has been filed.  you will recieve an email shortly.';
      echo '<b>Shipping charges will be added when the order is processed';
      echo ' as they very too greatly depending on items ordered.</b>';
}
}elseif ($action=='browsedetail'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      browsedetail($HTTP_GET_VARS);
}


}elseif ($action=='search'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      searchform();
}
}elseif ($action=='item'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
if ($HTTP_POST_VARS['value']!=''){
      showsearch(searchquery($HTTP_POST_VARS));
}elseif ($HTTP_GET_VARS!=''){
      showsearch(searchquery($HTTP_GET_VARS));
}else{
      searchform();
}
}
}elseif ($action=='showsearch'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
echo '<center><a href="/store/index.php?action=search">Search</a> | ';  
echo '<a href="/store/index.php?action=browse">Browse</a></center><br>';
if ($HTTP_POST_VARS['value']!=''){
      showsearch(searchquery($HTTP_POST_VARS));
}elseif ($HTTP_GET_VARS!=''){
      showsearch(searchquery($HTTP_GET_VARS));
}else{
      searchform();
}
}

}elseif ($action=='browseat'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){
echo '<center><a href="/store/index.php?action=search">Search</a> | ';
echo '<a href="/store/index.php?action=browse">Browse</a></center><br>';
      showlist($HTTP_GET_VARS['cat'], $HTTP_GET_VARS['inbound']);


}
}elseif ($action=='browse'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      browsemenu();
}


}elseif ($action=='orderhistory'){
//

}elseif ($action=='orderdetail'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      if ($valid_user!=''){

	      pastdetail($valid_user, $HTTP_GET_VARS['id']);

      }else{

	      echo 'You Must Log in First!';

      }

}

}elseif ($action=='passwd'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      send_password($HTTP_POST_VARS['email']);

}

}elseif ($action=='getpasswd'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
      getpassword();
}


}elseif (strpos($action,".htm")!=false){

if(file_exists($action)){

suckinfile($action);

}else{
      echo '<BODY>';
      echo '<H1>Not Found</H1>';
      echo 'The requested URL was not found on this server.<P>';
      echo '<HR>';
      echo '<ADDRESS>Apache/1.3.19 Server at www.trgtech.com Port 80</ADDRESS>';
}


}else{
	suckinfile('home.html');

}

        if(file_exists('/www/htdocs/browse/closestore.txt')){
	        suckinfile('/www/htdocs/browse/closestore.html');
	}


	include_once("bottom.inc");
}
?>

