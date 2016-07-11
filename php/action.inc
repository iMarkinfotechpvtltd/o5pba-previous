<?PHP
$absdir = '/home/trg/';
$includedir = $absdir.'includes/';


//session_cache_limiter('private');
if ( $session_id ) {  
	session_id($session_id);
}

session_start();
connect_db();

if ($action=='images'){
        if ($start==0){
                $start=1;  
        }
        if ($count==0){
                $count=100000;
        }
      images($start,$count);
	exit();
}elseif ($action=='text'){
	texts(); 
	exit ();
}

   if(!session_is_registered("valid_user")) {
	session_register("valid_user");
	}

   if(!session_is_registered("cart")) {
      $cart = array();
      session_register("cart");
      $cart['_items'] = "0";
      $cart['_total_price'] = "0.00";
      $cart['_total_tax'] = "0.00";
      $cart['_total_shipping'] = "0.00";
      $cart['_parts'] = "0";
    }
//Process actions before writing page output
if ($cart['_store']=='' || !isset($cart['_store']) || isset($_GET['setstore']) || isset($_POST['setstore'])){
        if (strpos(strtolower($_POST['setstore']),'5pba')!==false){
             $cart['_store'] = $_POST['setstore'];
        }
        if (strpos(strtolower($_GET['setstore']),'5pba')!==false){
             $cart['_store'] = $_GET['setstore'];
        }
}

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
}elseif ($action=='browsetrg'){
        unset ($cart['_store']);
}
if (strpos($_SERVER['PHP_SELF'],'/store/')===false){
	include_once("top.inc");
}else{
	if (strpos(strtolower($cart['_store']),'5pba')===false){
		include_once($includedir."top.inc");
	}else{
		if (file_exists($includedir.$cart['_store'].'.inc')){
			include_once($includedir.$cart['_store'].'.inc');
			include_once($includedir.'store.inc');
		}else{
		        include_once("top.inc");
			echo 'replace top for '.$includedir.$cart['_store'].'.inc'.'<br>';

		}
	}
}

//Process actions while writing page output

if  ($action=='browsedetail'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
	      browsedetail($HTTP_GET_VARS);
	}
}elseif ($action=='details'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
                if (isset($cart['_store'])){ 
                	echo '<center><a href="/store/index.php?action=search">Search</a> | ';
        	        echo '<a href="/store/index.php?action=browse">Browse</a>';
	                echo ' | <a href="/store/index.php?action=browsetrg">Go to TRG Store</a></center><br>';
                }

   	   itemdetail($HTTP_GET_VARS);
	}
}elseif ($action=='cart'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
	    workcart ($cart);
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
		echo '<a href="/store/index.php?action=browse">Browse</a>';
		if (isset($cart['_store'])){
	                echo ' | <a href="/store/index.php?action=browsetrg">Go to TRG Store</a></center><br>';    
		}else{
			echo '</center><br>';
		}
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
                echo '<a href="/store/index.php?action=browse">Browse</a>';
                if (isset($cart['_store'])){
                        echo ' | <a href="/store/index.php?action=browsetrg">Go to TRG Store</a></center><br>';
                }else{
                        echo '</center><br>';
                }
		showlist($HTTP_GET_VARS['cat'], $HTTP_GET_VARS['inbound']);
	}
}elseif ($action=='browsetrg'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
                browsemenu();
        }

}elseif ($action=='browse'){
        if(!file_exists('/www/htdocs/browse/closestore.txt')){
		browsemenu();
	}
}elseif ($action=='subtypes'){
	if(!file_exists('/www/htdocs/browse/closestore.txt')){
		browsemenu($HTTP_GET_VARS['cat']);
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





	include_once("bottom.inc");


?>

¸
