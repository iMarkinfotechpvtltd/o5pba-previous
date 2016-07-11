<?php
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include ('../includes/top.php');
?>


<TABLE BORDER CELLSPACING=1 CELLPADDING=5 WIDTH=586>
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
$current=date('Y');
$oldest=1968;
for ($i=$current; $i>$oldest-1; $i--){
	$db->query(sprintf('select * from halloffame WHERE player_year = %s OR legend_year = %s OR bobi_year = %s OR builder_year = %s ORDER BY last, first;',$i,$i,$i,$i));
	while ($db->next_record()){
		if ($db->f('builder_year')!=0 || $db->f('bobi_year')!=0){
			echo '<TR><TD><center>X</center></TD>';
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
}
/*
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tommy Ryan, Toronto</TD>
<TD>
<P>1968</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bob Woods, Toronto</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Charles Demelis, Willowdale</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Dick Brett, Hamilton</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Edward Hawkes, Toronto</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>John "Jake" Smith, Toronto</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Mabel McDowell, Weston</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ollie Miller, Toronto</TD>
<TD>
<P>1970</TD>
<TD>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Tom Mallon, Toronto</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Vera Ward, Hamilton</TD>
<TD>
<P>1970</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bert Garside, Pickering</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bob Totzke, Kitchener</TD>
<TD>
<P>1986</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Flo Cutting, Toronto</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Jack Fine, Toronto</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Jim Beeforth, Toronto</TD>
<TD>
<P>1986</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>John Martin, Toronto</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Billy Hoult, Agincourt</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>C.B. "Red" McQuaker, Mississauga</TD>
<TD>
<P>1986</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Doris Luke, Toronto</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Millie Evans, Mississauga</TD>
<TD>
<P>1986</TD>
<TD>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Stan Battersby, Stoney Creek</TD>
<TD>
<P>1986</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Theda Procher, Toronto</TD>
<TD>
<P>1986</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bill Graham, Toronto</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Carl Malcolmson, Unionville</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Evelyn Wood, Hamilton</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Fred Halle, Islington</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Jack Hales, London</TD>
<TD>
<P>1987</TD>
<TD>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Leon Hudecki, Hamilton</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Orv Bauman, Waterloo</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bill Bromfield, Toronto</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Lloyd Markle, Guelph</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Marion Dibble, Toronto</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Myrt Rowell, Hamilton</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Bea Ross Kotelko, Oshawa</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Bert Garside, Pickering</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Dot Peppin Smy, Toronto</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Edna Rimmer, Hamilton</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>George Smith, Scarborough</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Helen Richards, Scarborough</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Jackie Wilson, London</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>John Scholes, Welland</TD>
<TD>
<P>1987</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Lloyd Ormerod, Hamilton</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Norah Oakley, Toronto</TD>
<TD>
<P>1987</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Charlie Hill, Mississauga</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tom Craig, Scarborough</TD>
<TD>
<P>1988</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Brock Bailey, Toronto</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>George Kerr, Toronto</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>George Weale, Toronto</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Rolly Glandfield, Willowdale</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Charlene MacCormack, Streetsville</TD>
<TD>
<P>1988</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Emile Cote, Ottawa</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Fred Pechaluk, Weston</TD>
<TD>
<P>1988</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Joe Chiki, Fonthill</TD>
<TD>
<P>1988</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Lea McBeigh, Toronto</TD>
<TD>
<P>1988</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Shirley Bedell, Hamilton</TD>
<TD>
<P>1988</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Don Guindon, Windsor</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Don Walker, Barrie</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Ernie Roggie, Stoney Creek</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Frank Smith, Toronto</TD>
<TD>
<P>1989</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Percy Cutting, Toronto</TD>
<TD>
<P>1989</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Fraser Hambly, Toronto</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Helen MacCallum, Hamilton</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Marj Summers, St. Catharines</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ron Gifford, Scarborough</TD>
<TD>
<P>1989</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Lorraine Murphy, Oshawa.</TD>
<TD>
<P>1990</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Wilf Barlow, Stoney Creek</TD>
<TD>
<P>1990</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tom Simpson, Toronto</TD>
<TD>
<P>1990</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>June Gregg, Kitchener.</TD>
<TD>
<P>1990</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Marg Bratkin, Bramalea</TD>
<TD>
<P>1990</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Mickey Pikor, Beamsville</TD>
<TD>
<P>1990</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Doug Miller, Peterborough</TD>
<TD>
<P>1991</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Walter Valentan, Oshawa</TD>
<TD>
<P>1991</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bill Strong, Toronto</TD>
<TD>
<P>1991</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Janet Peel, Oshawa</TD>
<TD>
<P>1991</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Jim Hoult, Richmond Hill</TD>
<TD>
<P>1991</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Lorena Bates, Toronto</TD>
<TD>
<P>1991</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Oskar Kinzler, Aurora</TD>
<TD>
<P>1992</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tom Cowan, Barrie.</TD>
<TD>
<P>1992</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>George Corbridge, Toronto</TD>
<TD>
<P>1992</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Holly Leet, Nova Scotia</TD>
<TD>
<P>1992</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Nick Pagniello, Scarborough</TD>
<TD>
<P>1992</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Norm Kraatz, Kitchener</TD>
<TD>
<P>1992</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Ken Edge, Hamilton.</TD>
<TD>
<P>1993</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Mabel McDowell, Weston</TD>
<TD>
<P>1993</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Al Gard, Toronto</TD>
<TD>
<P>1993</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Betty Jones, Southampton</TD>
<TD>
<P>1993</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Doris Stewart, Scarborough</TD>
<TD>
<P>1993</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Irene Witley, Hamilton</TD>
<TD>
<P>1993</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>John Moyer, Waterloo</TD>
<TD>
<P>1993</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Sid Morris, Newmarket</TD>
<TD>
<P>1994</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Walter Heeney, Pickering</TD>
<TD>
<P>1994</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bea Milton, Oshawa</TD>
<TD>
<P>1994</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Betty Lou Field, Dorchester</TD>
<TD>
<P>1994</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Diane MacLeod, Mount Albert</TD>
<TD>
<P>1994</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Evelyn Wood, Hamilton</TD>
<TD>
<P>1994</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Rusty Starr, Toronto</TD>
<TD>
<P>1994</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bob Coulter, Stoney Creek</TD>
<TD>
<P>1995</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Marj Bentley, Willowdale</TD>
<TD>
<P>1995</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Harold Hopkins, Toronto</TD>
<TD>
<P>1995</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ab Collingwood, Stoney Creek</TD>
<TD>
<P>1995</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ken Rohrer, Woodstock</TD>
<TD>
<P>1995</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ruth Grant, Chatham</TD>
<TD>
<P>1995</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bill Bird, Scarborough</TD>
<TD>
<P>1996</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Dot Britton, Niagara Falls</TD>
<TD>
<P>1996</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tom McBurnie, Toronto</TD>
<TD>
<P>1996</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Al Snow, Bolton</TD>
<TD>
<P>1996</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ian Cameron, London</TD>
<TD>
<P>1996</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Russ Hurcom, Keswick</TD>
<TD>
<P>1996</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bill Boettger, Kitchener</TD>
<TD>
<P>1997</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Tom Horton, Newmarket</TD>
<TD>
<P>1997</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Jim Morris, Hamilton</TD>
<TD>
<P>1997</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Lou Hrivnak, Toronto</TD>
<TD>
<P>1997</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Primo Falcioni, Mississauga</TD>
<TD>
<P>1997</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Thelma Thompson, Kitchener</TD>
<TD>
<P>1997</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Arnold Witley, Hamilton</TD>
<TD>
<P>1998</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Walter Knapp, St. Thomas</TD>
<TD>
<P>1998</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Vera Inglis, Toronto</TD>
<TD>
<P>1998</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Basil Gasdia, Etobicoke</TD>
<TD>
<P>1998</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Gord Hobson, Barrie</TD>
<TD>
<P>1998</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Lorne Anderson, Windsor</TD>
<TD>
<P>1998</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bob Falconer, Calgary</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Jake Hellewell, Whitby</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Bert Adams, Shallow Lake</TD>
<TD>
<P>1999</TD>
<TD>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Anna Swartzman, Toronto</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Diane Harrison, Scarborough</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Don Betts, St. Catharines</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Walter Heeney, Pickering</TD>
<TD>
<P>1999</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Doug Connerty, Nepean</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Fred Smith, Hamilton</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Ralph Crump, London</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Duke Brooks, Toronto</TD>
<TD>
<P>2000</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Bill Korz, Owen Sound</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Ernie Roggie, Stoney Creek</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Sue Topping, Mississauga</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Susan Davies, Hamilton</TD>
<TD>
<P>2000</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Patricia Jepson, Brampton</TD>
<TD>
<P>2001</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>Ron Dann, Grand Bend</TD>
<TD>
<P>2001</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>&nbsp;</TD>
<TD>
<P>Robert Taylor, Toronto</TD>
<TD>
<P>2001</TD>
<TD>
<P>(dec.)</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Audrey Shanahan, Oakville</TD>
<TD>
<P>2001</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Gerald Carlson, Scarborough</TD>
<TD>
<P>2001</TD>
<TD>&nbsp;</TD>
</TR>
<TR><TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>
<P>X</TD>
<TD>
<P>Matt Dragun, Hamilton</TD>
<TD>
<P>2001</TD>
<TD>&nbsp;</TD>
</TR>
*/
?>
</TABLE>

<?php
include ('../includes/bottom.php');
?>
