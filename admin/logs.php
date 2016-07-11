<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');

function showmenu(){
	
	?>
	<H4>View Log Files</H4> 
	<P>
	<HR>
	<P>
	<P> 
	<CENTER>
	<FORM ACTION="<? echo $_SERVER['PHP_SELF']; ?>">
	<TABLE BORDER> 
	<TR> 
	<TD></TD> 
	<TD> Logfile Name </TD>  
	</TR> 
	<TR> 
	<TD><INPUT TYPE="radio" NAME="filen" VALUE="0" checked></TD>
	 <TD> Transaction Log </TD> 
	 </TR>
	<TR> 
	<TD><INPUT TYPE="radio" NAME="filen" VALUE="1"></TD>
	 <TD> Website Log <br>
	 &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<a href="/stats/">Website Log Analysis</a> </TD> 
	 </TR>
	<TR> 
	<TD><INPUT TYPE="radio" NAME="filen" VALUE="2"></TD>
	 <TD> Error Log </TD> 
	 </TR>	 
	 </TABLE>
	 <P> Display last 
	 <SELECT NAME="displaysize"> 
	 <OPTION SELECTED>4KB 
	 <OPTION>16KB 
	 <OPTION>32KB 
	 <OPTION>64KB 
	 <OPTION>256KB 
	 </SELECT> of logfile. <P>
	 <INPUT TYPE=submit name=function VALUE="View">  </FORM>
	 </CENTER><HR><CENTER><A HREF="/">Back to main page</A></CENTER>
	
	
	<?
	
}

function show ($file, $amount){
	global $access,$error;
	if ($file==0){
		$db=new db;
		$db->query(sprintf('select * from _transaction ORDER BY id DESC limit %s;',(10*$amount)));
		
		echo '<H4>Viewing Transaction Log</H4><P><HR><P><P>';
		if ($db->num_rows()){
			echo '<table width=650><tr><td width=75>Date</td><td width=75>User</td><td width=300>Action</td></tr>';
			while($db->next_record()){
				echo '<tr><td width=90><nobr>';
				echo date('M j, Y H:i',$db->f('id'));
				echo '</nobr></td><td width=60><nobr>';
				echo $db->f('user');
				echo '</nobr></td><td>&nbsp;';
				echo $db->f('action');
				echo '</td></tr>';
			}
			echo '</table>';
			
		}else{
			echo 'No Transactions';
		}
	}elseif ($file==1){
		echo '<H4>Viewing Access Log</H4><P><HR><P><P><pre>';
		
		$fp=file ($access);
		$cnt=count($fp);
		
		if ($cnt>10*$amount){
			$start=$cnt-(10*$amount);	
		}else{
			$start=1;
		}
		for ($i=$cnt; $i>=$start; $i--) {
			echo $fp[$i];	
		}
		
		
	}elseif ($file==2){
		echo '<H4>Viewing Error Log</H4><P><HR><P><P><pre>';
		
		$fp=file ($error);
		$cnt=count($fp);
		
		if ($cnt>10*$amount){
			$start=$cnt-(10*$amount);	
		}else{
			$start=1;
		}
		for ($i=$cnt; $i>=$start; $i--) {
			echo $fp[$i];	
		}
		
	}
	echo '<hr><a href="'.$_SERVER['PHP_SELF'].'">Back to Logs</a>';
}

$access='/home/o5pba/logs/access_log2';
$error='/home/o5pba/logs/error_log';


include('./includes/top.php');

switch ($function){
	
	case 'View':
	
	show ($filen, str_replace('KB','',$displaysize));
	break;
	
	default:
	showmenu();
	
	break;
	
}

include('./includes/bottom.php');
page_close();
?>
