<?php

function searchform(){
?>

<FORM>
   
   <TABLE BORDER=0>
      <TR>
         <TD WIDTH="500" colspan=3>
            <center><IMG SRC="images/stma_bnl.gif" WIDTH=54 HEIGHT=28 ALIGN=center>
            <IMG SRC="images/fmsb.gif" WIDTH=41 HEIGHT=41 ALIGN=top><FONT SIZE="+3"><B>Search</B></FONT>
            <IMG SRC="images/stma_bnr.gif" WIDTH=54 HEIGHT=28 ALIGN=center></center>
         </TD>
      </TR>
	<tr>
	<td colspan=3>
	    <CENTER><IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom></CENTER>
	</td></tr>

      <TR>
         <TD WIDTH="200">

         </TD>
         <TD WIDTH="200">

         </TD>
         <TD WIDTH="100">
            Sort By<br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>First Name:<br>
            

         </TD>
         <TD WIDTH="33%">
         <input type=hidden name=start value=0>
            <INPUT TYPE=text NAME="first" VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            <INPUT TYPE="radio" NAME="-SortField" VALUE="first"><br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>Last Name:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME="last" VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            <INPUT TYPE="radio" NAME="-SortField" VALUE="last"><br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>Membership Number:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME="memberid" VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>Games:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME=games VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            <INPUT TYPE="radio" NAME="-SortField" VALUE="games" checked><br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>Average:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME=average VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            <INPUT TYPE="radio" NAME="-SortField" VALUE="average" checked><br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>League:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME=league VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
      <TR>
         <TD VALIGN=top WIDTH="33%">
            <P ALIGN=right>Association:<br>
         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="association">
	<OPTION VALUE="" SELECTED>All Associations
<?
global $db;
$db->query('select association from average group by association');
while ($db->next_record()){
	echo sprintf('<option value="%s">%s',$db->f('association'),$db->f('association'));
}
?>
</SELECT>
</SELECT><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
<?php 
/*
      <TR>
         <TD VALIGN=top WIDTH="33%">
            <P ALIGN=right>Season:<br>
         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="year">
<?
global $db;
$db->query('select year from average group by year');
while ($db->next_record()){
   $year = $db->f('year');
   echo '<option ';
   if (date('Y') >= ($year - 1)) echo 'selected ';
	echo sprintf('value="%s">%s/%s</option>',$year,$year - 1,$year);
}
?>

</SELECT><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
*/?>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right><B>Sort Order:</B><br>
         </TD>
         <TD colspan=2>
            <SELECT NAME="-sort">
               <OPTION VALUE="DESC">Descending
               <OPTION VALUE="">Ascending
            </SELECT>
         </TD>
      </TR>
      <tr>
	<td colspan=3>
	    <CENTER><IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom></CENTER>
	</td></tr>
      <TR>
         <TD WIDTH="200">
            <P ALIGN=right>Return:<br>
         </TD>
		<TD WIDTH="200" colspan=2>

            <SELECT NAME="return">
               <OPTION VALUE=10>10
               <OPTION VALUE=20 SELECTED>20
               <OPTION VALUE=30>30
               <OPTION VALUE=50>50
               <OPTION VALUE=75>75
               <OPTION VALUE=100>100
            </SELECT><B> records at a time.</B><br>
         </TD>
</TR>

   
   	<td colspan=3>
   <CENTER><IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom>   <br>

   <INPUT TYPE="submit" NAME="function" VALUE="Search">
   <SPACER TYPE=horizontal SIZE=15> 
   <SPACER TYPE=horizontal SIZE=15> <INPUT TYPE=reset VALUE="Reset this form"><br></CENTER>
	</td></tr>
   

   </TABLE>
   
   
   

   
   
</FORM><br>
<?	
}
function showsearch($post){
	global $db;

	$db->query(sprintf('SELECT count(id) as foundcount FROM average WHERE first like "%s%%" AND last like "%s%%" AND memberid like "%s%%" AND league like "%s%%" AND association like "%s%%" ORDER BY %s %s;',strtoupper($post['first']),strtoupper($post['last']),strtoupper($post['memberid']),strtoupper($post['league']),strtoupper($post['association']),$post['-SortField'],$post['-sort']));
	$db->next_record();
	$foundcount=$db->f('foundcount');

	$db->query(sprintf('SELECT * FROM average WHERE first like "%s%%" AND last like "%s%%" AND memberid like "%s%%" AND league like "%s%%" AND association like "%s%%" ORDER BY %s %s LIMIT %s,%s;',strtoupper($post['first']),strtoupper($post['last']),strtoupper($post['memberid']),strtoupper($post['league']),strtoupper($post['association']),$post['-SortField'],$post['-sort'],$post['start']+0,$post['return']));

	if (!$db->num_rows()){
		echo 'No results were found.  Please revise your search and try again.<hr>';
		searchform();
	}else{
		if ($foundcount>($post['return']+$post['start'])){
			$nextlink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$post['start'],'start='.($post['start']+$post['return']),$_SERVER['QUERY_STRING']);
		}
		if ($post['start']>0){
			$prelink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$post['start'],'start='.($post['start']-$post['return']),$_SERVER['QUERY_STRING']);

		}
?>
<TABLE BORDER=0 WIDTH="500">

   <TR>
      <TD ALIGN=center colspan=3>
         <center><IMG SRC="images/stma_bnl.gif" WIDTH=54 HEIGHT=28 ALIGN=bottom>
         <FONT SIZE="+3"><B>Results</B></FONT>
         <IMG SRC="images/stma_bnr.gif" WIDTH=54 HEIGHT=28 ALIGN=bottom></center>
      </TD>
   </TR>

   <tr><td colspan=3>
   <center> <IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom></center>
   </td></tr>
   <TR>
      <TD WIDTH="33%">
         <P></P>
      </TD>
		<TD COLSPAN=2>
         <P>Displaying records <? echo $post['start']+1; ?> through
         <? echo $post['start']+$db->num_rows(); ?> of <? echo $foundcount; ?> records found.</P>
      </TD>
   </TR>
   <tr><td colspan=3>
   <center> <IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom></center>
   </td></tr>

   <TR>
      <TD WIDTH="33%">
         <P><? if ($prelink){echo "<a href='$prelink'>"; } ?>
            Previous Results</a>
         </P>
      </TD>
      <TD ALIGN=center WIDTH="34%">
         <P><centeR><A HREF="<? echo $_SERVER['PHP_SELF']; ?>">
         Start New Search</FONT></A></center></P>
      </TD>
      <TD WIDTH="33%">
         <P ALIGN=right><? if ($nextlink){echo "<a href='$nextlink'>"; } ?>
            Next Results
         </P>
      </TD>
   </TR>
</TABLE>
<TABLE BORDER=0 WIDTH="500">
   <TR>
      <TD>
         <font size="2"><CENTER><B>Ontario<BR>
         Rank</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>Zone<BR>
         Rank</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Last &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>First Name</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>Membership<BR>
          Number</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>Last<BR>Season</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>Zone /<br>Association</B></CENTER></font>
      </TD>
     <TD>
         <font size="2"><CENTER><B>Running Games</B></CENTER></font>
      </TD>
      <TD>
         <font size="2"><CENTER><B>Rolling Average</B></CENTER></font>
      </TD>
</B></CENTER></TR>
<? 
while ($db->next_record()){
	?>
<TR>
      <TD VALIGN=top>
         <CENTER><A HREF="<? echo $_SERVER['PHP_SELF']; ?>?function=detail&id=<? echo $db->f('id'); ?>"><? echo $db->f('oplacing'); ?></A></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('aplacing'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('last'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('first'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('memberid'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? 
            $year = $db->f('year');
            $year = sprintf("%02d",$year - floor($year/100)*100);
            $yearPrev = sprintf("%02d",$year - 1 - floor(($year - 1)/100)*100);
            echo $yearPrev.'/'.$year;
         ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('association'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('games'); ?></CENTER>
      </TD>
      <TD>
         <CENTER><? echo $db->f('average'); ?></CENTER>
      </TD>

</CENTER></TR>
<?
}
	?></TABLE>
<BR>
<TABLE BORDER=0 WIDTH="500">
   <tr><td colspan=3>
   <center> <IMG SRC="images/stma_bar.gif" WIDTH=514 HEIGHT=19 ALIGN=bottom></center>
   </td></tr>

   <TR>
      <TD WIDTH="33%">
         <P><? if ($prelink){echo "<a href='$prelink'>"; } ?>
            Previous Results</a>
         </P>
      </TD>
      <TD ALIGN=center WIDTH="34%">
         <P><centeR><A HREF="<? echo $_SERVER['PHP_SELF']; ?>">
         Start New Search</FONT></A></center></P>
      </TD>
      <TD WIDTH="33%">
         <P ALIGN=right><? if ($nextlink){echo "<a href='$nextlink'>"; } ?>
            Next Results
         </P>
      </TD>
   </TR>
</TABLE>
</CENTER>
<?	
	}
}
function details($id){
	global $db;
	$db->query(sprintf('SELECT * FROM average WHERE id="%s";',$id));
	$db->next_record();

	$current=$db->f('year');
	$oldest=2005;

	?>	

<CENTER><TABLE BORDER=0>
          <TR>
             <TD ALIGN=CENTER>

<P><IMG SRC="images/stma_bnl.gif"></TD>
             <TD ALIGN=CENTER>

<P><H4>Bowler's Details</h4> </TD>
             <TD ALIGN=CENTER>

<P><IMG SRC="images/stma_bnr.gif"></TD>
          </TR>
</TABLE> <IMG SRC="images/stma_bar.gif"></CENTER>


<P><TABLE BORDER=0 WIDTH="100%">
         <TR><TD WIDTH="33%"></TD><TD>

<P></P></TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>Name:</P></TD><TD> <? echo $db->f('first').' '.$db->f('last'); ?> </TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>Membership Number:</P></TD><TD> <? echo $db->f('sex'); ?> </TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>League:</P></TD><TD> <? echo $db->f('category'); ?> </TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>Membership Number:</P></TD><TD> <? echo $db->f('memberid'); ?> </TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>League:</P></TD><TD> <? echo $db->f('league'); ?> </TD></TR><TR><TD WIDTH="33%">

<P ALIGN=right>Association:</P></TD><TD> <? echo $db->f('association'); ?></TD></tr>

<?
for ($i=$current; $i>$oldest-1; $i--){

	$y[$i]=array('games'=>$db->f('games'),'average'=>$db->f('average'), 'GP'=>$db->f('GP_'.substr(($i),-2,2) ), 'AV'=>$db->f('AV_'.substr(($i),-2,2) ));
}

/*** modified on 2009/10, so don't need it 
$y[$current]=array('games'=>$db->f('games'),'average'=>$db->f('average'),'oplacing'=>$db->f('oplacing'), 'aplacing'=>$db->f('aplacing'));

$db->query(sprintf('select * from average_history where memberid="%s" and league="%s";', $db->f('memberid'), $db->f('league')));
while($db->next_record()){
	$y[$db->f('year')]=array('games'=>$db->f('games'),'average'=>$db->f('average'),'oplacing'=>$db->f('oplacing'), 'aplacing'=>$db->f('aplacing'));
}
*/

?>

<tr><td colspan = 2><center>
<table cellspacing="0" cellpadding="0"><tr>

<td><P ALIGN=right class=dark>Season:</td>
<td width=75><P ALIGN=right class=dark>Running</td>

<?
for ($i=$current; $i>$oldest-1; $i--){

	echo '<td width=75 class=dark><P ALIGN=right>'.substr(($i-1),0,4).'/'.substr(($i),-2,2).'</td>';

}
?>

</tr><tr>

<td><P ALIGN=right class=dark>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
<td width=75><P ALIGN=right class=dark>Average </td>

<?
for ($i=$current; $i>$oldest-1; $i--){

	echo '<td width=75 class=dark><P ALIGN=right>'.'&nbsp;'.'</td>';

}
?>

</tr><tr>

<td><P ALIGN=right class=dark>Games: </td>
<td width=75><P ALIGN=right class=dark><? echo $db->f('games') ; ?>&nbsp;&nbsp;&nbsp;&nbsp; </td>

<?
for ($i=$current; $i>$oldest-1; $i--){
	echo '<td width=75><P ALIGN=right class=light>'.$y[$i]['GP'].'&nbsp;&nbsp;&nbsp;&nbsp;'.'</td>';
	
}

?>

</tr><tr>

<td><P ALIGN=right class=dark>Average: </td>
<td width=75><P ALIGN=right class=dark><? echo $db->f('average') ; ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>

<?

for ($i=$current; $i>$oldest-1; $i--){
	echo '<td width=75><P ALIGN=right class=light>'.$y[$i]['AV'].'&nbsp;&nbsp;&nbsp;&nbsp;'.'</td>';
}

?>

</tr></table>

</tr><tr><td>&nbsp;</td></tr></table>

<CENTER><IMG SRC="images/stma_bar.gif"><BR></CENTER>

<P><A HREF="javascript:history.go(-1)">Click here to go <B>Back</B></a>.</P>

	
<?
}
require_once('../classes/prepend.php');

include_once('../includes/top.php');
$db=new DB;
$db->query('use o5pba');
//echo '<b>Historical Averages currently offline for reformatting.</b><br>';

switch ($function){
	case "Search":
	showsearch($_GET);

	break;

	case "detail":
	details($_GET['id']);
	break;
	default:

	searchform();
	break;
}

include_once('../includes/bottom.php');
?>
