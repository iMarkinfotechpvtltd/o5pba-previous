<html>
<head>
<!--   johnf@trgtech.com  -->
<? require_once('/home/httpd/vhosts/c5pba.ca/httpdocs/classes/prepend.php'); ?>
<title>About the C5PBA</title>
<link REL="shortcut icon" HREF="http://www.c5pba.ca/c5logo.ico"></link>
<link REL="icon" HREF="http://www.c5pba.ca/c5logo.ico"></link>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">


<script language="JavaScript">
<!--
function OpenNewWindow(url,winwidth,winheight) 
{
NewWindow=window.open(url,'descr','toolbar=yes,location=no,directories=no,status=no,menubar=no,scrollbar=no,scrollbars=yes,resizable=no,copyhistory=no,width='+winwidth+',height='+winheight)
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
} 
-->
</script>
<style>
<!--
<?
switch ($_SERVER['PHP_SELF']){
	case '/about_us/main.php':
	echo '#whatsnew { position:absolute; left:40px; top:350px; }';
	break;

	case '/mastercard_app/mastercard.php':
	case '/achievements/life_members.php':
	case '/achievements/award_programs.php':
	echo '#whatsnew { position:absolute; left:40px; top:380px; }';
	break;
	
	case '/about_us/history.php':
	echo '#whatsnew { position:absolute; left:40px; top:480px; }';
	break;
	
	default:
	echo '#whatsnew { position:absolute; left:40px; top:200px; }';
	break;
}
?>
TD{
	font-size:8pt;
	font-family: "Arial, Helvetica, sans-serif";
	text-align:justify;
	vertical-align:top;
}
TH{
	font-size:10pt;
	font-family: "Arial, Helvetica, sans-serif";
	text-align:left;
	vertical-align:top;
}
.newstuffpdf{
	font-size:8pt;
	font-family: "Arial, Helvetica, sans-serif";
	color: 990100;
}
.dark{
	background-color: E6E6E6;
	}
.light{
	background-color: F2F2F2;
}
A:active{
	color: 990100;
	text-decoration:none;
}
A:link{
	color: 990100;
	text-decoration:none;
}
A:visited{
	color: 990100;
	text-decoration:none;
}
A.scroller:link{
        color:#ff0000;
        text-decoration:none;
}
A.scroller:visited{
        color:#ff0000;
        text-decoration:none;
}
A.scroller:active{
        color:#ff0000;
        text-decoration:none;
}

-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000" onLoad="MM_preloadImages('/working_files/about_us_roll.gif','/working_files/achievements_roll.gif','/working_files/events_tournaments_roll.gif','/working_files/instructions_coaching_roll.gif','/working_files/rules_re
gulations_roll.gif')" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">

<table width="770" height="116">
  <tr> 
    <td class="menu" background="/working_files/top_navigation.jpg" valign="top"> <br>
      <table width="760">
        <tr> 
          <td class="menu" width="106" height="40" style='vertical-align: middle;'><a href="/about_us/main.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('about','','/working_files/about_us_roll.gif',1)"><img name="about" border="0" src="/working_files/about_us.gif" width="106" height="20"></a></td>
          <td class="menu" width="106" height="40" style='vertical-align: middle;'><a href="/achievements/main.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('achievements','','/working_files/achievements_roll.gif',1)"><img name="achievements" border="0" src="/working_files/achievements.gif" width="106" height="20"></a></td>
          <td class="menu" width="106" height="40" style='vertical-align: middle;'><a href="/tournament/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('events','','/working_files/events_tournaments_roll.gif',1)"><img name="events" border="0" src="/working_files/events_tournaments.gif" width="106" height="40"></a></td>
          <td class="menu" width="106" height="40" style='vertical-align: middle;'><a href="/instructions_coaching/main.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('instruction','','/working_files/instructions_coaching_roll.gif',1)"><img name="instruction" border="0" src="/working_files/instructions_coaching.gif" width="106" height="40"></a></td>
          <td class="menu" width="80"><a href="/rules_regulations/main.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('rules','','/working_files/rules_regulations_roll.gif',1)"><img name="rules" border="0" src="/working_files/rules_regulations.gif" width="80" height="40"></a></td>
          <td class="menu" width="212"> 
            <div align="center"> 
              <form name="form1" method="post" action="">
                <select name="Quick Access" onChange="MM_jumpMenu('parent',this,1)">
                  <option value="" selected>Quick Access</option>
                  <option value="/achievements/award_programs.php">Awards Program</option>
                  <option value="/about_us/bod/index.php?class=bod">Board of Directors</option>
                  <option value="/about_us/bowling_links.php">Bowling Links</option>
                  <option value="/tournament/">Calendar of Events</option>
                  <option value="/tournament/?function=tourn_view&id=1">Canadian Open</option>
                  <option value="/instructions_coaching/coaching_certificate.php">Coaching Certification</option>
                  <option value="/about_us/contact_us.php">Contact Us</option>
                  <option value="/news/publications/">Documents</option>
                  <option value="/news/main.php">Feedback &amp; Comments</option>
                  <option value="/tournament/?function=tourn_view&id=3">High-Low Doubles</option>
                  <option value="/about_us/history.php">History</option>
                  <option value="/">Home</option>
                  <option value="/rules_regulations/officiating_development.php">Lane Certification</option>
                  <option value="http://www.trgtech.com/store/index.php?action=browse&setstore=c5pba">League Supplies</option>
                  <option value="/achievements/life_membership_policy.php">Life Members Policy</option>
                  <option value="/achievements/life_members.php">Life Members</option>
                  <option value="/mastercard_app/mastercard.php">MasterCard Information</option>
                  <option value="http://www.trgtech.com/store/index.php?action=browse&setstore=c5pba">Merchandise</option>
                  <option value="/news/main.php">News</option>
                  <option value="/rules_regulations/officiating_development.php">Officiating Development</option>
                  <option value="/online_shop/main.php">Online Shop</option>
                  <option value="/achievements/perfect_games.php">Perfect Games</option>
                  <option value="/about_us/provincial_associations.php">Provincial Associations</option>
		<option value="/news/publications/">Publications</option>
                  <option value="/achievements/record_scores.php">Record Scores</option>
                  <option value="/events_tournaments/results.php">Results</option>
                  <option value="/rules_regulations/rule_book.php">Rule Book</option>
                  <option value="/instructions_coaching/tips.php">Tips</option>
                  <option value="/tournament/?function=tourn_view&id=2">Youth Challenge</option>
                </select>
              </form>
            </div>
          </td>
        </tr>
      </table>
      <div align="right"> </div>
      <div align="right"> 
        <table width="204">
          <tr> 
            <td class="menu"> 
              <div align="right"><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><a href="/news/main.php">News</a> 
                | <a href="http://www.trgtech.com/store/index.php?action=browse&setstore=c5pba">Online Shop</a> | <a href="/">Home</a></font></div>
            </td>
          </tr>
        </table>
        <font face="Arial, Helvetica, sans-serif" size="2"></font></div>
    </td>
  </tr>
</table>
<div ID="whatsnew">
	<? $cal = new newscalendar(); ?>
</div>
