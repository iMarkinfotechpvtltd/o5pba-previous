<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include ('../includes/top.php');

?>


<TABLE BORDER CELLSPACING=1 CELLPADDING=5 WIDTH=576>
<TR><TD COLSPAN=3>
<B><P><centeR>Division</center></B></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<B><P>Builders</B></TD>
<TD>
<B><P>Legends</B></TD>
<TD>
<B><P>Players</B></TD>
<TD>
<B><P>Name, City</B></TD>
<TD>
<B><P>Year</B></TD>
<TD>&nbsp;</TD>
</TR>
<?
$db=new DB();
$db->query('select * from halloffame ORDER BY player_year, legend_year,bobi_year, builder_year;');
while ($db->next_record()){
	if ($db->f('builder_year')!=0 || $db->f('bobi_year')!=0){
                echo '<tr><TD><center>X</center></TD>';
	}else{
		echo '<TR><TD>&nbsp;</TD>';
	}
	if ($db->f('legend_year')!=0){
                echo '<TD><center>X</center></TD>';
	}else{
		echo '<TD>&nbsp;</TD>';
	}
	if ($db->f('player_year')!=0){
                echo '<TD><center>X</center></TD>';
	}else{
		echo '<TD>&nbsp;</TD>';
	}
	echo sprintf('<TD><P><A HREF="hof.php?function=detail&id=%s">%s, %s</a>, %s</TD>',$db->f('id'),$db->f('last'),$db->f('first'),$db->f('city'));
	echo '<TD><P>';
	if ($db->f('builder_year')!=0){
		echo $db->f('builder_year') . ' ' ;
	}
	if ($db->f('bobi_year')!=0){
		echo $db->f('bobi_year') . ' ' ;
	}
	if ($db->f('legend_year')!=0){
		echo $db->f('legend_year') . ' ' ;
	}
	if ($db->f('player_year')!=0){
		echo $db->f('player_year');
	}
	echo '</TD>';
	if ($db->f('deceased')!=0){
		    if ($db->f('deceased_year')==0){
         		echo '<TD>(dec.)</TD></tr>';
         	}else{
	         	echo sprintf('<TD>(dec. %s)</TD></tr>', $db->f('deceased_year'));
         	}
	}else{
		echo '<TD>&nbsp;</TD></tr>';
	}
}

?>
</TABLE>


<?php
include ('../includes/bottom.php');
?>
