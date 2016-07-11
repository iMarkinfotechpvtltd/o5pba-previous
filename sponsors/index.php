<?

class show_sponsor{
	var $database;
	var $ary = array();	
	var $i=0;	
	
	function show_sponsor() {
	
		if (!$this->database){
			$this->database=new DB;
		}		
	}
	
	function plist() {
	
		$this->database->query('select * from sponsorship_level order by id');

		// save the result of the id in sponsrship_level
		while ($this->database->next_record()) {	
			$this->ary[$this->i] = $this->database->f('id');	
			$this->i++;
		} 
		
		
		for ($j=0; $j<count($this->ary); $j++) {
		
		    // if the record is null, skip
			$result = $this->database->query(sprintf('select * from sponsors where sponsorship_level = "%s"', $this->ary[$j]));
			if (mysql_num_rows($result) < 1 ){
	
		    } else { // show the name of sponsorship and the logo
			
				$this->database->query(sprintf('select * from sponsorship_level where id = "%s"', $this->ary[$j]));			
				while ($this->database->next_record()){		
					print "
					<table cellpadding='2' cellspacing='0'>
						<TH><IMG SRC='../images/spacer.gif' WIDTH=20 HEIGHT=8>".$this->database->f('name')."</TH>
						<tr></tr><tr valign='middle'>"; 
							
					// check the width 
					if ($this->database->f('id') == '1') {
						$width = '200px'; $height='160px';
					} else if ($this->database->f('id') == '2') {
						$width = '150px'; $height='120px';
					} else if ($this->database->f('id') == '3') {
						$width = '120px'; $height='120px';
					} else if ($this->database->f('id') == '4') {
						$width = '100px'; $height='100px';
					} else {
				   		$width = '80px'; $height='80px';	
					}				
						
				}
				
				$this->database->query(sprintf('select * from sponsors where sponsorship_level = "%s"', $this->ary[$j]));
			
				while ($this->database->next_record()){	
				
					$result = getimagesize("../images/sponsors/".$this->database->f('logo'));
					
					//auto resize the image
					if($result[0]>0 && $result[1]>0){ 
   						if($result[0]/$result[1]>= $width/$height){ 
    						if($result[0]>$width){
								$result[1]=$height*($result[1]/$result[0]);
    							$result[0]=$width;
								
   							} else {
								$result[0]=$result[0];
								$result[1]=$result[1];
							} 
							
    					}else{ 
					    	if($result[1]>$height){ 
								$result[0]=$width*($result[0]/$result[1]); 
					    		$result[1]=$height; 
					    		    
					    	} else {
								$result[1]=$result[1];
								$result[0]=$result[0];
							}
				  		} 
					}
					print "<td align='left' style='vertical-align:middle;' >"."<IMG SRC='../images/spacer.gif' style='vertical-align:middle;'  WIDTH=20 HEIGHT=8>";

					if ($this->database->f('url') != '') {
						print "<a href='http://".$this->database->f('url')."'>"; 
					} else {
						print "<a href='./business_card.php?id=".$this->database->f('id')."'>";
					}
					
					print  "<img src='../images/sponsors/".$this->database->f('logo')."' alt='LOGO' style='vertical-align:middle;' border=0 width=".$result[0]." height=".$result[1].">".
					 	 "</a>"."</td>";
					
				}
				
				//print "<tr><td></td><td align=center>Become a Sponsor.</td></tr>"; <IMG SRC='../images/spacer.gif' WIDTH=20 HEIGHT=8>
				print "</tr></table>";
			}
		}
	}

}

require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include_once('../includes/top.php');

?>
<?
	
print <<<HERE
	<table width=500>
		<tr><th><IMG SRC="../images/sponsors/sponsors_header.png" WIDTH=100 HEIGHT=30 ALT="sponsors header"><br>
		</th></tr>
		<tr><td>&nbsp;</td></tr>
	</table>	
HERE;

$show_sponsor = new show_sponsor();
$show_sponsor->plist();


?>

<?php
include_once('../includes/bottom.php');

?>
