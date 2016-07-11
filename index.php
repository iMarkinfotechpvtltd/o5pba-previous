<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
include('./includes/top.php');
?>
<style>
.mainpage {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>

<table border="0" cellspacing="0" cellpadding="5" width="100%" class="mainpage">
	<tr>
		<td valign="top" colspan="3">
                <img src="/images/sponsors/2015Year ofSport-5Pin.png"  HEIGHT=160 ALT="Year of Sport"><br>
		<p><H3>The Ontario 5 Pin Bowlers' Association (O5PBA) is recognized as the Sport Governing body for 5 pin bowling in Ontario.</H2>
		<H3>It is an association, which is administered by bowlers interested in promoting and organizing the sport of 5 pin bowling. The O5PBA has been in existence for almost 40 years and has grown to encompass 14 geographical "Zone" Associations and 35 "Decentralized" or House Associations within these Zones. Membership over the last few years has had a real roller coaster ride and now is approximately 10,000 bowlers.</H2></p><br>
<p><H2>Mission Statement</H2><H3>The O5PBA provides leadership and structure to enable the sport of 5 pin bowling to flourish in Ontario. We are unwavering in our commitment and the development of our athletes according to the principles of CS4L. We will strive to provide all Ontarians with fair and equal access to participation and personal growth through programs for recreational, competitive and elite athletes as well as coaching and officials programs.</H3></p><br>

<center>

<!--                <img src="/images/sponsors/Heritage_Sports_Canada.jpg"  HEIGHT=50 ALT="Hertige Sports of Canada"><br>
--!>
<br>            

</center>

		</td>
	</tr>
	<tr>
		<td width="700">
                        <h2>Recent Changes</h2>
			<? include('newest.php'); ?>
		</td>
		<td width="*">
                <center> 
<!--		 <a href='https://www.ailife.com/avantages/secure/sgnuj/'><img src="/images/sponsors/AIL-Banner-Ad.gif"  HEIGHT=200 ALT="AIL Canada"><br>
--!>
<br>

		 <a href='/images/videos/Canadian5-PinBowling.mov'><img src="/images/videos/Canadian5-PinBowling.jpg"  HEIGHT=200 ALT="Canadina 5-Pin Bowling"><br>As seen on the CBC - George Stroumboulopoulos Show<br><br>

<?
		if (file_exists($htdocsdir.'image1.jpg')){
			echo '<img src="/image1.jpg"><br><br>';
		}
		if (file_exists($htdocsdir.'image2.jpg')){
			echo '<img src="/image2.jpg">';
		}
		?>
                </center>
		</td>
		<td width="150">
<center>
			<a href='/aboutus/perfect.php'><IMG SRC="/images/imptstuff/450games.jpg" ALT="" border=0></a><br><br>
<!--			<a href='/kidshelpphone/index.php'><IMG SRC="/images/imptstuff/kidshelpphone.png" ALT="" border=0></a>
-->
</center>
		</td>
	</tr>
	<tr>
		<td>
		</td>
	</tr>
</table>

