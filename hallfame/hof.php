<?php

function searchform(){
?>
<FORM METHOD="GET">
   
   <CENTER><TABLE BORDER=0>
      <TR>
         <TD ALIGN=center>
            <P><IMG SRC="images/stpl_bnl.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
         </TD>
         <TD ALIGN=center>
            <P><IMG SRC="images/fmsb.gif" WIDTH=41 HEIGHT=41 ALIGN=middle></P>
         </TD>
         <TD ALIGN=center>
            <P><FONT SIZE="+3"><B>Search</B></FONT></P>
         </TD>
</B></FONT><TD ALIGN=center>
            <P><IMG SRC="images/stpl_bnr.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
         </TD>
      </TR>
   </TABLE>
    <IMG SRC="images/stpl_bar.gif" WIDTH=520 HEIGHT=12 ALIGN=bottom></CENTER>
   
   <DL>
      <DT><TABLE BORDER=0>
         <TR>
            <TD WIDTH=292>
               <P ALIGN=right>First Name:</P>
            </TD>
            <TD>
               <P><INPUT TYPE=text NAME="first" VALUE="" SIZE=30></P>
            </TD>
         </TR>
         <TR>
            <TD WIDTH=292>
               <P ALIGN=right>Last Name:</P>
            </TD>
            <TD>
               <P><INPUT TYPE=text NAME="last" VALUE="" SIZE=30></P>
            </TD>
         </TR>
         <TR>
            <TD WIDTH=292>
               <P ALIGN=right>Division:</P>
            </TD>
            <TD>
               <P><SELECT NAME=Division>
                  <OPTION VALUE="%">All Divisions
                  <OPTION VALUE="builder">Builders
                  <OPTION VALUE="bobi">Builders
                  <OPTION VALUE="legend">Legends
                  <OPTION VALUE="player">Players
               </SELECT></P>
            </TD>
         </TR>
         <TR>
            <TD WIDTH=292>
               <P ALIGN=right>Year Inducted:</P>
            </TD>
            <TD>
               <P><SELECT NAME="Year">
                  <OPTION VALUE='%'>Any Year
                  <?
                  global $current,$oldest;
                  for ($i=$current; $i>$oldest-1; $i--){
                  	
                  	echo sprintf('<OPTION VALUE="%s">%s',$i,$i);
                  }
                  ?>
                  
               </SELECT></P>
            </TD>
         </TR>
      </TABLE>
      </DT>
   </DL>
   
   <CENTER><IMG SRC="images/stpl_bar.gif" WIDTH=520 HEIGHT=12 ALIGN=bottom><BR>
   
   <P><INPUT TYPE="submit" NAME="function" VALUE="Search"><INPUT TYPE="hidden" NAME="start" VALUE="0">
   <SPACER TYPE=horizontal SIZE=15> <INPUT TYPE=reset VALUE="Reset this form"></CENTER>
</FORM>

<?	
}
function showsearch($post){
	global $db,$current,$oldest;
	/*
	foreach ($post as $key=>$val){
	
	echo sprintf('%s - %s<br>',$key,$val);
	}
	*/
	if ($post['Division']!='%'){
		$division=sprintf (' AND %s_year > 0', $post['Division']);
	}
	if ($post['Year']!='%'){
		if ($post['Division']!='%'){
			$division.=sprintf (' AND %s_year = %s', $post['Division'], $post['Year']);
		}else{
			$division= sprintf(' AND ( builder_year = %s or legend_year = %s or player_year = %s or bobi_year = %s ) ', $post['Year'], $post['Year'], $post['Year'], $post['Year']);
		}
	}
	
	$db->query(sprintf('SELECT * FROM halloffame WHERE first like "%s%%" AND last like "%s%%" %s;',$post['first'],$post['last'],$division));
	$foundcount=$db->num_rows();
	if ($foundcount>20){
		$db->query(sprintf('SELECT * FROM halloffame WHERE first like "%s%%" AND last like "%s%%" %s LIMIT %s,%s;',$post['first'],$post['last'],$division,$post['start']+0,20));
	}
	
	if ($foundcount>(20+$post['start'])){
		$nextlink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$post['start'],'start='.($post['start']+20),$_SERVER['QUERY_STRING']);
	}
	if ($post['start']>0){
		$prelink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$post['start'],'start='.($post['start']-20),$_SERVER['QUERY_STRING']);
		
	}
	if (!$db->num_rows()){
		echo 'No results were found.  Please revise your search and try again.<hr>';
		searchform();
	}else{
?>



<TABLE BORDER=0>
   <TR>
      <TD ALIGN=center>
         <P><IMG SRC="images/stpl_bnl.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
      </TD>
      <TD ALIGN=center>
         <P><FONT SIZE="+3"><B>Search Results</B></FONT></P>
      </TD>
</B></FONT><TD ALIGN=center>
         <P><IMG SRC="images/stpl_bnr.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
      </TD>
   </TR>
   <tr><td colspan="3"><IMG SRC="images/stpl_bar.gif" WIDTH=520 HEIGHT=12 ALIGN=bottom></td></tr>
</TABLE>
<TABLE BORDER=0 WIDTH="520">
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
<TABLE BORDER=1 WIDTH="520">
   <TR>
      <TD ALIGN=center width=200>
         <P><B>Name</B></P>
      </TD>
      <TD ALIGN=center>
         <P><B>Division</B></P>
      </TD>
      <TD ALIGN=center width=90>
         <P><B>Year Inducted</B></P>
      </TD>
</B></CENTER></TR>

<? 
while ($db->next_record()){
	?>
   <TR>
      <TD>
         <P><A HREF="<? echo $_SERVER['PHP_SELF']; ?>?function=detail&id=<? echo $db->f('id'); ?>">
         <? 
         echo $db->f('first').' '.$db->f('last').'</A>';
         if ($db->f('deceased')==1){
         	if ($db->f('deceased_year')==0){
         		echo '(dec.)';
         	}else{
	         	echo sprintf('(dec. %s)', $db->f('deceased_year'));
         	}
         }
         ?> 
         
         </P>
      </TD>
      <TD>
         <CENTER><?
         if($db->f('builder_year')!=0){
         	echo 'Builders<br>';
         }
         if($db->f('legend_year')!=0){
         	echo 'Legends<br>';
         }
         if($db->f('player_year')!=0){
         	echo 'Players<br>';
         }
         if($db->f('bobi_year')!=0){
         	echo 'Builder of the Bowling Industry<br>';
         }
         ?>
         </CENTER>
      </TD>
      <TD>
         <CENTER>
         <?
         if($db->f('builder_year')!=0){
         	echo $db->f('builder_year').'<br>';
         }
         if($db->f('legend_year')!=0){
         	echo $db->f('legend_year').'<br>';
         }
         if($db->f('player_year')!=0){
         	echo $db->f('player_year').'<br>';
         }
         if($db->f('bobi_year')!=0){
         	echo $db->f('bobi_year').'<br>';
         }
         ?>
         </CENTER>
      </TD>
</CENTER></TR>
<?
}
echo '</TABLE>';
	}
}
function details($id, $story=''){
	global $db,$current,$oldest;
	$db->query(sprintf('SELECT * FROM halloffame WHERE id="%s%%";',$id));
	$db->next_record();
	?>	

	
	
	<HEAD>
   <TITLE>O5 Hall of Fame - Record Detail</TITLE>
</HEAD>
<BODY>
<CENTER><TABLE BORDER=0>
   <TR>
      <TD ALIGN=center>
         <P><IMG SRC="images/stpl_bnl.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
      </TD>
      <TD ALIGN=center>
         <P><FONT SIZE="+2"><B>Inductee Details</B></FONT></P>
      </TD>
</B></FONT><TD ALIGN=center>
         <P><IMG SRC="images/stpl_bnr.gif" WIDTH=89 HEIGHT=35 ALIGN=bottom></P>
      </TD>
   </TR>
</TABLE>
 <IMG SRC="images/stpl_bar.gif" WIDTH=520 HEIGHT=12 ALIGN=bottom></CENTER>

<P><TABLE BORDER=0 WIDTH="100%">
   <TR>
      <TD>
         <P><FONT SIZE="+1" COLOR="#660099"><B><TABLE BORDER=0>
            <TR>
               <TD ALIGN=center WIDTH=145>
                  <P><? if (file_exists(sprintf('./pictures/%s.jpg',$db->f('id')))){
                  	echo sprintf('<IMG SRC="./pictures/%s.jpg" ALIGN=bottom>',$db->f('id'));
                  }?></P>
               </TD>
               <TD>
                  <P><FONT SIZE="+4" COLOR="#660099"><B>
                  <?
                  echo $db->f('first').' '.$db->f('last').'</b></font><font size="+1">';
                  if ($db->f('deceased')==1){
                        if ($db->f('deceased_year')==0){
         					echo '(dec.)';
         				}else{
	         				echo sprintf('(dec. %s)', $db->f('deceased_year'));
	 					}
                  }
                  ?></P>
                  
                  <P><FONT SIZE="+1" COLOR="#660099"><B><table><tr><td><FONT SIZE="+1" COLOR="#660099"><B>Inducted into:</B></FONT></td><td>
                  <?
                  if($db->f('builder_year')!=0){
                  	echo sprintf('<FONT SIZE="+1" COLOR="#660099"><B>Builders Division in %s</B></FONT><br>',$db->f('builder_year'));
                  }
                  if($db->f('legend_year')!=0){
                  	echo sprintf('<FONT SIZE="+1" COLOR="#660099"><B>Legends Division in %s</B></FONT><br>',$db->f('legend_year'));
                  }
                  if($db->f('player_year')!=0){
                  	echo sprintf('<FONT SIZE="+1" COLOR="#660099"><B>Players Division in %s</B></FONT><br>',$db->f('player_year'));
                  }
                  if($db->f('bobi_year')!=0){
                  	echo sprintf('<FONT SIZE="+1" COLOR="#660099"><B>Builders of the Bowling Industry in %s</B></FONT><br>',$db->f('bobi_year'));
                  }
                  ?></td></tr></table>
                  </B></FONT></P>
               </TD>
            </TR>
         </TABLE>
          </B></FONT></P>
      </TD>
   </TR>
   <tr><td>
   		<Table width="100%" border="1">
   		<tr>
   			<td><? if($db->f('builder_year')!=0){
   				echo sprintf('<a href=%s?function=detail&id=%s&story=1>',$_SERVER['PHP_SELF'],$db->f('id'));
   				
   			}?>Builder Story</a></td>
   			<td><? if($db->f('legend_year')!=0){
   				echo sprintf('<a href=%s?function=detail&id=%s&story=2>',$_SERVER['PHP_SELF'],$db->f('id'));
   				
   			}?>Legend Story</a></td>
   			<td><? if($db->f('player_year')!=0){
   				echo sprintf('<a href=%s?function=detail&id=%s&story=3>',$_SERVER['PHP_SELF'],$db->f('id'));
   				
   			}?>Player Story</a></td>
   			<td><? if($db->f('bobi_year')!=0){
   				echo sprintf('<a href=%s?function=detail&id=%s&story=4>',$_SERVER['PHP_SELF'],$db->f('id'));
   				
   			}?>Builder of the Bowling Industry Story</a></td>
   		</tr>
    <TR>
      <TD colspan = 4>
         <P><?
         if ($story==''){
         	if($db->f('builder_year')!=0){
         		$story=1;
         		
         	}elseif($db->f('legend_year')!=0){
         		$story=2;
         	}elseif($db->f('player_year')!=0){
         		$story=3;
         	}elseif ($db->f('bobi_year')!=0){
         		$story=4;
         	}
         }
         $fields=array ('1'=>'builder_story', '2'=>'legend_story', '3'=>'player_story', '4'=>'bobi_story');
         echo $db->f($fields[$story]);
         ?></P>
      </TD>
   </TR>
     		</table>
   </td></tr>

</TABLE>
</P>

<CENTER><IMG SRC="images/stpl_bar.gif" WIDTH=520 HEIGHT=12 ALIGN=bottom><BR>

<P>Press 'BACK' on your Browser OR...<A HREF="<?echo $_SERVER['PHP_SELF'];?>">Start
A New Search</A></P></CENTER>

	
	
	
	
	
<?
}
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include_once('../includes/top.php');
$db=new DB;
$current=date('Y');
$oldest=1968;


switch ($_GET['function']){
	case "Search":
	showsearch($_GET);
	
	break;
	
	case "detail":
	details($_GET['id'], $_GET['story']);
	break;
	default:
	
	searchform();
	break;
}
include_once('../includes/bottom.php');
?>
