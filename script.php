<?php


echo "got here";
 $hostname     = "localhost";
 $dbname = "o5pba";
	 $username     = "root";
 $password = "";

echo "got here";
	mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
// Select the database
	mysql_select_db($dbname) or DIE('Database name is not available!');
$query = sprintf("select * from oa_seasons where name = '2012-13'");
$results = mysql_query($query) ;

while($row = mysql_fetch_array($results)) {
  echo "hi";
  print_r($row);
   
  echo '<br />';
}  
	
/*	
$db2=new DB;
$db2->query('use o5pba');
$db2->query('describe oa_member_bowlings');
while($db2->next_record())
{

}*/
//$db2->query("alter table oa_member_bowlings modify num_games double(10,1)");
?>