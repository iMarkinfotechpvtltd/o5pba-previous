<?php 
unset($cart['_store']);
?>

<html>

<head>
<!--   johnf@trgtech.com  -->
<link REL="shortcut icon" HREF="http://www.trgtech.com/trg.ico"></link>
<link REL="icon" HREF="http://www.trgtech.com/trg.ico"></link>

<link href="/style/trg_style.css" rel="stylesheet" type="text/css">

<SCRIPT SRC="/includes/ssm.js" language="JavaScript1.2"></SCRIPT>
<SCRIPT SRC="/includes/ssmItems.js" language="JavaScript1.2"></SCRIPT>

<SCRIPT language="JavaScript1.2">
<!--
// ssmItems[...]=[name, link, target, colspan, endrow?] - leave 'link' and
addItem("<img src='/includes/spacer.jpg' border=0>", "", "");
addItem("<img src='/includes/browse.jpg' border=0>", "/store/index.php?action=browse", "");
addItem("<img src='/includes/search.jpg' border=0>", "/store/index.php?action=search", "");
addItem("<img src='/includes/cart.jpg' border=0>", "/store/index.php?action=cart", "");
addItem("<img src='/includes/order.jpg' border=0>", "/store/index.php?action=orderhistory", "");
addItem("<img src='/includes/bkgrnd.jpg' border=0>", "_", "");
<?php
$spacer="<img src='/includes/spacer.jpg' border=0>";

echo 'addItem("'.$spacer.'Items in Cart: '.$cart['items'].'", "/store/index.php?action=cart", "", "", "#FFFFFF");';
echo 'addItem("'.$spacer.'Order Cost:   $'.number_format($cart['total_price'],2).'", "/store/index.php?action=cart", "");';
?>
addItem("<img src='/includes/spacer.jpg' border=0>", "", "", "", "", "#ffffff");
buildMenu();
-->
</SCRIPT>

<title>     TRG Computer Solutions     </title>



</head>

<body>

<!-- START THE TABLE UP HERE TO CONTAIN THE MAIN NAV BUTTONS -->

<table border=0 cellspacing=0 cellpadding=0 width=750>

<tr>

<td class="topCenter" rowspan=2>

<img src="/images/trg_logo.gif" border="0">

</td>

<td class="bottomLeft">

<img src="/images/header.gif" border="0"><br>

<img src="/images/menu.gif" usemap="#menu" border="0">
<map name="menu">
<area shape="rect" coords="14,6,51,21" href="/index.php?action=home.html">
<area shape="rect" coords="56,6,90,21" href="/store/index.php?action=subtypes&cat=Apple">
<area
shape="rect" coords="95,6,142,21" href="/current_hot_stuff/index.php?action=in_store_specials.html">
<area shape="rect" coords="147,6,189,21" href="
<?php
   $query = "select * from categories where subtype like '%Storage%'";
   $result = @mysql_query($query);
   if ($result){
$row=mysql_fetch_array($result);
        echo 'index.php?action=browseat&cat='.$row['catid'];
    }
?>
">
<area shape="rect" coords="196,6,251,21" href="/browse/index.php?action=browse.html">
<area shape="rect" coords="261,6,320,21" href="/index.php?action=virus.html">
<area shape="rect" coords="326,6,381,21" href="/fun_stuff/index.php?action=fun_stuff.html">
<area shape="rect" coords="388,6,440,21" href="index.php?action=subtypes&cat=Software">
<area shape="rect" coords="445,6,493,21" href="/browse/index.php?action=our_services.html">
<area shape="rect" coords="499,6,560,21" href="/browse/index.php?action=about_us.html">
</map>

</td>

</tr>
</table><table border=0 cellspacing=0 cellpadding=0 width=750>
<!-- END OF NAV TABLE, MOVE ON TO THE MAIN BODY TABLE -->

<tr>

<td width=40>
&nbsp
</td><td class="bottomLeft">
<!-- END OF NAV TABLE, MOVE ON TO THE MAIN BODY TABLE; IT WILL ONLY CONTAIN ONE ROW, AND TWO CELLS -->


