<?php
require_once('/home/httpd/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
	$db=new DB;             

	$db->query('SELECT * FROM halloffame;');
	while($db->next_record()){
		if (!file_exists(sprintf('./pictures/%s.jpg',$db->f('id')))){
			echo sprintf('Missing picture for %s %s.  Id # %s<br>',$db->f('first'),$db->f('last'),$db->f('id'));
        	}
	}
?>
