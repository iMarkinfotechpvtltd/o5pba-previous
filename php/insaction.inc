<?PHP

//session_cache_limiter('private');



if ( $session_id ) {  

                

session_id($session_id);

        

}

         

session_start();

            

connect_db();







   if(!session_is_registered("valid_user"))



    {

	session_register("valid_user");

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

}elseif ($action=='cancelorder'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){                

		session_destroy ();

	      $cart['items'] = "0";

	      $cart['total_price'] = "0.00";

	}

}



	include_once("top.inc");


//Process actions while writing page output



if ($action=='login'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      $valid_user=loginform($HTTP_POST_VARS, $action,'',$cart,$valid_user);



}



}elseif ($action=='bill' || $action=='billedit'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

        $cart=billing_edit($HTTP_POST_VARS, $valid_user,$action, $cart);

}



}elseif ($action=='edit'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      $cart=user_edit($HTTP_POST_VARS, $valid_user, $action, $cart);

}



}elseif ($action=='ship' || $action=='shipedit'){



        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      $cart=shipping_edit($HTTP_POST_VARS, $valid_user, $action, $cart);

}



}elseif ($action=='pay' || $action=='payedit'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      $cart=payment_edit($HTTP_POST_VARS, $valid_user, $action, $cart);

}

}elseif ($action=='auth'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      loginform($HTTP_POST_VARS, $action,'',$cart);

}







}elseif ($action=='checkout'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

            order_form($valid_user, $cart);

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



      order_form($valid_user, $cart);

}





}elseif ($action=='order'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

      echo $computer;

      echo 'Order has been filed.  you will recieve an email shortly.<br><br>';

      echo '<b>Shipping charges may be changed when the order is processed<br>';

      echo ' as they very too greatly depending on items ordered.</b><br>';

}

}elseif ($action=='cancelorder'){

        if(!file_exists('/www/htdocs/browse/closestore.txt')){

echo 'Order has been canceled.  You will have to reselect all items.<br>';

echo 'Click <a href="http://www.trgtech.com/">here</a> to return to TRG\'s web site.<br><Br>';

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




}



	include_once("secure_bottom.inc");


?>

