<?php
//**************************************
//     Page load dropdown results     //
//**************************************
function getTierOne()  {
  global $db;
  $db->query('SELECT * FROM oa_zones ORDER BY name');
  while ($db->next_record()) {
    echo '<option value="'.$db->f('id').'">'.$db->f('name').'</option>';
	}
}

//**************************************
//     First selection results     //
//**************************************
if($_GET['func'] == "zid" && isset($_GET['func'])) { 
   zid($_GET['drop_var']);
}

function zid($drop_var) {  
  $hostname     = "localhost";
 $dbname = "o5pba";
$username     = "root";
$password = "";
mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
mysql_select_db($dbname) or DIE('Database name is not available!');
       
	echo '<select style="width:100px;" name="cid" id="cid">
	      <option value=" " disabled="disabled" selected="selected">-- Centre --</option>';
  $db1 = mysql_query('SELECT * FROM oa_centres WHERE zone_id="'.$drop_var.'" ORDER BY name ASC');
	while ($db=mysql_fetch_array($db1)) {
    echo '<option value="'.$db["id"].'">'.$db["name"].'</option>';
	}
	
	echo '</select>';
	echo "<script type=\"text/javascript\">
$('#wait_2').hide();
	$('#cid').change(function(){
	  $('#wait_2').show();
	  $('#result_2').hide();
      $.get(\"averagebookhelp.php\", {
		func: \"cid\",
		drop_var: $('#cid').val()
      }, function(response){
        $('#result_2').fadeOut();
        setTimeout(\"finishAjax_tier_three('result_2', '\"+escape(response)+\"')\", 400);
      });
    	return false;
	});
</script>";
}


//**************************************
//     Second selection results     //
//**************************************
if($_GET['func'] == "cid" && isset($_GET['func'])) { 
   cid($_GET['drop_var']); 
}

function cid($drop_var)
{  
  $hostname     = "localhost";
 $dbname = "o5pba";
$username     = "root";
$password = "";
mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
mysql_select_db($dbname) or DIE('Database name is not available!');

	$result = mysql_query("SELECT * FROM oa_leagues WHERE centre_id='$drop_var' ORDER BY name") 
	or die(mysql_error());
	                                                                               
	echo '<select style="width:100px;" name="lid" id="lid">
	      <option value=" " disabled="disabled" selected="selected">-- League --</option>';

		   while($lid = mysql_fetch_array( $result )) 
			{
			  echo '<option value="'.$lid['id'].'">'.$lid['name'].'</option>';
			}
	
	echo '</select> ';
  //  echo '<input type="submit" name="submit" value="Submit" />';
}
?>