<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
$t=new viewtournaments('tournament');
$t->postpath='tournament/posts/';
?>
<html>
<head>
<script src="AC_OETags.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
//  -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 7;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 0;
// ------------------------------------------------------------------------
// -->
</script>
</head>
<body>
<script language="JavaScript" type="text/javascript">
<!--
// Version check based upon the values entered above in "Globals"
var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
// Check to see if the version meets the requirements for playback
if (hasReqestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	 window.location='<? echo "album2.php?key=".$key."&id=".$id; ?>';
} else {  // flash is too old or we can't detect the plugin
	window.location='errpage.html';
}
// -->
</script>
<noscript>
	<span style="font-family: sans-serif; font-size: 11pt;">You need Adobe Flash Player 7 to display the photo album.<br>
				Our photo album uses Flash software to bring a unique browsing experience to our users. <br>Please download Adobe Flash Player (free) from:</span><br>
				<a href=http://www.adobe.com/go/getflash/><img border=0 src="noflash.gif"></a>
</noscript>
</body>	

