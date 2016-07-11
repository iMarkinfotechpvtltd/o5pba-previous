<?php
echo "If you do not see the pictures please download the latest \"Flash player\" for your computer.";
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
$t=new viewtournaments('tournament');
$t->postpath='tournament/posts/';
?>
<html>
<head>
<body>
<? $t->view_folder($key, $id); ?>
</body>	

