<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include('./includes/top.php');
?>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr>
		<td valign="top" colspan="3">
		<p>The Ontario 5 Pin Bowlers' Association (O5PBA) is recognized as the Sport Governing body for 5 pin bowling in Ontario.</p>
		<p>It is an association, which is administered by bowlers interested in promoting and organizing the sport of 5 pin bowling. The O5PBA has been in existance for almost 40 years and has grown to encompass 15 geographical "Zone" Associations and 35 "Decentralized" or House Associations within these Zones. Membership over the last few years has had a real roller coaster ride and now is approximately 20,000 bowlers.</p>

<center>
        <IMG SRC="/images/spacer.gif" WIDTH=8 HEIGHT=50 ALT=""><img src="/images/sponsors/ontario3.gif" ALT="Ontario Government Logo"><br>
</center>


		</td>
	</tr>
	<tr>
		<td width="*">
			<? include('newest.php'); ?>
		</td>
		<td width="100">
		<?
		if (file_exists($htdocsdir.'image1.jpg')){
			echo '<img src="/image1.jpg"><br><br>';
		}
		if (file_exists($htdocsdir.'image2.jpg')){
			echo '<img src="/image2.jpg">';
		}
		?>
		</td>
		<td width="75">
			<a href='/aboutus/perfect.php'><IMG SRC="/images/imptstuff/450games.jpg" ALT="" border=0></a><br><br>
			<a href='/kidshelpphone/index.php'><IMG SRC="/images/imptstuff/kidshelpphone.png" ALT="" border=0></a>
		</td>
	</tr>
	<tr>
		<td>
		</td>
	</tr>
</table>
<?php
include('./includes/bottom.php');
?>
