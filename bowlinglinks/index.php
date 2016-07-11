<?
require_once('/var/www/vhosts/o5pba.ca/httpdocs/classes/prepend.php');
include_once($htdocsdir.'includes/top.php');

$db=new db();

function listing($section){
	global $db;


	$i = 0;

	$db->query(sprintf('select * from link left join link_sections on link.section = link_sections.id where section = %s order by title',$section));
	//$db->query(sprintf('select * from link where section = "%s" order by title',$section));
	while ($db->next_record()){
		if ($i == 0){
			echo sprintf('<b>%s</b><br>',$db->f('name'));
			$i=1;
		}
		if ($db->f('url')!=''){
			echo sprintf('<a href="%s">',$db->f('url'));
		
			if ($db->f('status')==1){
				echo '<img src="./images/onlinedot.gif" border=0 align="center"> ';
			}else{
				echo '<img src="./images/offlinedot.gif" border=0 align="center"> ';
			}
		}
		echo sprintf('%s</a><br>',$db->f('title'));
	}
	if ($i != 0){
		echo '<br>';
	}
}



?>
<h3>Bowling Links</h3>
<table width=600>
<tr><td>
<?

listing('33');
listing('34');
listing('35');
listing('36');
listing('37');
listing('38');
listing('39');
listing('40');
listing('41');
listing('42');
listing('43');
listing('44');

echo '</td><td>';

listing('45');
listing('46');
listing('47');
listing('48');



$db->query('select modified from link order by modified desc');
$db->next_record();
echo sprintf('<br><br><img src="./images/onlinedot.gif" border=0 align="center"> denotes sites online as of %s.<br>',date('m/j/Y',$db->f('modified')));
echo sprintf('<img src="./images/offlinedot.gif" border=0 align="center"> denotes sites offline as of %s.',date('m/j/Y',$db->f('modified')));

echo '</tr></table>';

include_once($htdocsdir.'includes/bottom.php');

?>
