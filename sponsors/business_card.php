<?
class business_card {
	var $db;
	
	function business_card() {	
		if (!$this->db){
			$this->db=new DB;
		}		
	}
	
	function show_card(){
	
		$this->db->query(sprintf('select * from sponsors where id = "%s"', $_GET['id']));
		while ($this->db->next_record()) {	
			print "<table width=500>
					<tr><th><IMG SRC='../images/sponsors/sponsors_header.png' WIDTH=100 HEIGHT=30 ALT='sponsors header'><br>
					</th></tr><tr><br></tr>
					<tr><td align='center' valign='middle'><IMG SRC='../images/spacer.gif' WIDTH=20 HEIGHT=8>";
			print	"<IMG SRC='../images/business_card/".$this->db->f('business_card')."' ALT='business card'></td></tr>";
			print   "<tr><br><br></tr>";
			print	"<tr align='center' valign='middle'><td ><IMG SRC='../images/spacer.gif' WIDTH=20 HEIGHT=8><a href='./index.php'>
					<IMG SRC='../images/business_card/back_btn.gif' ALT='back' border=0></a></td></tr>";
			print	"</table>";	
		} 
		
		
	}
}
	
?>

<?php 
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include_once('../includes/top.php');

?>
<?

$business_card = new business_card();
$business_card->show_card();

?>

<?php
include_once('../includes/bottom.php');

?>
