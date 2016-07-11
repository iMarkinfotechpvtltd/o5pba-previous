<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
$t=new viewtournaments('bowling_school_mini');
$t->postpath='otherevents/posts/';
?>
<html>
<head>
<? $t->view_folder($key, $id); ?>
</body>	

