<?
require_once('/home/httpd/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
?>
<script language="javascript">
function OpenNewWindow(url,winwidth,winheight) 
{
NewWindow=window.open(url,'descr','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbar=yes,scrollbars=yes,resizable=yes,copyhistory=no,width='+winwidth+',height='+winheight)
}
</SCRIPT>
<?

$path=$htdocsdir.'store/images/';

function ilist(){
	global $path,$webpath, $HTTP_SERVER_VARS;
	$db=new DB;
	
	$db->query('select * from store;');
	echo '<h4>Proshop</h4><br><i>Click Item name for details.</i>';
	echo '<table border="0" cellpadding="2" cellspacing="0" width="500">';
	echo '<tr class=dark><td width=100></td><td>Sizes</td><td colspan=1 width=200>Item Name</td><td>Price</td><td></td></tr>';
	while($db->next_record()){
		echo sprintf('<tr class=light><td width=60></td><td width=40>&nbsp;%s</td><td width=240><a href="%s">%s</td></td><td>%s</td></tr>',$db->f('sizes'), $_SERVER['PHP_SELF'].'?function=details&id='.$db->f('id'),$db->f('name'),$db->f('price'));
	}
	?>
	</table><br><br><br>
	To place your order, please contact the O5 office.<br>

			(416) 426-7167 - voice<br>
			(416) 426-7364 - fax<br>
			<a href="mailto:o5pba@o5pba.ca">o5pba@o5pba.ca</a> - email<br>

	<?
	
}
function idetails($id){
	global $path, $webpath;
	$db=new DB;

	$db->query(sprintf('select * from store where id = %s;',$id));
	$db->next_record();
	echo '<a href="'.$_SERVER['PHP_SELF'].'">Back to list</a>';
	?>
	               <table border="0" cellpadding="2" cellspacing="0" width=550>
	               <td width=400><table width=100%>
				  <tr>
				  	<td colspan=2 class=dark width=300>
  		                <p align="center">Item Details</p>
  					</td>
                  </tr>
                
                  <tr>
                  <td>Product Id:</td><td>
		<?  echo $db->f('productid');	?>

	            	</td></tr>
                  <tr>
                  <td>Item Name:</td><td>
		<?  echo $db->f('name');	?>

	            	</td></tr>
                  <tr>
                  <td>Item Price:</td><td>
		<?  echo $db->f('price');	?>

	            	</td></tr>
                  <tr>
                  <td>Sizes Available:</td><td>
        <?  echo $db->f('sizes');	?>

							</td>
		                  </tr>
                  <tr>
                  <td>Description:</td><td>
        <?  echo $db->f('description');	?>

							</td>
		                  </tr></table></td><td>
		                  
		                  <?
	if(file_exists($path.'t'.$db->f('id').'.jpg')){
		echo 'Picture<br><center>';
		echo sprintf('<a href="%s"><img src="%s" border=0>',$webpath.'/store/images/'.$db->f('id').'.jpg',$webpath.'/store/images/t'.$db->f('id').'.jpg');
		echo '</center><i>click image to view fullsize</i>';		
	}
	
		                  ?>
		                  </td></tr>
               </table>
                <?
}


include('../includes/top.php');

if ($function=='details'){
	idetails($id);
}else{
	ilist();
}
include('../includes/bottom.php');

?>
