<style>
td p {margin:0;}
.darkred {background-color:#FF6666;}
.lightred {background-color:#FFBFBF;}
</style>
<?php

function searchform(){
?>
<script type="text/javascript">
	
	function isSafari() {
		return /Safari/i.test(navigator.userAgent);
	}
	
	var leagueOptions = new Array();
	var centreOptions = new Array();
	
	function onZoneDropDownChange(zoneEl, doReset) {
		var selectedValue = zoneEl.options[zoneEl.selectedIndex].value;
		var coptions = centreOptions[selectedValue];
		var centreEl = document.forms[0].centre;
		
		centreEl.innerHTML = '<option value="">All Centres</option>';
		
		if(coptions != undefined)
			centreEl.innerHTML += coptions.join('');
		else if (selectedValue == '') {
			for (var i in centreOptions) {
				centreEl.innerHTML += centreOptions[i].join('');
			}
		}
		
		if (doReset !== false)
			centreEl.selectedIndex = 0;
			
		onCentreDropDownChange(centreEl, doReset);
	}
	
	function onCentreDropDownChange(centreEl, doReset) {
		var selectedValue = centreEl.options[centreEl.selectedIndex].value;
		var loptions = leagueOptions[selectedValue];
		var leagueEl = document.forms[0].league;
		
		leagueEl.innerHTML = '<option value="">All Leagues</option>'
		
		if (loptions != undefined)
			leagueEl.innerHTML += loptions.join('');
		else if (selectedValue == '' && leagueEl.form.association.selectedIndex == 0) {
			for (var i in leagueOptions) {
				leagueEl.innerHTML += leagueOptions[i].join('');
			}
		}
		
		if (doReset !== false)
			leagueEl.selectedIndex = 0;
	}
	
	function setupLeagueAndCentreDropDwns() {
		var loptions = document.forms[0].league.options;
		var coptions = document.forms[0].centre.options;
		var cid;
		var zid;
		
		for (var i=0; i<loptions.length; i++) {
			cid = loptions[i].getAttribute('data-Centre');
			if (leagueOptions[cid] == undefined) {
				leagueOptions[cid] = new Array();
			}
			leagueOptions[cid].push('<option value="' + loptions[i].value + '">' + loptions[i].innerHTML + '</option>');
		}
		for (var i=0; i<coptions.length; i++) {
			zid = coptions[i].getAttribute('data-Zone');
			if (centreOptions[zid] == undefined) {
				centreOptions[zid] = new Array();
			}
			centreOptions[zid].push('<option value="' + coptions[i].value + '">' + coptions[i].innerHTML + '</option>');
		}
		
		onZoneDropDownChange(document.forms[0].association, false);
	}
	
	if(isSafari()){ //Test for Safari
		var _timer=setInterval(function(){
		if(/loaded|complete/.test(document.readyState)){
		  clearInterval(_timer);
		  setupLeagueAndCentreDropDwns() // call target function
		}}, 10);
	}
	else {
		var alreadyrunflag=0 //flag to indicate whether target function has already been run
		
		if (document.addEventListener)
		  document.addEventListener("DOMContentLoaded", function(){alreadyrunflag=1; setupLeagueAndCentreDropDwns() }, false)
		else if (document.all && !window.opera){
		  document.write('<script type="text/javascript" id="contentloadtag" defer="defer" src="javascript:void(0)"><\/script>')
		  var contentloadtag=document.getElementById("contentloadtag")
		  contentloadtag.onreadystatechange=function(){
			if (this.readyState=="complete"){
			  alreadyrunflag=1;
			  setupLeagueAndCentreDropDwns();
			}
		  }
		}
		
		window.onload=function(){
		  setTimeout("if (!alreadyrunflag){setupLeagueAndCentreDropDwns() }", 0)
		}
	}
</script>
<FORM autocomplete="off">
   
   <TABLE class="avg" BORDER=0>
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
            <INPUT TYPE="radio" NAME="-SortField" VALUE="first_name"><br>
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
            <INPUT TYPE="radio" NAME="-SortField" VALUE="last_name"><br>
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>C5 Registration#:<br>
            

         </TD>
         <TD WIDTH="33%">
            <INPUT TYPE=text NAME="memberid" VALUE="" SIZE=30><br>
         </TD>
         <TD WIDTH="20%">
            <INPUT TYPE="radio" NAME="-SortField" VALUE="member_number" checked><br>
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
            <INPUT TYPE="radio" NAME="-SortField" VALUE="running_games" checked><br>
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
            <INPUT TYPE="radio" NAME="-SortField" VALUE="rolling_average" checked><br>
         </TD>
      </TR>
      <TR>
         <TD VALIGN=top WIDTH="33%">
            <P ALIGN=right>Association:<br>
         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="association" onchange="onZoneDropDownChange(this)">
	<OPTION VALUE="" SELECTED>All Associations</OPTION>
<?
global $db;
$db->query('select id, name from oa_zones ORDER BY name ASC');
while ($db->next_record()){
	echo sprintf('<option value="%s">%s</option>',$db->f('id'),$db->f('name'));
}
?>
</SELECT>
	<br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
	  <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>Centre:<br>
            

         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="centre" onchange="onCentreDropDownChange(this)">
	<OPTION VALUE="" SELECTED>All Centres</OPTION>
<?
global $db;
$db->query('select id, name, zone_id from oa_centres ORDER BY name ASC');
while ($db->next_record()){
	echo sprintf('<option value="%s" data-Zone="%s">%s</option>',$db->f('id'),$db->f('zone_id'),$db->f('name'));
}
?>
</SELECT><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
      <TR>
         <TD WIDTH="33%">
            <P ALIGN=right>League:<br>
            

         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="league">
	<OPTION VALUE="" SELECTED>All Leagues</OPTION>
<?
global $db;
$db->query('select id, name, centre_id from oa_leagues ORDER BY name ASC');
while ($db->next_record()){
	echo sprintf('<option value="%s" data-Centre="%s">%s</option>',$db->f('id'),$db->f('centre_id'),$db->f('name'));
}
?>
</SELECT><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
<TR>
  <TD WIDTH="33%">
            <P ALIGN=right>Season:<br>
            

         </TD>
         <TD WIDTH="33%">
            <SELECT NAME="season">
	<OPTION VALUE="" SELECTED>All Seasons</OPTION>
<?
global $db;
$db->query('select id, name from oa_seasons ORDER BY name DESC');
while ($db->next_record()){
	echo sprintf('<option value="%s">%s</option>',$db->f('id'),$db->f('name'));
}
?>
</SELECT><br>
         </TD>
         <TD WIDTH="20%">
            
         </TD>
      </TR>
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
	$db4=new DB;
	$db4->query('use o5pba');	
	$db4->query("select value as sea from oa_settings");
    $db4->next_record();
	$sea = $db4->f('sea');
	
	$sql = "SELECT m.*, (sum(mb.num_pinfalls)/sum(mb.num_games))  as rolling_average FROM oa_members as m
	JOIN oa_member_bowlings as mb on m.id=mb.member_id 
	JOIN oa_zones z  ON  z.id = mb.zone_id 
	JOIN oa_seasons s  ON s.id = mb.season_id ";
	// echo $sql;
	
	// $sql = "SELECT m.*, floor(m.running_pinfalls/m.running_games) AS rolling_average	FROM oa_members m";
	$where = " WHERE season_id in ($sea) ";
	// if ($post['first']=='' && $post['last']=="" && $post['memberid']=="" && $post['association']=="" && $post['league']=="" && $post['season']=="" && $post['league']=="" && $post['centre']=="") {$where.= "AND m.running_games>20 ";}
	$order = sprintf("ORDER BY %s %s, m.id", $post['-SortField'], $post['-sort']);
	$groupby = "group by m.id";
	$limit = sprintf("LIMIT %d,%d", $post['start']+0, $post['return']);
	
	if ($post['first']) {
		$where .= sprintf(" AND m.first_name LIKE '%%%s%%'", mysql_real_escape_string($post['first']));
	}
	if ($post['last']) {
		$where .= sprintf(" AND m.last_name LIKE '%%%s%%'", mysql_real_escape_string($post['last']));
	}
	if ($post['memberid']) {
		$where .= sprintf(" AND member_number LIKE '%%%s%%'", mysql_real_escape_string($post['memberid']));
	}
	if ($post['association']) {
		$where .= sprintf(" AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.zone_id = %d)", $post['association']);
	}
	if ($post['league']) {
		$where .= sprintf(" AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.league_id = %d)", $post['league']);
	}
	if ($post['season']) {
	 echo" ";
		$where .= sprintf(" AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.season_id = %d)", $post['season']);
	}
	if ($post['centre']) {
		$where .= sprintf(" AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.centre_id = %d)", $post['centre']);
	}

	$sql .= ' ' . $where;
	$sql .= ' ' . $groupby;
	$sql .= ' ' . $order;
	// echo $sql;  exit;
	$db->query(sprintf("SELECT COUNT(*) AS foundcount FROM (%s) a", $sql));
	$db->next_record();
	$foundcount=$db->f('foundcount');

	$db->query($sql . ' ' . $limit);
	// echo $sql . ' ' . $limit;
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
      <TD  nowrap='nowrap'>
         <font size="2"><CENTER><B>Ontario<BR>
         Rank</B></CENTER></font>
      </TD>
      <TD  nowrap='nowrap'>
         <font size="2"><CENTER><B> Last Name</B></CENTER></font>
      </TD>
      <TD  nowrap='nowrap'>
         <font size="2"><CENTER><B>First Name</B></CENTER></font>
      </TD>
      <TD  nowrap='nowrap'>
         <font size="2"><CENTER><B>C5<BR>
          Reg#</B></CENTER></font>
      </TD>
      <TD width="45"  nowrap='nowrap'>
         <font size="2"><CENTER><B>Last<BR>Season</B></CENTER></font>
      </TD>
      <TD   nowrap='nowrap'>
         <font size="2"><CENTER><B>Zones /<br>Associations</B></CENTER></font>
      </TD>
     <TD  nowrap='nowrap'>
         <font size="2"><CENTER><B>Running <br/>Games</B></CENTER></font>
      </TD>
      <TD nowrap='nowrap'>
         <font size="2"><CENTER><B>Rolling <br/>Average</B></CENTER></font>
      </TD>
</B></CENTER></TR>
<?
$db2=new DB;
$db2->query('use o5pba');
$db4=new DB;
$db4->query('use o5pba');			
while ($db->next_record()){
	  // echo $db->f('rolling_average');
      $db4->query("select value as sea from oa_settings");
      $db4->next_record();
		$sea = $db4->f('sea');
		$db4->query(sprintf("select sum(t.num) as games, (sum(t.num2)/sum(t.num)) as average, sum(num2) as pinfall from (SELECT mb.num_games as num,mb.num_pinfalls as num2
			         FROM  oa_member_bowlings mb
			         JOIN oa_zones z  ON  z.id = mb.zone_id 
					 JOIN oa_seasons s  ON s.id = mb.season_id 	 
					 WHERE mb.member_id = %d and s.id in ($sea)) as t", $db->f('id')));
			 
			$db4->next_record();
			// echo $db4->f('games');exit;
			if($db4->f('games')>20) {$rank = $db->f('ontario_rank');}
			else {$rank = "N/R";}
			?>
<TR>
      <TD VALIGN=top >
         <CENTER><A HREF="<? echo $_SERVER['PHP_SELF']; ?>?function=detail&id=<? echo $db->f('id'); ?>"><? echo $rank; ?></A></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><? echo $db->f('last_name'); ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><? echo $db->f('first_name'); ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><? echo $db->f('member_number'); ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER>
		 <?php
			$db2->query(sprintf("SELECT s.name FROM oa_seasons s JOIN oa_member_bowlings mb ON mb.season_id = s.id WHERE mb.member_id = %d ORDER BY s.start_date DESC LIMIT 1", $db->f('id')));
			if ($db2->next_record()) {
				echo $db2->f('name');
			}
			else echo 'N/A';
         ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><?php
			$db2->query(sprintf("SELECT DISTINCT z.name FROM oa_zones z JOIN oa_member_bowlings mb ON mb.zone_id = z.id WHERE mb.member_id = %d ORDER BY z.name ASC", $db->f('id')));
			while ($db2->next_record()) {
				echo $db2->f('name');
				echo '<br/> ';
			}
		 ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><? echo $db4->f('games'); ?></CENTER>
      </TD>
      <TD  nowrap='nowrap'>
         <CENTER><? echo floor($db4->f('average'));  ?></CENTER>
      </TD>
	

</TR>
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
	$db->query(sprintf('SELECT *, FLOOR(running_pinfalls/running_games) AS rolling_average FROM oa_members WHERE id=%d;',$id));
	$db->next_record();
	
	$sql = sprintf("SELECT DISTINCT mb.*, z.name AS zone_name, z.code AS zone_code,
							c.name AS centre_name, l.name AS league_name,
							l.code AS league_code, s.name AS season_name,
							CONCAT_WS('', z.code, '%s', CONCAT_WS('-', c.code, l.number)) AS membership_number,
							FLOOR(mb.num_pinfalls/mb.num_games) AS average,
							slr.rank AS seasonal_league_rank,
							mb.id as id
					FROM oa_member_bowlings mb
						LEFT JOIN oa_zones z ON z.id = mb.zone_id
						LEFT JOIN oa_leagues l ON l.id = mb.league_id
						LEFT JOIN oa_centres c ON c.id = mb.centre_id
						LEFT JOIN oa_seasons s ON s.id = mb.season_id
						LEFT JOIN oa_seasonal_league_rankings slr ON l.id = slr.league_id AND slr.member_id = mb.member_id AND slr.season_id = s.id
					WHERE mb.member_id = %d
					ORDER BY s.start_date DESC, seasonal_league_rank ASC", $db->f('member_number'), $id);
	$db4=new DB;
$db4->query('use o5pba');
	$db4->query("select  value as sea from oa_settings");
      $db4->next_record();
		$sea = $db4->f('sea');
		$db4->query(sprintf("select sum(t.num) as games, (sum(t.num2)/sum(t.num)) as average, sum(num2) as pinfall from (SELECT mb.num_games as num,mb.num_pinfalls as num2
			         FROM  oa_member_bowlings mb
			         JOIN oa_zones z  ON  z.id = mb.zone_id 
					 JOIN oa_seasons s  ON s.id = mb.season_id 	 
					 WHERE mb.member_id = %d and s.id in ($sea)) as t", $db->f('id')));
			 
			$db4->next_record();
	$db2=new DB;
	$db2->query('use o5pba');
	$db2->query($sql);
	 
	?>	
<style>
	h2, h3 {
		margin: 5px 0;
		font-family: helvetica;
		font-weight: normal;
	}
	h2 {
		font-size: 14pt;
	}
	h3 {
		font-size: 12pt;
	}
</style>

<CENTER><TABLE BORDER=0 cellspacing="0" cellpadding="0">
	<tr>
		<td width="25%"></td>
		<td width="20%"></td>
		<td width="20%"></td>
		<td></td>
		</tr>
          <TR>
             <TD ALIGN=LEFT colspan="4">
				<h3>Bowler Data</h3> </TD>
          </TR>
<TR><TD colspan="4">

<h1><? echo $db->f('first_name').' '.$db->f('last_name'); ?> </h1></TD></TR>

<TR>
<TR>
	
	<TD  bgcolor= '#FF6666' ALIGN=left><h3>Rolling Average:</h3></TD><TD bgcolor='#FF6666'><h3> <? echo floor($db4->f('average')); ?> </h3></TD>
	<TD>
		<h3>C5 Registration#:</h3></TD>
	<TD><h3> <? echo $db->f('member_number'); ?> </h3></TD>
</tr>

<tr><td colspan='4'><br/></td></tr>
<TR>
<td>
<table>

	<tr><TD>
		<h3>Ontario Rank:</h3></TD>
	</tr>
	<tr><TD ALIGN=left><h3>Games:</h3></TD></tr>
		 <tr><TD ALIGN=left><h3>Pinfall:</h3></TD></tr>
	

</table>
</td>
<td>
 <table>
<tr><TD><h3> <? echo $db->f('ontario_rank'); ?> </h3></TD></tr>
<tr><TD><h3> 

	  <?php
		
		 echo $db4->f('games'); ?> </h3></TD></tr>
<tr><TD><h3> <? echo $db4->f('pinfall'); ?> </h3></TD></tr>
</table>
</td>
</td>


<td rowspan="6" colspan="2">
	     
	    <table>
		 <tr><td valign="top" > <h3 >Member</h3> </td> 
        <td>
		 <table>			 
		 <tr><th>  <h3 style='font-weight:bold'> Season:</h3></th><th><h3 style='font-weight:bold'>Association:</h3></th></tr>
		  <?php
		$arr[50];
		$i = 0;
		$db3=new DB;
		$db3->query('use o5pba');
				$db3->query(sprintf("SELECT distinct z.name as zname,s.name as sname 
			         FROM  oa_member_bowlings mb
			         JOIN oa_zones z  ON  z.id = mb.zone_id 
					 JOIN oa_seasons s  ON s.id = mb.season_id 	 
					 WHERE mb.member_id = %d and z.name != 'Sanctioned Tournaments' and s.id in ($sea)  ORDER BY s.name DESC ", $db->f('id')));
			 
			while ($db3->next_record()) {
			 echo "<tr><td>" .  $db3->f('sname'). " &nbsp &nbsp</td> <td>" . $db3->f('zname')  . "</td></tr>";
			 
			}
				$db3->query('use o5pba');
				$db3->query(sprintf("SELECT mb.id as id,z.name as zname, s.name as sname 
			         FROM  oa_member_bowlings mb
			         JOIN oa_zones z  ON  z.id = mb.zone_id 
					 JOIN oa_seasons s  ON s.id = mb.season_id 	 
					 WHERE mb.member_id = %d and s.id in ($sea) ", $db->f('id')));
			 while ($db3->next_record()) {
			$arr[$i++] = $db3->f('id');
			 
			}
			$tot = $i;
			
		 ?>
		 </table>
		 </td></tr>
		 </table
		 
	   
	     
	</td>
	</TR>





<tr><td colspan="4"><br/><br/><br/><br/></td></tr>

<tr><td colspan = 4><center>
<table cellspacing="0" cellpadding="5"><tr>
	<th><center>Zone<br>Rank</center></th>
	<th><center><br>Association</center></th>
	<th><center><br>League</center></th>
	<th><center><br>Centre</center></th>
	<th><center>O5<br>Member#</center></th>
	<th><center><br>Games</center></th>
	<th><center><br>Pinfalls</center></th>
	<th><center><br>Average</center></th>
	<th><center><br>Season</center></th>
</tr>

<?php
$i = 0;
while ($db2->next_record()) {
  $used= 0;
	for($j =0 ; $j < $tot; $j++)
	{
	 if($arr[$j] == $db2->f('id')) { $used = 1;}
	
	}
	if($used == 0)
	{
	?>
	  
	   <tr class="<?php echo $i%2 != 0 ? 'dark' : '' ?>">
		<td><center><?php echo $db2->f('seasonal_league_rank') ?></center></td>
		<td><center><?php echo $db2->f('zone_name') ?></center></td>
		<td><center><?php echo $db2->f('league_name') ?></center></td>
		<td><center><?php echo $db2->f('centre_name') ?></center></td>
		<td><center><?php echo $db2->f('membership_number') ?></center></td>
		<td><center><?php echo $db2->f('num_games') ?></center></td>
		<td><center><?php echo $db2->f('num_pinfalls') ?></center></td>
		<td><center><?php echo $db2->f('average') ?></center></td>
		<td><center><?php echo $db2->f('season_name') ?></center></td>
		</tr>
	<?php
	}
	else
	{?>
	   <tr class="<?php echo $i%2 != 0 ? 'darkred' : 'lightred' ?>">
		<td><center><?php echo $db2->f('seasonal_league_rank') ?></center></td>
		<td><center><?php echo $db2->f('zone_name') ?></center></td>
		<td><center><?php echo $db2->f('league_name') ?></center></td>
		<td><center><?php echo $db2->f('centre_name') ?></center></td>
		<td><center><?php echo $db2->f('membership_number') ?></center></td>
		<td><center><?php echo $db2->f('num_games') ?></center></td>
		<td><center><?php echo $db2->f('num_pinfalls') ?></center></td>
		<td><center><?php echo $db2->f('average') ?></center></td>
		<td><center><?php echo $db2->f('season_name') ?></center></td>
		</tr>
	<?php	
	}
	$i++;
}

?>

</table></center></td></tr></TABLE></CENTER>

<P><A HREF="javascript:history.go(-1)">Click here to go <B>Back</B></a>.</P>

	
<?
}

require_once('../classes/prepend.php');

include_once('../includes/top.php');
$db=new DB;
$db->query('use o5pba');
//echo '<b>This page is currently under construction!</b><br>';

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
