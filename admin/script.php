<?php



 $hostname     = "localhost";
 $dbname = "o5pba";
// $username     = "o5pba";
// $password = "secrethello";
 $username     = "root";
 $password = "romba";
 
mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
mysql_select_db($dbname) or DIE('Database name is not available!');
$query = sprintf("select * from bowling_school");
$results = mysql_query($query) ;

while($row = mysql_fetch_array($results)) {
 
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