<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
//ini_set('memory_limit', '64M');
require_once('../classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));

function fputcsv2 ($fh, $fields, $delimiter = ',', $enclosure = '"', $mysql_null = false) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $output = array();
    foreach ($fields as $field) {
        if ($field === null && $mysql_null) {
            $output[] = 'NULL';
            continue;
        }

        $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? (
            $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
        ) : $field;
    }

    fwrite($fh, join($delimiter, $output) . "\n");
}

function postInWhatsNew() {
	global $htdocsdir;
	
	$today = date('M d, Y');
	$lines = explode('</tr>', file_get_contents($htdocsdir.'whatsnew/newest.php'));
	$inserted = false;
	$insertBeforeNextHeadline = false;
	$newlines = array();
	$headline = "<tr><th>$today</th></tr>" . "\n";
	$content = '<tr><td class="%class%"><a href="/average/?expand=">Average Book Updated</a></td></tr>' . "\n";
	$class = 'dark';
	$contentLineAlreadyExists = false;
	
	foreach ($lines as $id=>$line){
		if (strpos($line,'</tr>') === false && trim($line) != '')
			$line .= '</tr>';
		
		if ($inserted) {
			$newlines[] = $line;
			continue;
		}
		
		if (strpos($line,'<tr><th>') !== false){
			if ($insertBeforeNextHeadline) {
				if (!$contentLineAlreadyExists) {
					if (($contentLinePosition+1) % 2 == 0)
						$class = 'light';
					$newlines[] = str_replace('%class%', $class, $content);
				}
				$newlines[] = $line;
				$insertBeforeNextHeadline = false;
				$inserted = true;
				$contentLinePosition = 0;
				$contentLineAlreadyExists = false;
				$class = 'dark';
				continue;
			}
			$contentLinePosition = 0;
			$contentLineAlreadyExists = false;
			$class = 'dark';
			preg_match('/th>(\w*\s\d\d\,\s\d\d\d\d)<\/th/', $line, $matches);
			$date = $matches[1];
			$time = strtotime($date);
			
			if ($time < strtotime($today)) {
				// insert outside and above
				$newlines[] = $headline;
				$newlines[] = str_replace('%class%', $class, $content);
				$newlines[] = $line;
				$inserted = true;
				//continue;
			}
			else if ($time == strtotime($today)) {
				$newlines[] = $line;
				$insertBeforeNextHeadline = true;
				//continue;
			}
		}
		else {
			$newlines[] = $line;
			$contentLinePosition++;
			if (strpos($line,'Average Book Updated') !== false)
				$contentLineAlreadyExists = true;
		}
	}
	
	if (!$inserted) {
		// insert at the end
		$newlines[] = $headline;
		$newlines[] = str_replace('%class%', $class, $content);
	}
	
	$h = fopen($htdocsdir.'whatsnew/newest.php', 'w');
	//var_dump($newlines);
	if ($h) {
		fwrite($h, implode("", $newlines));
		fclose($h);
	}
}

if ($_REQUEST['action']=='export') {
	
	$db=new db;
	$zid = intval($_POST['association']);
	$sid = intval($_POST['season']);
	$where = '';
	$filename = 'O5PBA AVERAGE BOOK';
	$latestSeasonId = null;
	
	$db->query('SELECT * FROM oa_seasons ORDER BY end_date DESC LIMIT 1');
	if ($db->next_record()) {
		$latestSeasonId = $db->f('id');
		$filename .= ' - SEASON ' . str_replace('-', '/', $db->f('name'));
	}
	
	$db->query("SELECT value FROM oa_settings WHERE `key` = 'SEASONS_TO_INCLUDE_IN_ROLLING_AVERAGE'");
	if ($db->next_record()) {
		$sid = $db->f('value');
	}
	// print_r($sid);
	// exit;
	
	$sql = "SELECT m.*, FLOOR(m.running_pinfalls/m.running_games) AS rolling_average
			FROM oa_members m";
	$where = "WHERE 1=1";
	
	if ($zid) {
		$where .= sprintf(" AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb JOIN oa_leagues l ON l.id = mb.league_id JOIN oa_centres c ON c.id = l.centre_id WHERE mb.member_id = m.id AND mb.season_id IN ($sid) AND c.zone_id = %d)", $zid);
	}
	if ($sid) {
		$where .= " AND EXISTS (SELECT mb.id FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.season_id IN ($sid))";
	}

	$sql .= ' ' . $where;

	if ($zid) {
		$db->query(sprintf("SELECT * FROM oa_zones WHERE id = %d", $zid));
		if ($db->next_record()) {
			$filename .= ' - ZONE ' . strtoupper($db->f('code'));
		}
	}
	if ($sid) {
		$db->query(sprintf("SELECT * FROM oa_seasons WHERE id = %d", $sid));
		if ($db->next_record()) {
			$filename .= ' - SEASON ' . str_replace('-', '/', $db->f('name'));
		}
	}
	// print_r($sql); exit;
	
	
	header('Expires: 0');
	header('Cache-control: private');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Description: File Transfer');
	header('Content-type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="'.$filename.'.csv"');

	$output = fopen('php://output', 'w+'); 
	
	// print the head columns
	fputcsv2($output, array('Association','C5#', 'Last Name', 'First Name',
							'Category', 'Sex', 'League Codes', 'Last Season',
							'Total Games Played', 'Rolling Average', 'Top League Average From Last Season'));
	
	$db->query($sql);
	
	while($db->next_record()) {
		$data = array();
		$db2=new DB;
		$db2->query('use o5pba');
		$lastSeason = 'N/A';
		$associations = '';
		$categories = '';
		$leagues = '';
		$topLeagueAvgFromLatestSeason = 'N/A';
		
		
		$db2->query("SELECT value FROM oa_settings WHERE `key` = 'SEASONS_TO_INCLUDE_IN_ROLLING_AVERAGE'");
		if ($db2->next_record()) {
		$seasonsToConsider = $db2->f('value');
		}
		// print_r($db2);

		$db2->query(sprintf("SELECT s.id, s.name FROM oa_seasons s JOIN oa_member_bowlings mb ON mb.season_id = s.id WHERE mb.member_id = %d AND s.id IN ($seasonsToConsider) ORDER BY s.start_date DESC LIMIT 1", $db->f('id')));
		if ($db2->next_record()) {
			$lastSeason = $db2->f('name');
			$lastSeasonId = $db2->f('id');
		}
		$db2->query(sprintf("SELECT DISTINCT z.name FROM oa_member_bowlings mb JOIN oa_leagues l ON mb.league_id = l.id JOIN oa_centres c ON l.centre_id = c.id JOIN oa_zones z ON c.zone_id = z.id WHERE (mb.member_id = %d AND mb.season_id IN ($seasonsToConsider)) ORDER BY z.name ASC", $db->f('id')));
		while ($db2->next_record()) {
			$associations .= $db2->f('name') . " ";
		}
		$db2->query(sprintf("SELECT DISTINCT mb.category FROM oa_member_bowlings mb WHERE (mb.member_id = %d AND mb.season_id IN ($seasonsToConsider)) ORDER BY mb.category ASC", $db->f('id')));
		while ($db2->next_record()) {
			$categories .= $db2->f('category') . " ";
		}
		$db2->query(sprintf("SELECT DISTINCT CONCAT_WS('-', c.code, l.number) AS league_full_code FROM oa_member_bowlings mb JOIN oa_leagues l ON mb.league_id = l.id JOIN oa_centres c ON l.centre_id = c.id WHERE (mb.member_id = %d AND mb.season_id IN ($seasonsToConsider)) ORDER BY league_full_code ASC", $db->f('id')));
		while ($db2->next_record()) {
			$leagues .= $db2->f('league_full_code') . " ";
		}
		if ($latestSeasonId) {
  		
		  //getting the highest league average

				 if(($latestSeasonId -1 ) == $lastSeasonId)
				 {
						$db2->query(sprintf("select max(mb.league) as league_average from (SELECT FLOOR(SUM(mb.num_pinfalls)/SUM(mb.num_games)) AS league 
						FROM oa_member_bowlings mb
						LEFT JOIN oa_zones z ON z.id = mb.zone_id
						WHERE mb.member_id = %d AND mb.season_id = $lastSeasonId 
						and z.name !='Sanctioned Tournaments' and z.name !='O5PBA'
						group by mb.league_id) as mb", $db->f('id')));
						if ($db2->next_record() && $db2->f('league_average')  ) {
				
							$topLeagueAvgFromLatestSeason = $db2->f('league_average');
						}
			
				}
		}

		$data[] = trim($associations);
		$data[] = $db->f('member_number');
		$data[] = $db->f('last_name');
		$data[] = $db->f('first_name');
		$data[] = trim($categories);
		$data[] = $db->f('sex');
		$data[] = trim($leagues);
		$data[] = $lastSeason;
		$data[] = $db->f('running_games');
		$data[] = $db->f('rolling_average');
		$data[] = $topLeagueAvgFromLatestSeason;
		
		fputcsv2($output, $data);
	}
	
	fclose($output);
	return;
}

include_once('./includes/menuitems.php');
include('./includes/top.php');

$perm->check('averagebook');

//echo '<br/><b>This page is currently under construction!</b><br>';
?>
<SCRIPT LANGUAGE="JavaScript">
function disableForm(theform) {
	if (!doConfirmation())
		return false;
	
	if (document.all || document.getElementById) {
		for (i = 0; i < theform.length; i++) {
			var tempobj = theform.elements[i];
			if (tempobj.type.toLowerCase() == "submit"){
				tempobj.value = 'Sending';
			}
			if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset"){
				tempobj.disabled = true;
			}

		}
	}
	return true;
}

function doConfirmation() {
	return confirm('Are you sure you about this?');
}

function checkSeasons(val) {
	with (document.forms.changeSettingsForm) {
		var els = document.getElementsByName('rolling_average_seasons[]');
		for (var i=0; i < els.length; i++) {
			els[i].checked = val;
		}
	}
}

var centresZone = new Array();
var leaguesCentre = new Array();

var leagueOptions = new Array();
var centreOptions = new Array();

function onZoneDropDownChange(zoneEl, doReset) {
	var selectedValue = zoneEl.options[zoneEl.selectedIndex].value;
	var coptions = centreOptions[selectedValue];
	var centreEl = zoneEl.form.centre_id;
	
	centreEl.innerHTML = '<option value="">----No Centre----</option>';
	
	if(coptions != undefined)
		centreEl.innerHTML += coptions.join('');
	/*else if (selectedValue == '') {
		for (var i in centreOptions) {
			centreEl.innerHTML += centreOptions[i].join('');
		}
	}*/
	
	if (doReset !== false)
		centreEl.selectedIndex = 0;
		
	onCentreDropDownChange(centreEl, doReset);
}

function onCentreDropDownChange(centreEl, doReset) {
	var selectedValue = centreEl.options[centreEl.selectedIndex].value;
	var loptions = leagueOptions[selectedValue];
	var leagueEl = centreEl.form.league_id;
	
	leagueEl.innerHTML = '<option value="">----No League----</option>'
	
	if (loptions != undefined)
		leagueEl.innerHTML += loptions.join('');
	/*else if (selectedValue == '' && leagueEl.form.association.selectedIndex == 0) {
		for (var i in leagueOptions) {
			leagueEl.innerHTML += leagueOptions[i].join('');
		}
	}*/
	
	if (doReset !== false)
		leagueEl.selectedIndex = 0;
}

</SCRIPT>
<?php
/**
 * It true the form will have additional admin buttons to perform non user level operations.
 */
$adminFunctions = false;

/**
 * If true previous average book records will be deleted before importing a csv file.
 */
$deletePreviousBeforeCSVImport = false;

/**
 * These are the expected columns when importing a CSV file.  If they do not match
 * we cannot import since we do not know how to interpret the data.
 */
$expectedColumns = array('No.','Zone','Centre','League Code','League Name','Last Name','First Name','C5#','Category','Sex','Pinfall','Games', 'Year','DUP.');
/* 
NO.(0),ZONE,DC ASSOCIATION,CENTRE,LEAGUE CODE (4),LEAGUE NAME,LAST NAME,FIRST NAME,C5 REG. # (8), 
TOURN. T,REG. R,SR. S,SOO/BLIND S,YBC UNDER 18 S,YBC GRAD,DUP. X,	 (9-15)
M,F,SEASON,NEW MEMBER (19)
*/

$db=new db;

if($_POST['action']=='newmem')
{
  // romba - avoid incorrectly Mac generated cvs
  ini_set("auto_detect_line_endings", true);
 $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
 if ($_FILES["file"]["error"] > 0 || $ext != "csv")
  {
  echo "Error: " . $_FILES["file"]["error"] . " or not a csv<br />";
  }
  else
  {
    if ($fp = fopen($_FILES['file']['tmp_name'],'r')) {
	if ($_POST['rep'] == "n"){$row[20] = "";}
	else {$row[18] = "";}
	 $headers =0;
	 $success=0;
	 $failure=0;
    while (!feof($fp)) {
		  $add =1;
      $currentLine = fgetcsv($fp,8192);
      $row = $currentLine;
		  if($headers ==0 && $row[0] > 0) {
		    $headers =1;
		  }
		  if($headers == 1) { 
		    for($i = 0 ; $i < 21 ; $i++) { // getting rid of quotations
			     $row[$i] = str_replace('"', "", $row[$i]);
			  }
				$db2=new DB;
				$db2->query('use o5pba');
				
        // for ($dd=0;$dd<20;$dd++) {echo $dd.': '.$row[$dd].'<br />';}
        // exit;
				// echo $row[2];exit;
				if ($_POST['rep'] == "n"){
			    $rzone = $row[1];
			    $rcentre = $row[3];
			    $rleague = $row[5];
			    $rc5 = $row[8];
			    if($row[16] =='M') {$rgender = $row[16];} else {$rgender = $row[17];}
			    $rlastn = $row[6];
			    $rfirstn = $row[7];
				if($row[9] !='') {$rcat = $row[9];
				} else if ($row[10] !='') {$rcat = $row[10];
				} else if ($row[11] !='') {$rcat = $row[11];
				} else if ($row[12] !='') {$rcat = $row[12];
				} else if ($row[13] !='') {$rcat = $row[13];
				} else if ($row[14] !='') {$rcat = $row[14];
				} else if ($row[15] !='') {$rcat = $row[15];}
			    $ryear = $row[20];
			    $rgames = $row[19];
			    $rpin = $row[18];
				} else {
				  $rzone = $row[1];
				  $rcentre = $row[3];
				  $rleague = $row[5];
				  $rc5 = $row[8];
				  if($row[16] =='M') {$rgender = $row[16];} else {$rgender = $row[17];}
				  $rlastn = $row[6];
				  $rfirstn = $row[7];
				  if($row[9] !='') {$rcat = $row[9];
				  } else if ($row[10] !='') {$rcat = $row[10];
				  } else if ($row[11] !='') {$rcat = $row[11];
				  } else if ($row[12] !='') {$rcat = $row[12];
				  } else if ($row[13] !='') {$rcat = $row[13];
				  } else if ($row[14] !='') {$rcat = $row[14];
				  } else if ($row[15] !='') {$rcat = $row[15];}
				  $ryear = $row[18];
				  $rgames = '';
				  $rpin = '';
				}
        // echo $rlastn;exit;
           
        if($rzone) {//zone
					$db2->query("select id from oa_zones where name = '$rzone'");
					// print_r($db2);
          if($db2->next_record())	{
					    $zone = $db2->f("id");
              // echo $zone;
					}	else {
					 $add = 0;
					 echo "invalid zone name  on line $row[0] <br/>";
					}
				}	else {
				  $add= 0;
					echo "no zone entered on line $row[0] <br/>";
				}	if($rcentre && $add == 1) {//centre
					$db2->query("select id from oa_centres where name = '$rcentre' and zone_id = $zone");
					// echo "select id from oa_centres where name = '$rcentre' and zone_id = $zone";
          if($db2->next_record())	{
					   $centre = $db2->f("id");
					   // echo $db2->f("id");
					   // echo 'sdfsdf';
					}	else {
					 $add = 0;
					 echo "invalid centre name or centre is not located in zone  on line $row[0] <br/>";
					}
				}	else {
				  if ($add == 1){ echo "no centre entered on line $row[0] <br/>";} //one error per row 
				  $add= 0;
				}
				if($rleague && $add == 1) {//league name
					$db2->query("select id from oa_leagues where  name = '$rleague' and centre_id = $centre");
					if($db2->next_record())	{
					   $league = $db2->f("id");
					}	else {
						$add = 0;
						echo "invalid  league or not located in centre   on line $row[0] <br/>";
					}
				}	else {
				  if ($add == 1){ echo "no  league entered on line $row[0] <br/>";} //one error per row 
				  $add= 0;
				}
				if(!$rc5 && $add == 1) {//check c5#
				  // c5# found don't need to create new member
					$db2->query("select id from oa_members where  member_number ='$rc5'");
					if($db2->next_record()) {
					   $member = $db2->f("id");
					}	else {
					   if ($add == 1){ echo " invalid  c5# number on line $row[0] <br/>";} //one error per row 
				     $add= 0;
				  }
				}	else if($add == 1) { // create new member
				  if($rgender){
				    $gender = $rgender;
				  } else {
						echo "invalid gender on row $row[0] <br/>";
						$add = 0;
				  }
				  if(!$rlastn && $add == 1) {
						echo "invalid last name on row $row[0] <br/>";
						$add = 0;
				  }
				  if(!$rfirstn && $add == 1) {
						echo "invalid first name on row $row[0] <br/>";
						$add = 0;
				  }
				  $db2->query("select * from oa_members where member_number = $rc5");
				  if($db2->next_record()) {
				    //echo "duplicate c5# on $row[0] <br/>";
				    $add = 1;
				  } else if($add == 1 && $_POST['rep'] == "y") {
				    $member_number =$rc5;
						echo "adding new member c5#: $member_number First_name: $rfirstn last_name: $rlastn Gender: $gender <br/>";
  					$query = sprintf("REPLACE INTO oa_members SET first_name = '%s',
  									 last_name = '%s', sex = '%s', member_number = '%s'",
  									 mysql_real_escape_string($rfirstn),
  									 mysql_real_escape_string($rlastn),
  									 $gender, $member_number);
           	if(!$db2->query($query)) {
  					  echo "Failed to create user on row $row[0]";
  					  $add =0;
  					}	else {
  					   $db2->query("select id from oa_members where member_number = $member_number");
  					   $db2->next_record();
  					   $member = $db2->f('id');
  					   // echo $member;
					  }
          }
				  else if ($_POST['rep'] == "n"){
					   echo "does not exist $row[0] <br/>";
					   $add = 0;
					}
				} // endelse
				$cat="";$cat = $rcat;
				
				if($add == 1) { // add the record
			  $ryear =str_replace('"', "", $ryear);
		    $ryear =str_replace("/","-", $ryear);
				$db2->query("select id from oa_seasons where name = '$ryear'");
				if($db2->next_record()) {
				  $season = $db2->f('id');
					// $db2->query("select * from oa_member_bowlings where season_id='$season'  and league_id = '$league' and member_id = '$member'");
					// if(!$db2->next_record()) {
					if ($rgames == '' || $rpin == '') {
					if (isset($rc5) && $rc5!='') {
            $member_number =$rc5;
    				$db2->query("select id from oa_members where member_number = $member_number");
    				$db2->next_record();
    				$member = $db2->f('id');
          } else {}
          // echo $member.'1stQuery<br />';
					// echo $rc5.'1stQuery<br />';
  				$db2->query("DELETE FROM oa_member_bowlings WHERE member_id = '$member' and season_id = '$season' and league_id = '$league'");
          $query = sprintf("INSERT INTO oa_member_bowlings
					   SET member_id = %d, centre_id = %d,
					   zone_id = %d, league_id = %d,
					   season_id = %d, date_entered = NOW(),
					   category = '%s', dup = '%s',
					   num_games = %f, num_pinfalls = %d",
					   $member, $centre, $zone, $league, $season, $cat, "", 0, 0);
						// print_r($query);exit;				
						if(!$db2->query($query)) {
						// if(0>1) {
						  echo "Failed to create record on row $row[0]<br/>";
						  $add =0;
						} else {
						  // echo "Successfully added record on line $row[0] <br/>";
						  $success++;
						}
					}	else {
  				  // echo $member.'2ndQuery<br />';
            // $db2->query("select id from oa_seasons where name = '$ryear'");
  					// if($db2->next_record()) {
    				// $season = $db2->f('id');			
      				$member_number =$rc5;
    					$db2->query("select id from oa_members where member_number = $member_number");
    					$db2->next_record();
    					$member = $db2->f('id');
    					$query = sprintf("REPLACE INTO oa_member_bowlings
    					 SET member_id = %d, centre_id = %d,
    					 zone_id = %d, league_id = %d,
    					 season_id = %d, date_entered = NOW(),
    					 category = '%s', dup = '%s',
    					 num_games = %f, num_pinfalls = %d",
    					 $member, $centre, $zone, $league, $season,$cat, "", $rgames, $rpin);
    					// print_r($query);exit;
    					$db2->query("DELETE FROM oa_member_bowlings WHERE member_id = '$member' and season_id = '$season' and league_id = '$league'");
  					  if(!$db2->query($query)) {
  					  // if(0>1) {
  						  echo "Failed to create record on row $row[0]<br/>";
  						  $add =0;
  						} else {
						    // echo "Successfully added record on line $row[0] <br/>";
						    $success++;
						  }					 
					  // }  
			    }
			  } else {
			   $add = 0;
			   echo "invalid season on  line $row[0] <br/>";
			  }
			}	
		}
		if($add  == 0 ){$failure++;}
		}//endwhile
		$total = $success + $failure;
		echo "<br/><br/>";
		echo "Out of a total of $total rows<br/>";
		echo "$success rows successfully added <br/>";
		echo "$failure rows unsuccessfully added <br/>";
    }
	else
	{
	$a = $_FILES['file']['tmp_name'];
	 echo "could not open file for reading $a";
	
	}
  }	 




}


if ($_POST['action']=='submit'  ){
// echo 'adasda';exit;
    print_r($_FILES);
	$upload_class = new Upload_Files;
	
	$upload_class->temp_file_name = trim($_FILES['upload']['tmp_name']);
	$upload_class->file_name = 'average.csv';//$_FILES['upload']['name'];//'average.sql';

	$upload_class->upload_dir = $htdocsdir;
	$upload_class->upload_log_dir = $htdocsdir."hallfame/pictures/";
	$upload_class->max_file_size = 30960000;
	$upload_class->banned_array = array("");
	$upload_class->ext_array = array(".sql",".csv");

	$valid_ext = $upload_class->validate_extension();
	$valid_size = $upload_class->validate_size();
	$valid_user = $upload_class->validate_user();
	$max_size = $upload_class->get_max_size();
	$file_size = $upload_class->get_file_size();

	$errors = 0;
	
	if (!$valid_ext) {
		$result = "The file extension is invalid, please try again!";
	}elseif (!$valid_size) {
		$result = "The file size is invalid, please try again! The maximum file size is: $max_size and your file was: $file_size";
	}elseif (!$valid_user) {
		$result = "You have been banned from uploading to this server.";
	} else {
		$upload_file = $upload_class->upload_file_with_validation();

		if (!$upload_file) {
			$result = "Your file could not be uploaded!";
		} else {
			$result = "Your file has been successfully uploaded to the server.";
		}
	}

   $fileExtension = substr(strrchr(strtoupper($_FILES['upload']['name']),'.'),1);
   
	if ($result != "Your file has been successfully uploaded to the server."){
		echo $result;

		average_book_index();
	}else if ($fileExtension == 'CSV') {
		echo $result.'<br>CSV File<br>';
      
      ini_set('auto_detect_line_endings',true);

      $db->Halt_On_Error = "report";
	  
      if ($fp = fopen($htdocsdir.'average.csv','r')) {
        // let's do this
        $db->query('START TRANSACTION');
		
		$currentLineNumber = 0; // keep track of what line we are processing
		
         while (!feof($fp)) {
            $currentLineNumber++;
            $currentLine = fgets($fp,8192);

            $row = split(",",rtrim($currentLine));
			
            // make sure that we have the columns we expected
			// in the order we expect them in 'header' of the file
			// i.e. the first line
			if (!$haveOneRow) {
               $haveOneRow = true;

               if (count($row) < count($expectedColumns)) {
                  $mismatch = true;
               } else {
                  for ($i = 0;$i < count($expectedColumns);$i++) {
                     if (strtolower($row[$i]) != strtolower($expectedColumns[$i])) {
                        $mismatch = true;
                        break;
                     }
                  }
               }
               
			   // print an error indicating wrong columns provided
               if ($mismatch) {
                  echo "Your CSV file does not have a header, or it does not match the expected header:<br/>Expected:<table><tr>\n";
                  foreach ($expectedColumns as $col) {
                     echo "<td>$col</td>";
                  }
                  echo '</tr></table>';
				  echo "Found:<table><tr>\n";
                  foreach ($row as $col) {
                     echo "<td>$col</td>";
                  }
                  echo '</tr></table>';
                  break;
               }
               continue;                  
            }
			
			// we are now in the >=2nd line and everything looks good
			if (count($row) >= count($expectedColumns)) {
				// extract values from the parsed line
				$centre_name = $row[1];
				$league_name = $row[2];
				$last_name = $row[3];
				$first_name = $row[4];
				$member_number = $row[5];//$membership_number = $row[5];
				$category = $row[6];
				$dup = $row[7];
				$sex = $row[8];
				$num_games = $row[9];
				$num_pinfalls = $row[10];
				$league_code = $row[11];
				$zone_name = $row[12];
				$season_name = $row[13];
				
				//$doinsert = ($zone_name == $new_zone_name || $season_name == $new_season_name) ? true : false;
				$season_name = str_replace('/', '-', $season_name);
				
				echo "Processing line number $currentLineNumber: $currentLine <br/>";
				
				// Do some error checking first
				if (trim($centre_name) == '') {
					echo "Error: Empty centre name at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($league_name) == '') {
					echo "Error: Empty league name at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($last_name) == '') {
					echo "Error: Empty last name at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($first_name) == '') {
					echo "Error: Empty first name at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($member_number) == '') {
					echo "Error: Empty C5# at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($zone_name) == '') {
					echo "Error: Empty zone at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				if (trim($season_name) == '') {
					echo "Error: Empty year at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				/*if (trim($league_code) == '') {
					echo "Empty league code at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}*/
				
				$dashPos = strpos($league_code, '-');
				if ($dashPos !== false)
					$league_number = substr($league_code, $dashPos+1);
				else {
					echo "Error: Could not get league number at line $currentLineNumber! <br/>";
					$errors++;
					continue;
				}
				$centre_code = substr($league_code, 0, $dashPos);
	
				// extract details from membership_number
				/*$m_parts = explode(' ', $membership_number);
				if (count($m_parts) < 3) {
					echo "Error: Your CSV file contains an invalid membership# at line $currentLineNumber.<br/>";
					$errors++;
					continue;
				}
				
				$zone_code = $m_parts[0];
				if (empty($league_number)) $league_number = isset($m_parts[3]) ? $m_parts[3] : null;
				if (!$member_number) $member_number = $m_parts[1];*/
				
				// get zone id
				$db->query(sprintf("SELECT * FROM oa_zones WHERE name = '%s'", mysql_real_escape_string($zone_name)));
				if ($db->next_record()) {
					$zone_id = $db->f('id');
				}
				else {
					$zone_name_parts = explode(' ', preg_replace('/[^a-zA-Z\s]/', '', $zone_name));
					$zone_code = strtoupper(substr($zone_name_parts[0], 0, 1));
					
					if ($zone_name_parts[1]) {
						$zone_code .= strtoupper(substr($zone_name_parts[1], 0, 1));
					}
					
					// check if this zone code already exists
					$db->query(sprintf("SELECT * FROM oa_zones WHERE code = '%s'",
										$zone_code));
					if ($db->next_record()) {
						$zone_code = strtoupper(substr($zone_name_parts[0], 0, 2));
					}
					
					// create zone
					$query = sprintf("INSERT INTO oa_zones SET name = '%s', code = '%s'",
									 mysql_real_escape_string($zone_name), $zone_code);
					//echo $query . '<br/>';
				    if ($db->query($query) === false) {
					    echo "Failed to create new zone at line $currentLineNumber: " . $db->Error;
						echo "<br/>";
						$errors++;
					    continue;
				    }
				   
				    $zone_id = mysql_insert_id($db->Link_ID);
				}
				
				// get season id
				$s_parts = preg_split('/[\-\/]/', $season_name);
				$season_start = $s_parts[0] . '-07-01';
				$season_end = ($s_parts[0] + 1) . '-06-30';
				$db->query(sprintf("SELECT * FROM oa_seasons WHERE start_date = '%s' OR name = '%s'", $season_start, $season_name));
				if ($db->next_record()) {
					$season_id = $db->f('id');
				}
				else {
					// create season
					$query = sprintf("INSERT INTO oa_seasons SET name = '%s',
									 start_date = '%s', end_date = '%s'",
									 $season_name, $season_start, $season_end);
					//echo $query . '<br/>';
				    if ($db->query($query) === false) {
					    echo "Failed to create new season at line $currentLineNumber: " . $db->Error;
						echo "<br/>";
						$errors++;
					    continue;
				    }
				   
				    $season_id = mysql_insert_id($db->Link_ID);
				}
				
				// get centre id
				$db->query(sprintf("SELECT * FROM oa_centres WHERE name = '%s' AND zone_id = %d",
								   mysql_real_escape_string($centre_name), $zone_id));
				if ($db->next_record()) {
					$centre_id = $db->f('id');
				}
				else {
					// check if this centre code already exists
					$db->query(sprintf("SELECT * FROM oa_centres WHERE code = '%s'",
										$centre_code));
					 if ($db->next_record()) {
						$centre_name_parts = explode(' ', preg_replace('/[^a-zA-Z\s]/', '', $centre_name));
						
						$centre_code = strtoupper(substr($centre_name_parts[0], 0, 1));
						if ($centre_name_parts[1]) {
							$centre_code .= strtoupper(substr($centre_name_parts[1], 0, 1));
						}
						else {
							$centre_code .= strtoupper(substr($centre_name_parts[0], 1, 1));
						}
						
						// check if this centre code already exists
						$db->query(sprintf("SELECT * FROM oa_centres WHERE code = '%s'",
											$centre_code));
						 if ($db->next_record()) {
							$centre_code = strtoupper(substr($centre_name_parts[0], 0, 2));
							if ($centre_name_parts[1]) {
								$centre_code .= strtoupper(substr($centre_name_parts[1], 0, 1));
							}
							else {
								$centre_code .= strtoupper(substr($centre_name_parts[0], 2, 1));
							}
						}
					}
					// create centre
					$query = sprintf("INSERT INTO oa_centres SET name = '%s', code = '%s',
									 zone_id = %d",
									 mysql_real_escape_string($centre_name),
									 $centre_code, $zone_id);
					//echo $query . '<br/>';
				    if ($db->query($query) === false) {
					    echo "Failed to create new centre at line $currentLineNumber: " . $db->Error;
						echo "<br/>";
						$errors++;
					    continue;
				    }
				   
				    $centre_id = mysql_insert_id($db->Link_ID);
				}
				
				// get league id
				$db->query(sprintf("SELECT * FROM oa_leagues WHERE name = '%s' AND centre_id = %d",
								   mysql_real_escape_string($league_name), $centre_id));
				if ($db->next_record()) {
					$league_id = $db->f('id');
				}
				else {
					// create league
					if (!intval($league_number)) {
						// get highest league_number
						$db->query(sprintf("SELECT * FROM oa_leagues WHERE centre_id = %d ORDER BY number DESC LIMIT 1",
										   $centre_id));
						if ($db->next_record()) {
							$league_number = sprintf('%03d',($db->f('number') + 1));
						}
						else {
							$league_number = '001';
						}
					}
					$query = sprintf("INSERT INTO oa_leagues SET name = '%s',
									 code = NULL, number = '%03d', centre_id = %d",
									 mysql_real_escape_string($league_name),
									 $league_number, $centre_id);
					//echo $query . '<br/>';
				    if ($db->query($query) === false) {
					    echo "Failed to create new league at line $currentLineNumber: " . $db->Error;
						echo "<br/>";
						$errors++;
					    continue;
				    }
				   
				    $league_id = mysql_insert_id($db->Link_ID);
				}
				
				// get member
				$db->query(sprintf("SELECT * FROM oa_members WHERE member_number = '%s'", $member_number));
				if ($db->next_record()) {
					$member_id = $db->f('id');
				}
				else {
					// create member
					
					$query = sprintf("INSERT INTO oa_members SET first_name = '%s',
									 last_name = '%s', sex = '%s', member_number = '%s'",
									 mysql_real_escape_string($first_name),
									 mysql_real_escape_string($last_name),
									 $sex, $member_number);
					//echo $query . '<br/>';
				    if ($db->query($query) === false) {
					    echo "Failed to create new member at line $currentLineNumber: " . $db->Error;
						echo "<br/>";
						$errors++;
					    continue;
				    }
				   
				    $member_id = mysql_insert_id($db->Link_ID);
				}
				
				// enter bowling record
				$query = sprintf("REPLACE INTO oa_member_bowlings
									SET member_id = %d, centre_id = %d,
										zone_id = %d, league_id = %d,
										season_id = %d, date_entered = NOW(),
										category = '%s', dup = '%s',
										num_games = %f, num_pinfalls = %d",
										$member_id, $centre_id, $zone_id,
										$league_id, $season_id, $category,
										$dup, $num_games, $num_pinfalls);
				
				//echo $query . '<br/>';
				if ($db->query($query) === false) {
					echo "Failed to create member bowling record at line $currentLineNumber: " . $db->Error;
					echo "<br/>";
					$errors++;
					continue;
				}
				else {
					
				}
			}
         }
		 /* Scan the input file for header rows (while !feof()) */
		
	
	
              
               
               
		
            
		
		
		 //$db->query('ROLLBACK');
		 $db->query('COMMIT');
		 
		 // update rolling average and ranks
		 update_rolling_averages_and_ranks();
		 
		 echo("Updated Average Book with CSV file, updating/merging with previous records.  $currentLineNumber rows read with $errors errors");
         logit("Updated Average Book with CSV file, updating/merging with previous records.  $currentLineNumber rows read with $errors errors");
         
         $db->Halt_On_Error = "true";
         
         fclose($fp);
		 
		 postInWhatsNew();
      } else {
         print('Error: Could not parse uploaded file. '.$htdocsdir."average.csv\n");
      }
	}/*else if ($fileExtension == 'SQL') {
		echo $result.'<br>SQL File<br>';

		system('mysql -u o5pba -psecrethello < '.$htdocsdir.'average.csv');
      
      logit("Updated Average Book with SQL file");
	}*/else {
		echo $result.'<br>Unknown file type.<br>';
	}

}
else if ($_REQUEST['action']=='editseasons') {
	echo '<h4>Edit Seasons</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$query = sprintf("UPDATE oa_seasons
						  SET name = '%s', start_date = '%s', end_date = '%s'
						  WHERE id = %d",
						 $_POST['name'], $_POST['start_date'], $_POST['end_date'],
						 $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to update season: " . $db->Error;
		}
		else {
			//postInWhatsNew();
			logit("Updated season #" . $_POST['id'] . " with values: " . $_POST['name']
				  . ", " . $_POST['start_date'] . ", " . $_POST['end_date']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		 $query = sprintf("INSERT INTO oa_seasons
						  SET name = '%s', start_date = '%s', end_date = '%s'",
						 $_POST['name'], $_POST['start_date'], $_POST['end_date']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to insert season: " . $db->Error;
		}
		else {
			logit("Added new season with values: " . $_POST['name']
				  . ", " . $_POST['start_date'] . ", " . $_POST['end_date']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Delete'])) {
		$db->Halt_On_Error = "report";
		$db->query('START TRANSACTION');
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE season_id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete members bowling data for season: " . $db->Error;
			$db->query('ROLLBACK');
		}
		else {
			$query = sprintf("DELETE FROM oa_seasons
							  WHERE id = %d", $_POST['id']);
		  
		    //echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to delete season: " . $db->Error;
				$db->query('ROLLBACK');
			}
			else {
				$db->query('COMMIT');
				//$db->query('ROLLBACK');
				update_rolling_averages_and_ranks();
				logit("Deleted season #" . $_POST['id']);
			}
		}
	}
	edit_seasons_form();
}
else if ($_REQUEST['action']=='editzones') {
	echo '<h4>Edit Zones</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$query = sprintf("UPDATE oa_zones
						  SET name = '%s', code = '%s'
						  WHERE id = %d",
						 mysql_real_escape_string($_POST['name']), $_POST['code'], $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to update zone: " . $db->Error;
		}
		else {
			logit("Updated zone #" . $_POST['id'] . " with values: " . $_POST['name']
				  . ", " . $_POST['code']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		$query = sprintf("INSERT INTO oa_zones
						  SET name = '%s', code = '%s'",
						 mysql_real_escape_string($_POST['name']), $_POST['code']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to insert zone: " . $db->Error;
		}
		else {
			logit("Added new zone with values: " . $_POST['name'] . ", " . $_POST['code']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Delete'])) {
		$db->Halt_On_Error = "report";
		$db->query('START TRANSACTION');
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE zone_id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete members bowling data in zone: " . $db->Error;
			$db->query('ROLLBACK');
		}
		else {
			$query = sprintf("DELETE oa_leagues.* FROM oa_leagues, oa_centres
							  WHERE oa_centres.id = centre_id AND zone_id = %d",
							  $_POST['id']);
		  
		    //echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to delete leagues of centres of zone: " . $db->Error;
				$db->query('ROLLBACK');
			}
			else {
				$query = sprintf("DELETE FROM oa_centres
								  WHERE zone_id = %d", $_POST['id']);
			
			    //echo $query . '<br/>';
			    if ($db->query($query) === false) {
					echo "Failed to delete centres of zone: " . $db->Error;
					$db->query('ROLLBACK');
			    }
			    else {
					$query = sprintf("DELETE FROM oa_zones
										WHERE id = %d", $_POST['id']);
				  
					  //echo $query . '<br/>';
					  if ($db->query($query) === false) {
						  echo "Failed to delete zone: " . $db->Error;
						  $db->query('ROLLBACK');
					  }
					  else {
						  $db->query('COMMIT');
						  //$db->query('ROLLBACK');
						  update_rolling_averages_and_ranks();
						  logit("Deleted zone #" . $_POST['id']);
					  }
			    }
			}
		}
	}
	edit_zones_form();
}
else if ($_REQUEST['action']=='editcentres') {
	echo '<h4>Edit Centres</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$query = sprintf("UPDATE oa_centres
						  SET name = '%s', code = '%s', zone_id = %d
						  WHERE id = %d",
						mysql_real_escape_string($_POST['name']), $_POST['code'], $_POST['zone_id'],
						$_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to update centre: " . $db->Error;
		}
		else {
			update_zone_ranks();
			logit("Updated centre #" . $_POST['id'] . " with values: " . $_POST['name']
				  . ", " . $_POST['code']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		$query = sprintf("INSERT INTO oa_centres
						  SET name = '%s', code = '%s', zone_id = %d",
						mysql_real_escape_string($_POST['name']), $_POST['code'],
						$_POST['zone_id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to insert centre: " . $db->Error;
		}
		else {
			update_zone_ranks();
			logit("Added new centre with values: " . $_POST['name'] . ", " . $_POST['code']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Delete'])) {
		$db->Halt_On_Error = "report";
		$db->query('START TRANSACTION');
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE centre_id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete members bowling data in centre: " . $db->Error;
			$db->query('ROLLBACK');
		}
		else {
			$query = sprintf("DELETE FROM oa_leagues
							  WHERE centre_id = %d", $_POST['id']);
		  
		    //echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to delete leagues of centre: " . $db->Error;
				$db->query('ROLLBACK');
			}
			else {
				$query = sprintf("DELETE FROM oa_centres
								  WHERE id = %d", $_POST['id']);
			
			    //echo $query . '<br/>';
			    if ($db->query($query) === false) {
					echo "Failed to delete centre: " . $db->Error;
					$db->query('ROLLBACK');
			    }
			    else {
					$db->query('COMMIT');
					//$db->query('ROLLBACK');
					update_rolling_averages_and_ranks();
					logit("Deleted centre #" . $_POST['id']);
			    }
			}
		}
	}
	edit_centres_form();
}
else if ($_REQUEST['action']=='editleagues') {
	echo '<h4>Edit Leagues</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$query = sprintf("UPDATE oa_leagues
						  SET name = '%s', number = '%s', centre_id = %d
						  WHERE id = %d",
						mysql_real_escape_string($_POST['name']), $_POST['number'], $_POST['centre_id'],
						$_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to update league: " . $db->Error;
		}
		else {
			update_zone_ranks();
			logit("Updated league #" . $_POST['id'] . " with values: " . $_POST['name']
				  . ", " . $_POST['number']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		$query = sprintf("INSERT INTO oa_leagues
						  SET name = '%s', number = '%s', centre_id = %d",
						mysql_real_escape_string($_POST['name']), $_POST['number'],
						$_POST['centre_id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to insert league: " . $db->Error;
		}
		else {
			update_zone_ranks();
			logit("Added new league with values: " . $_POST['name']
				  . ", " . $_POST['number']);
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Delete'])) {
		$db->Halt_On_Error = "report";
		$db->query('START TRANSACTION');
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE league_id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete members bowling data in league: " . $db->Error;
			$db->query('ROLLBACK');
		}
		else {
			$query = sprintf("DELETE FROM oa_leagues
							  WHERE id = %d", $_POST['id']);
		  
		    //echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to delete league: " . $db->Error;
				$db->query('ROLLBACK');
			}
			else {
				$db->query('COMMIT');
				//$db->query('ROLLBACK');
				update_rolling_averages_and_ranks();
				logit("Deleted league #" . $_POST['id']);
			}
		}
	}
	edit_leagues_form();
}
else if ($_REQUEST['action']=='editmembers') {
	echo '<h4>Edit Members</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$mergeIntoMId = intval($_POST['merge_into_member_id']);
		// check if we have to merge this
		// member into another one
		if ($mergeIntoMId > 0 && $mergeIntoMId != $_POST['id']) {
			$mergeIntoMemberNumber = '';
			// make sure member exists
			$query = sprintf("SELECT * FROM oa_members WHERE id = %d", $mergeIntoMId);
			$db->query($query);
			if ($db->next_record()) {
				$mergeIntoMemberNumber = $db->f('member_number');
				$db->Halt_On_Error = "report";
				
				$db->query('START TRANSACTION');
				
				// copy bowling data from source to target
				$query = sprintf("UPDATE oa_member_bowlings
									SET member_id = %d
									WHERE member_id = %d",
								$mergeIntoMId, $_POST['id']);
				echo $query . '<br/>';
				if ($db->query($query) === false) {
					$db->query('ROLLBACK');
					echo "Error: Failed to copy member bowling data from source to target; " . $db->Error;
					echo '<br/>';
				}
				else {
					$query = sprintf("DELETE FROM oa_members
										WHERE id = %d", $_POST['id']);
					echo $query . '<br/>';
					$db->query($query);
					
					$db->query('COMMIT');
					//$db->query('ROLLBACK');
					update_rolling_averages_and_ranks();
					logit("Merged member #" . $_POST['id'] . " (" . $_POST['member_number']
						  . ") into member #" . $mergeIntoMId . "($mergeIntoMemberNumber)");
				}
			}
			else {
				echo "Error: Chosen member to merge into was not found!";
				echo '<br/>';
			}
		}
		// no merging - simple update
		else {
			$query = sprintf("UPDATE oa_members
							  SET first_name = '%s', last_name = '%s',
									prev_member_number = member_number,
									sex = '%s', member_number = '%s'
							  WHERE id = %d",
							 mysql_real_escape_string($_POST['first_name']),
							 mysql_real_escape_string($_POST['last_name']), $_POST['sex'],
							 $_POST['member_number'], $_POST['id']);
			
			//echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to update member: " . $db->Error;
			}
			else {
				logit("Updated member #" . $_POST['id'] . " with values: " . $_POST['first_name']
					  . ", " . $_POST['last_name'] . ", " . $_POST['sex']
					  . ", " . $_POST['member_number']);
			}
		}
	}
	else if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		$query = sprintf("INSERT INTO oa_members
						  SET first_name = '%s', last_name = '%s',
								sex = '%s', member_number = '%s'",
						 mysql_real_escape_string($_POST['first_name']),
						 mysql_real_escape_string($_POST['last_name']), $_POST['sex'],
						 $_POST['member_number']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to insert member: " . $db->Error;
		}
		else {
			update_rolling_averages_and_ranks();
			logit("Added new member with values: " . $_POST['first_name']
				  . ", " . $_POST['last_name'] . ", " . $_POST['sex']
				  . ", " . $_POST['member_number']);
		}
	}
	else if (isset($_REQUEST['id']) && isset($_REQUEST['Delete'])) {
		$db->Halt_On_Error = "report";
		$db->query('START TRANSACTION');
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE member_id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete member's bowling data: " . $db->Error;
			$db->query('ROLLBACK');
		}
		else {
			$query = sprintf("DELETE FROM oa_members
							  WHERE id = %d", $_POST['id']);
		  
		    //echo $query . '<br/>';
			if ($db->query($query) === false) {
				echo "Failed to delete member: " . $db->Error;
				$db->query('ROLLBACK');
			}
			else {
				$db->query('COMMIT');
				//$db->query('ROLLBACK');
				update_rolling_averages_and_ranks();
				logit("Deleted member #" . $_POST['id']);
			}
		}
	}
	edit_members_form();
} else if ($_REQUEST['action']=='addbowlings') {
  if (isset($_POST['id']) && isset($_REQUEST['Add'])) {
		$query = sprintf("INSERT INTO oa_member_bowlings
						  SET num_games = %f, num_pinfalls = %d,
								season_id = %d, league_id = %d,
								centre_id = %d, zone_id = %d,
								category = '%s', member_id = %d",
						 $_POST['num_games'], $_POST['num_pinfalls'], $_POST['season_id'],
						 $_POST['lid'], $_POST['cid'], $_POST['zid'],
						 $_POST['category'], $_POST['member_id']);
		
		//echo $query . '<br/>';die();
		if ($db->query($query) === false) {
			echo "Failed to insert bowling data: " . $db->Error;
		}
		else {
			update_rolling_averages_and_ranks();
			logit("Added new member bowling data with values: " . $_POST['num_games']
				  . ", " . $_POST['num_pinfalls']);
			postInWhatsNew();
		}
	}

  add_member_bowlings_form();
}
else if ($_REQUEST['action']=='editbowlings') {
	echo '<h4>Edit Bowling Data</h4>';
	if (isset($_POST['id']) && isset($_REQUEST['Submit'])) {
		$query = sprintf("UPDATE oa_member_bowlings
						  SET num_games = %f, num_pinfalls = %d,
								season_id = %d, league_id = %d,
								centre_id = %d, zone_id = %d,
								category = '%s', member_id = %d
						  WHERE id = %d",
						 $_POST['num_games'], $_POST['num_pinfalls'], $_POST['season_id'],
						 $_POST['league_id'], $_POST['centre_id'], $_POST['zone_id'],
						 $_POST['category'], $_POST['member_id'], $_POST['id']);
		
		//echo $query . '<br/>';die();
		if ($db->query($query) === false) {
			echo "Failed to update bowling data: " . $db->Error;
		}
		else {
			update_rolling_averages_and_ranks();
			logit("Updated member bowling data #" . $_POST['id'] . " with values: " . $_POST['num_games']
				  . ", " . $_POST['num_pinfalls']);
			postInWhatsNew();
		}
	}
	
	else if (isset($_POST['id']) && isset($_REQUEST['Delete'])) {
		$query = sprintf("DELETE FROM oa_member_bowlings
						  WHERE id = %d", $_POST['id']);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to delete bowling data: " . $db->Error;
		}
		else {
			update_rolling_averages_and_ranks();
			logit("Deleted member bowling data #" . $_POST['id']);
			postInWhatsNew();
		}
	}
	edit_member_bowlings_form();
}
else if ($_REQUEST['action']=='editsettings') {
	if (isset($_REQUEST['Submit'])) {
		$newValue = isset($_POST['rolling_average_seasons_all'])
					? 'all'
					: implode(',', $_POST['rolling_average_seasons']);
		$query = sprintf("UPDATE oa_settings
						  SET value = '%s'
							WHERE `key` = 'SEASONS_TO_INCLUDE_IN_ROLLING_AVERAGE'",
						 $newValue);
		
		//echo $query . '<br/>';
		if ($db->query($query) === false) {
			echo "Failed to update settings: " . $db->Error;
		}
		else {
			update_rolling_averages_and_ranks();
		}
	}
	average_book_index();
}
else{
	average_book_index();
}
//update_rolling_averages_and_ranks();
@ page_close();
include_once($htdocsdir.'includes/bottom.php');

//update_rolling_averages_and_ranks();
//update_zone_ranks();

function update_rolling_averages_and_ranks() {
	update_rolling_averages();
	update_ontario_ranks();
	update_zone_ranks();
	update_seasonal_league_ranks();
}

function update_rolling_averages() {
	global $db;
	$db=new DB;
	
	$db->query("SELECT * FROM oa_settings WHERE `key` = 'SEASONS_TO_INCLUDE_IN_ROLLING_AVERAGE'");
	$db->next_record();
	$seasonsToConsider = $db->f('value');
	
	if ($seasonsToConsider == 'all') {
		$query = "UPDATE oa_members m
						SET running_pinfalls = (SELECT SUM(mb.num_pinfalls) FROM oa_member_bowlings mb WHERE mb.member_id = m.id),
							running_games = (SELECT SUM(mb.num_games) FROM oa_member_bowlings mb WHERE mb.member_id = m.id)";
	}
	else {
		$query = "UPDATE oa_members m
						SET running_pinfalls = (SELECT SUM(mb.num_pinfalls) FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.season_id IN (". $seasonsToConsider . ")),
							running_games = (SELECT SUM(mb.num_games) FROM oa_member_bowlings mb WHERE mb.member_id = m.id AND mb.season_id IN (". $seasonsToConsider . "))";
	}
	//echo $query . '<br/>';
	$db->query($query);
}

function update_ontario_ranks() {
	global $db;
	
	$db2=new DB;
	$db2->query('use o5pba');
	
	$db2->query("SELECT *, (FLOOR(running_pinfalls/running_games)) AS rolling_average FROM oa_members ORDER BY rolling_average DESC, id");
	$rank = 0;
	$arank = 0;
	$avg = null;
	while ($db2->next_record()) {
	   $arank++;
	   if ($avg != $db2->f('rolling_average')) {
			$avg = $db2->f('rolling_average');
			$rank = $arank;
	   }
	   $db=new DB;
	   $query = sprintf("UPDATE oa_members SET ontario_rank = %d WHERE id = %d", $rank, $db2->f('id'));
	   //echo $query . '<br/>';
	   $db->query($query);
	}
}

function update_seasonal_league_ranks() {
	global $db;
	
	$db2=new DB;
	$db2->query('use o5pba');
	
	$db=new DB;
	$db->query("TRUNCATE oa_seasonal_league_rankings");
	$db2->query("SELECT *, (FLOOR(SUM(num_pinfalls)/SUM(num_games))) AS league_average
					FROM oa_member_bowlings
					GROUP BY season_id, league_id, member_id
					ORDER BY zone_id, season_id, league_average DESC, member_id");
	$zrank = 0;
	$zid = null;
	$sid = null;
	$avg = null;
	$arank = 0;
	while ($db2->next_record()) {
		if ($db2->f('zone_id') != $zid) {
			$zrank = 0;
			$arank = 0;
			$zid = $db2->f('zone_id');
			$avg = null;
		}
		if ($db2->f('season_id') != $sid) {
			$zrank = 0;
			$arank = 0;
			$sid = $db2->f('season_id');
			$avg = null;
		}
		
		if ($db2->f('member_id') == null)
			continue;
		
		$arank++;
		if ($avg != $db2->f('league_average')){
			$avg = $db2->f('league_average');
			$zrank = $arank;
		}
		
		//else if ($db2->f('member_id') != null and $db2->f('zone_id') != null) {
			$query = sprintf("INSERT INTO oa_seasonal_league_rankings SET rank = %d,
							   league_id = %d, season_id = %d, member_id = %d", $zrank,
							   $db2->f('league_id'), $sid, $db2->f('member_id'));
			//echo $query . '<br/>';
			$db->query($query);
		//}
	}
}

function update_zone_ranks() {
	global $db;
	$db=new DB;
	$db2=new DB;
	$db2->query('use o5pba');
	
	$db->query("TRUNCATE oa_zone_rankings");
	$db2->query("SELECT *, (FLOOR(SUM(num_pinfalls)/SUM(num_games))) AS zone_average
					FROM oa_member_bowlings
					GROUP BY zone_id, member_id
					ORDER BY zone_id, zone_average DESC, member_id");
	$zrank = 0;
	$zid = null;
	$avg = null;
	$arank = 0;
	while ($db2->next_record()) {
		if ($db2->f('zone_id') != $zid) {
			$zrank = 0;
			$arank = 0;
			$zid = $db2->f('zone_id');
			$avg = null;
		}
		/*if ($db2->f('season_id') != $sid) {
			$zrank = 0;
			$arank = 0;
			$sid = $db2->f('season_id');
			$avg = null;
		}*/
		
		if ($db2->f('member_id') == null)
			continue;
		
		$arank++;
		if ($avg != $db2->f('zone_average')){
			$avg = $db2->f('zone_average');
			$zrank = $arank;
		}
		
		$query = sprintf("INSERT INTO oa_zone_rankings SET rank = %d,
						   zone_id = %d, member_id = %d", $zrank, $zid,
						   $db2->f('member_id'));
		//echo $query . '<br/>';
		$db->query($query);
	}
}

function average_book_index() {
	$upload_class = new Upload_Files;
	echo '<h4>Upload new season members<hr/></h4> <br/>';
	echo "<form method='post' action='$_SERVER[PHP_SELF]' enctype='multipart/form-data'>";
	echo "<input type='hidden' id='action' name='action' value='newmem'/>";
	echo "<p style='font-size:18px;'><input type='radio' id='rep1' name='rep' value='y'/> <label for='rep1'>Beginning of season - Member registration</label>";
	echo "<br /><input type='radio' id='rep2' name='rep' value='n' checked/> <label for='rep2'>End of Season - Update Members bowling stats (games and Pinfall)</p>";
	echo "<input type='file' name='file' id='file'/><br/>";
	
	?>
	 
<input type='submit' name='submit' id='submit' value="load records"/><br/>
<?php
	
	echo "</form>";
	// echo '<h4>Upload Bowling Data<hr/></h4>';
	// $upload_class->average_book_upload_form();
	
	
	echo '<h4>Export Average Book<hr/></h4>';
	export_bowling_data_form();
	
	echo '<h4>Change Rolling Average Calculation Settings<hr/></h4>';
	change_settings_form();
    
	echo '<h4><a href="?action=editseasons">Edit Seasons</a><hr/></h4>';
	echo '<h4><a href="?action=editzones">Edit Zones</a><hr/></h4>';
	echo '<h4><a href="?action=editcentres">Edit Centres</a><hr/></h4>';
	echo '<h4><a href="?action=editleagues">Edit Leagues</a><hr/></h4>';
	echo '<h4><a href="?action=editmembers">Edit Members</a><hr/></h4>';
	echo '<h4><a href="?action=editbowlings">Edit Bowling Data</a> | <a href="?action=addbowlings">Add Bowling Data</a><hr/></h4>';
}

function edit_seasons_form() {
	global $db;
	
	echo '<b><i>New Season</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Start Date</th><th width=180>End Date</th><td colspan=1></td></tr></table></td></tr>';
	
	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'start_date', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'end_date', 'value' => ''));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo sprintf('<tr class=%s><td>',$color);
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('name');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('start_date');
	echo '(Y-m-d)</td>';
	
	echo '<td width=180>';
	$form->show_element('end_date');
	echo '(Y-m-d)</td>';
	
	echo '<td>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table>';
	
	
	$db->query('SELECT * FROM oa_seasons ORDER BY end_date DESC');
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Start Date</th><th width=180>End Date</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => $db->f('name')));
		$form->add_element(array('type' => 'text', 'name' => 'start_date', 'value' => $db->f('start_date')));
		$form->add_element(array('type' => 'text', 'name' => 'end_date', 'value' => $db->f('end_date')));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
		
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('name');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('start_date');
		echo '(Y-m-d)</td>';
		
		echo '<td width=180>';
		$form->show_element('end_date');
		echo '(Y-m-d)</td>';
		
		echo '<td>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';
		
		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	
	}
	
	echo '</table>';
}

function edit_zones_form() {
	global $db;
	
	echo '<b><i>New Zone</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	$form = new form;
		
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'code', 'value' => ''));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo sprintf('<tr class=%s><td>',$color);
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('name');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('code');
	echo '</td>';
	
	echo '<td>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '<td width=50>';
	//$form->show_element('Delete');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table>';
	
	
	$db->query('SELECT * FROM oa_zones ORDER BY code ASC');
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => $db->f('name')));
		$form->add_element(array('type' => 'text', 'name' => 'code', 'value' => $db->f('code')));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
		
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('name');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('code');
		echo '</td>';
		
		echo '<td>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';
		
		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	
	}
	
	echo '</table>';
}

function edit_centres_form() {
	global $db;
	
	$zid = intval($_GET['zid']);
	
	$db->query('SELECT id, name FROM oa_zones ORDER BY name ASC');
	$zones = array();
	while ($db->next_record()) {
		$zones[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	
	echo '<b><i>New Centre</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><th width=180>Zone</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	$form = new form;
	
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'code', 'value' => ''));
	$form->add_element(array('type' => 'select', 'name' => 'zone_id', 'value' => '',
							 'options' => $zones));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo sprintf('<tr class=%s><td>',$color);
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('name');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('code');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('zone_id');
	echo '</td>';
	
	echo '<td>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '<td width=50>';
	//$form->show_element('Delete');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table><br/><br/>';
	
	
	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'action', 'value' => 'editcentres'));
	$form->add_element(array('type' => 'select', 'name' => 'zid', 'value' => $zid,
							 'extrahtml' => 'onchange="this.form.submit()"',
							 'options' => array_merge(array(array('value' => '0',
																  'label' => '----No Zone----')),
													  $zones)));
	
	$form->start('','GET',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"",'form');
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td style="text-align: right">Zone:</td><td style="text-align: left">';
	$form->show_element('zid');
	$form->show_element('action');
	echo '</td></tr></table>';
	$form->finish();
	
	$db->query(sprintf('SELECT * FROM oa_centres WHERE zone_id = %d ORDER BY code ASC', $zid));
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><th width=180>Zone</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => $db->f('name')));
		$form->add_element(array('type' => 'text', 'name' => 'code', 'value' => $db->f('code')));
		$form->add_element(array('type' => 'select', 'name' => 'zone_id', 'value' => $db->f('zone_id'),
								 'options' => array_merge(array(array('value' => '0', 'label' => '----No Zone----')),$zones)));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
		
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('name');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('code');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('zone_id');
		echo '</td>';
		
		echo '<td>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';
		
		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	
	}
	
	echo '</table>';
}

function edit_leagues_form() {
	global $db;
	
	$zid = intval($_GET['zid']);
	$cid = intval($_GET['cid']);
	
	$db->query('SELECT c.id, c.name, z.name AS zone_name
				FROM oa_centres c JOIN oa_zones z ON z.id = c.zone_id
				ORDER BY z.name ASC, c.name ASC');
	$sel = '<select name="cid" onchange="this.form.submit()">';
	$coptions = '<option value="0">----No Centre----</option>';
	$zname = '';
	while ($db->next_record()) {
		if ($db->f('zone_name') != $zname) {
			if ($zname != '')
				$coptions .= '</optgroup>';
			$zname = $db->f('zone_name');
			$coptions .= '<optgroup label="' . $zname . '">';
		}
		$selected = $cid == $db->f('id') ? 'selected' : '';
		$coptions .= sprintf('<option %s value="%d">%s</option>', $selected,
							 $db->f('id'), $db->f('name'));
	}
	$sel .= $coptions . '</select>';
	
	
	echo '<b><i>New League</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><th width=180>Centre</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	$form = new form;
	
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'number', 'value' => ''));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo sprintf('<tr class=%s><td>',$color);
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('name');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('number');
	echo '</td>';
	
	echo '<td width=180>';
	echo '<select name="centre_id">'
			. str_replace(array('<option value="0">----No Centre----</option>', 'selected'), '',
						   $coptions)
			. '</select>';
	echo '</td>';
	
	echo '<td>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '<td width=50>';
	//$form->show_element('Delete');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table><br/><br/>';
	
	
	
	$db->query('SELECT * FROM oa_centres WHERE zone_id = ' . $zid . ' ORDER BY name ASC');
	$centres = array();
	while ($db->next_record()) {
		$centres[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	$db->query('SELECT * FROM oa_zones ORDER BY name ASC');
	$zones = array();
	while ($db->next_record()) {
		$zones[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	
	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'action', 'value' => 'editleagues'));
	$form->add_element(array('type' => 'select', 'name' => 'zid', 'value' => $zid,
							 'extrahtml' => ' onchange="this.form.submit()"',
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Zone----')),$zones)));
	$form->add_element(array('type' => 'select', 'name' => 'cid', 'value' => $cid,
							 'extrahtml' => ' onchange="this.form.submit()"',
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Centre----')),$centres)));
	
	$form->start('','GET',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"",'form');
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td style="text-align: right">Zone:</td><td style="text-align: left">';
	//echo $sel;
	echo $form->show_element('zid');;
	echo '</td>';
	echo '<td style="text-align: right">Centre:</td><td style="text-align: left">';
	echo $form->show_element('cid');;
	echo '</td>';
	echo '<td style="text-align: right"></td><td style="text-align: left">';
	$form->show_element('action');
	echo '</td></tr></table>';
	$form->finish();
	
	$db->query(sprintf('SELECT * FROM oa_leagues WHERE centre_id = %d ORDER BY number ASC', $cid));
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Name</th><th width=180>Code</th><th width=180>Centre</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'text', 'name' => 'name', 'value' => $db->f('name')));
		$form->add_element(array('type' => 'text', 'name' => 'number', 'value' => $db->f('number')));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
		
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('name');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('number');
		echo '</td>';
		
		echo '<td width=180>';
		echo '<select name="centre_id">'
				. preg_replace('/(value="' . $db->f('centre_id') . '")/', '$1 selected',
							   $coptions)
				. '</select>';
		echo '</td>';
		
		echo '<td>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';
		
		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	}
	echo '</table>';
}

function edit_members_form() {
	global $db;
	
	$db->query('SELECT * FROM oa_members ORDER BY last_name ASC, first_name ASC, member_number ASC');
	$members = array();
	while ($db->next_record()) {
		$members[] = array('value' => $db->f('id'), 'label' => $db->f('last_name') . ', ' . $db->f('first_name') . ' (' . $db->f('member_number') . ')');
	}
	
	echo '<b><i>New Member</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>First Name</th><th width=180>Last Name</th><th width=100>Sex</th><th width=180>C5#</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	$form = new form;
		
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'first_name', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'last_name', 'value' => ''));
	$form->add_element(array('type' => 'select', 'name' => 'sex', 'value' => '',
							 'options' => array(array('value' => 'M', 'label' => 'Male'),
												array('value' => 'F', 'label' => 'Female'))));
	$form->add_element(array('type' => 'text', 'name' => 'member_number', 'value' => ''));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo sprintf('<tr class=%s><td>',$color);
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('first_name');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('last_name');
	echo '</td>';
	
	echo '<td width=100>';
	$form->show_element('sex');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('member_number');
	echo '</td>';
	
	echo '<td>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '<td width=50>';
	//$form->show_element('Delete');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table><br/><br/>';
	
	
	$first_name = trim($_GET['first_name']);
	$last_name = trim($_GET['last_name']);
	$member_number = trim($_GET['member_number']);
	
	$where = 'WHERE 1=1';
	if ($first_name) {
		$where .= sprintf(' AND m.first_name LIKE "%%%s%%"', $first_name);
	}
	if ($last_name) {
		$where .= sprintf(' AND m.last_name LIKE "%%%s%%"', $last_name);
	}
	if ($member_number) {
		$where .= sprintf(' AND m.member_number LIKE "%%%s%%"', $member_number);
	}
	$sql = "SELECT m.*
			FROM oa_members m
			$where
			ORDER BY m.last_name ASC, m.first_name ASC";
	
	$db->query("SELECT COUNT(*) AS foundcount FROM ( $sql ) a");
	$db->next_record();
	$foundcount = $db->f('foundcount');
	
	if ($foundcount>(10+$_GET['start'])){
		if (strpos($_SERVER['QUERY_STRING'], 'start=') === false)
			$nextlink=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&start='.($_GET['start']+10);
		else
			$nextlink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$_GET['start'],'start='.($_GET['start']+10),$_SERVER['QUERY_STRING']);
	}
	if ($_GET['start']>0){
		if (strpos($_SERVER['QUERY_STRING'], 'start=') === false)
			$prelink=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&start='.($_GET['start']-10);
		else
			$prelink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$_GET['start'],'start='.($_GET['start']-10),$_SERVER['QUERY_STRING']);
 	}
		
	$db->query($sql . ' LIMIT ' . ($_GET['start']+0) . ', 10');
	
	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'action', 'value' => 'editmembers'));
	$form->add_element(array('type' => 'text', 'name' => 'first_name', 'value' => $first_name));
	$form->add_element(array('type' => 'text', 'name' => 'last_name', 'value' => $last_name));
	$form->add_element(array('type' => 'text', 'name' => 'member_number', 'value' => $member_number));
	$form->add_element(array("type"=>"submit", "name"=>"Fetch", 'value'=>'Get Data'));
	
	$form->start('','GET',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"",'form');
	echo '<table cellspacing=0 cellpadding=5>';
	echo '<tr><td style="text-align: right">First Name:</td><td style="text-align: left">';
	$form->show_element('first_name');
	echo '</td>';
	echo '<td style="text-align: right">Last Name:</td><td style="text-align: left">';
	$form->show_element('last_name');
	echo '</td>';
	echo '<td style="text-align: right">C5#:</td><td style="text-align: left">';
	$form->show_element('member_number');
	echo '</td>';
	echo '<td style="text-align: right"></td><td style="text-align: left">';
	$form->show_element('Fetch');
	$form->show_element('action');
	echo '</td></tr></table>';
	$form->finish();
	
	if (isset($_GET['first_name'])) {
	?>
	<TABLE BORDER=0 WIDTH="800">
	   <TR>
		  <TD WIDTH="33%">
			 <P></P>
		  </TD>
			<TD COLSPAN=2>
			 <P>Displaying records <? echo $_GET['start']+1; ?> through
			 <? echo $_GET['start']+$db->num_rows(); ?> of <? echo $foundcount; ?> records found.</P>
		  </TD>
	   </TR>
	   <TR>
		  <TD WIDTH="33%">
			 <P><? if ($prelink){echo "<a href='$prelink'>"; } ?>
				Previous Results</a>
			 </P>
		  </TD>
		  <TD ALIGN=center WIDTH="34%">
			 <P><centeR>&nbsp;</center></P>
		  </TD>
		  <TD WIDTH="33%">
			 <P ALIGN=right><? if ($nextlink){echo "<a href='$nextlink'>"; } ?>
				Next Results
			 </P>
		  </TD>
	   </TR>
	</TABLE>
	<?php
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Last Name</th><th width=180>First Name</th><th width=100>Sex</th><th width=180>C5#</th><th width=180>Merge Into Member</th><td colspan=1></td><td colspan=1></td></tr></table></td></tr>';
	
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'text', 'name' => 'first_name', 'value' => $db->f('first_name')));
		$form->add_element(array('type' => 'text', 'name' => 'last_name', 'value' => $db->f('last_name')));
		$form->add_element(array('type' => 'select', 'name' => 'sex', 'value' => $db->f('sex'),
								 'options' => array(array('value' => 'M', 'label' => 'Male'),
													array('value' => 'F', 'label' => 'Female'))));
		$form->add_element(array('type' => 'text', 'name' => 'member_number', 'value' => $db->f('member_number')));
		$form->add_element(array('type' => 'select', 'name' => 'merge_into_member_id', 'value' => '',
							 'extrahtml' => 'style="width:180px"',
							 'options' => array_merge(array(array('value' => '0', 'label' => '----Do Not Merge----')), $members)));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
	//	$form->add_element(array("type"=>"checkbox", "name"=>"aseason", 'value'=> $db->f('id')));
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'form');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('last_name');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('first_name');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('sex');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('member_number');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('merge_into_member_id');
		echo '</td>';
		
		echo '<td>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';

		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	}
	echo '</table>';
	}
}

function add_member_bowlings_form() {
	include_once('averagebookhelp.php');

 ?>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('#wait_1').hide();
	$('#zid').change(function(){
	  $('#wait_1').show();
	  $('#result_1').hide();
      $.get("averagebookhelp.php", {
		func: "zid",
		drop_var: $('#zid').val()
      }, function(response){
        $('#result_1').fadeOut();
        setTimeout("finishAjax('result_1', '"+escape(response)+"')", 400);
      });
    	return false;
	});
});

function finishAjax(id, response) {
  $('#wait_1').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
function finishAjax_tier_three(id, response) {
  $('#wait_2').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
</script>
<?php
  global $db;
  if ($_GET['lid']) {
	 	$lid = intval($_GET['lid']);
	}
	if ($_GET['cid']) {
	  $cid = intval($_GET['cid']);
	}
	if ($_GET['zid']) {
	  $zid = intval($_GET['zid']);
	}
	if ($_GET['sid']) {
	  $sid = intval($_GET['sid']);
	}
	if ($_GET['first_name']) {
	  $first_name = trim($_GET['first_name']);
	}
	if ($_GET['last_name']) {
	  $last_name = trim($_GET['last_name']);
	}
	if ($_GET['membership_number']) {
	  $membership_number = trim($_GET['membership_number']);
	}
	
	if($_GET['func'] == "dd1" && isset($_GET['func'])) { 
   dd1($_GET['drop_var']); 
  }
  
	$db->query('SELECT * FROM oa_seasons ORDER BY end_date DESC');
	$seasons = array();
	while ($db->next_record()) {
		$seasons[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	$db->query('SELECT * FROM oa_members ORDER BY last_name ASC, first_name ASC, member_number ASC');
	$members = array();
	while ($db->next_record()) {
		$members[] = array('value' => $db->f('id'), 'label' => $db->f('last_name') . ', ' . $db->f('first_name') . ' (' . $db->f('member_number') . ')');
	}
	$db->query('SELECT * FROM oa_zones ORDER BY name ASC');
	$zones = array();
	while ($db->next_record()) {
		$zones[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	$db->query('SELECT * FROM oa_leagues ORDER BY name ASC');
	$leagues2 = array();
	while ($db->next_record()) {
		$leagues2[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	$db->query('SELECT * FROM oa_centres ORDER BY name ASC');
	$centres2 = array();
	while ($db->next_record()) {
		$centres2[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	} ?>
 
<?php
	echo '<b><i>New Bowling Data</i></b>';
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Member</th><th width=100>Zone</th><th width=100>Centre</th><th width=100>League</th><th width=75>Category</th><th width=106>Games</th><th width=106>Pinfall</th><th width=100>Season</th><td colspan=1 width=50></td><td colspan=1 width=50></td></tr></table></td></tr>';
	
	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => ''));
	$form->add_element(array('type' => 'text', 'name' => 'category', 'value' => '', 'extrahtml' => 'style="width:50px"',));
	$form->add_element(array('type' => 'text', 'name' => 'num_games', 'value' => '', 'extrahtml' => 'style="width:100px"',));
	$form->add_element(array('type' => 'text', 'name' => 'num_pinfalls', 'value' => '', 'extrahtml' => 'style="width:100px"',));
	$form->add_element(array('type' => 'select', 'name' => 'season_id', 'value' => '',
							 'extrahtml' => 'style="width:100px"',
							 'options' => $seasons));
	/*
  $form->add_element(array('type' => 'select', 'name' => 'zone_id', 'value' => '',
							 'extrahtml' => 'style="width:100px" onchange="onZoneDropDownChange(this)"',
							 'options' => $zones));
	$form->add_element(array('type' => 'select', 'name' => 'centre_id', 'value' => '',
							 'extrahtml' => 'style="width:100px" onchange="onCentreDropDownChange(this)"',
							 'options' => $centres2));
	$form->add_element(array('type' => 'select', 'name' => 'league_id', 'value' => '',
							 'extrahtml' => 'style="width:100px"',
							 'options' => $leagues2));
  */
  
	$form->add_element(array('type' => 'select', 'name' => 'member_id', 'value' => '',
							 'extrahtml' => 'style="width:180px"',
							 'options' => $members));
	$form->add_element(array("type"=>"submit", "name"=>"Add", 'value'=>'Add'));
	
	echo '<tr><td>';
	$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'saveMemberBowlingForm');
	echo '<table><tr>';
	
	echo '<td>';
	$form->show_element('id');
	echo '</td>';
	
	echo '<td width=180>';
	$form->show_element('member_id');;
	echo '</td>';
	
	echo '<td width=100>';
	// $form->show_element('zone_id');  ?>
	<select style="width:100px;" name="zid" id="zid">
  <option value="" selected="selected" disabled="disabled">-- Zone --</option>
  <?php getTierOne(); ?>
</select>
	<?php echo '</td>';
	
	echo '<td width=100>';
	// $form->show_element('centre_id');
	echo '<span id="wait_1" style="display: none;">
    <img alt="Please Wait" src="ajax-loader.gif"/>
    </span>
    <span id="result_1" style="display: none;"></span>';
	echo '</td>';
	
	echo '<td width=100>';
	// $form->show_element('league_id');
	echo '<span id="wait_2" style="display: none;">
    <img alt="Please Wait" src="ajax-loader.gif"/>
    </span>
    <span id="result_2" style="display: none;"></span> ';
	echo '</td>';
	
	echo '<td width=75>';
	$form->show_element('category');
	echo '</td>';
	
	echo '<td width=100>';
	$form->show_element('num_games');
	echo '</td>';
	
	echo '<td width=100>';
	$form->show_element('num_pinfalls');
	echo '</td>';
	
	echo '<td width=100>';
	$form->show_element('season_id');
	echo '</td>';
	
	echo '<td width=50>';
	$form->show_element('Add');
	echo '</td>';
	
	echo '<td width=50>';
	//$form->show_element('Delete');
	echo '</td>';
	
	echo '</tr></table>';
	$form->finish();
	echo '</td></tr>';
	echo '<tr><td><hr/></td></tr>';
	echo '</table><br/><br/>'; 
}


function edit_member_bowlings_form() {  
 include_once('averagebookhelp.php');  ?>
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('#wait_1').hide();
	$('#zid').change(function(){
	  $('#wait_1').show();
	  $('#result_1').hide();
      $.get("averagebookhelp.php", {
		func: "zid",
		drop_var: $('#zid').val()
      }, function(response){
        $('#result_1').fadeOut();
        setTimeout("finishAjax('result_1', '"+escape(response)+"')", 400);
      });
    	return false;
	});
});

function finishAjax(id, response) {
  $('#wait_1').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
function finishAjax_tier_three(id, response) {
  $('#wait_2').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
</script>


<?php
  global $db;
	if ($_GET['lid']) {
	 	$lid = intval($_GET['lid']);
	}
	if ($_GET['cid']) {
	  $cid = intval($_GET['cid']);
	}
	if ($_GET['zid']) {
	  $zid = intval($_GET['zid']);
	}
	if ($_GET['sid']) {
	  $sid = intval($_GET['sid']);
	}
	if ($_GET['first_name']) {
	  $first_name = trim($_GET['first_name']);
	}
	if ($_GET['last_name']) {
	  $last_name = trim($_GET['last_name']);
	}
	if ($_GET['membership_number']) {
	  $membership_number = trim($_GET['membership_number']);
	}
	
	if($_GET['func'] == "dd1" && isset($_GET['func'])) { 
   dd1($_GET['drop_var']); 
  }
	  
	
	$db->query('SELECT * FROM oa_seasons ORDER BY end_date DESC');
	$seasons = array();
	while ($db->next_record()) {
		$seasons[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	
	/*
	$db->query('SELECT * FROM oa_leagues WHERE centre_id = ' . $cid . ' ORDER BY name ASC');
	$leagues = array();
	while ($db->next_record()) {
		$leagues[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	*/

   $db->query('SELECT * FROM oa_zones ORDER BY name ASC');
	$zones = array();
	while ($db->next_record()) {
		$zones[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}

	
	$db->query('SELECT * FROM oa_leagues ORDER BY name ASC');
	$leagues2 = array();
	while ($db->next_record()) {
		$leagues2[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
		?>
		<!--
		<script>
		//	leaguesCentre['<?php echo $db->f('id') ?>'] = '<?php echo $db->f('centre_id') ?>';
		</script>
		-->
		<?php
	}
	$db->query('SELECT * FROM oa_centres ORDER BY name ASC');
	$centres2 = array();
	while ($db->next_record()) {
		$centres2[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
		?>
		<!--
		<script>
		//	centresZone['<?php echo $db->f('id') ?>'] = '<?php echo $db->f('zone_id') ?>';
		</script>
		-->
		<?php
	}

	$form = new form;
	$form->add_element(array('type' => 'hidden', 'name' => 'action', 'value' => 'editbowlings'));
	$form->add_element(array('type' => 'text', 'name' => 'first_name', 'value' => $first_name));
	$form->add_element(array('type' => 'text', 'name' => 'last_name', 'value' => $last_name));
	$form->add_element(array('type' => 'text', 'name' => 'membership_number', 'value' => $membership_number));
	$form->add_element(array('type' => 'select', 'name' => 'sid', 'value' => $sid,
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Season----')),$seasons)));
	/*
  $form->add_element(array('type' => 'select', 'name' => 'zid', 'value' => $zid,
							 'extrahtml' => ' onchange="this.form.submit()"',
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Zone----')),$zones)));
   
  $form->add_element(array('type' => 'select', 'name' => 'zid', 'value' => $zid,
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Zone----')),firstdd())));
   
	$form->add_element(array('type' => 'select', 'name' => 'cid', 'value' => $cid,
							 // 'extrahtml' => ' onchange="this.form.submit()"',
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No Centre----')),$centres2)));
	$form->add_element(array('type' => 'select', 'name' => 'lid', 'value' => $lid,
							 'options' => array_merge(array(array('value' => '0', 'label' => '----No League----')),$leagues2)));
	*/
  $form->add_element(array("type"=>"submit", "name"=>"Fetch", 'value'=>'Get Data'));
	
	$form->start('','GET',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"",'form');
	echo '<table cellspacing=0 cellpadding=5>';
	echo '<tr><td style="text-align: right">Season:</td><td style="text-align: left">';
	echo $form->show_element('sid');;
	echo '</td>';    ?>
	

	<?php echo '<td style="text-align: right">Zone:</td><td style="text-align: left">';
	// echo $form->show_element('zid'); ?>
	<select name="zid" id="zid">
  <option value="" selected="selected" disabled="disabled">-- Zone --</option>
  <?php getTierOne(); ?>
</select>
	<?php echo '</td>';
	echo '<td style="text-align: right">Centre:</td><td style="text-align: left">';
	// echo $form->show_element('cid');
	echo '<span id="wait_1" style="display: none;">
    <img alt="Please Wait" src="ajax-loader.gif"/>
    </span>
    <span id="result_1" style="display: none;"></span>';
	echo '</td>';
	echo '<td style="text-align: right">League:</td><td style="text-align: left">';
	// echo $form->show_element('lid');
	echo '<span id="wait_2" style="display: none;">
    <img alt="Please Wait" src="ajax-loader.gif"/>
    </span>
    <span id="result_2" style="display: none;"></span> ';
	echo '</td></tr>';
	echo '<tr><td style="text-align: right">First Name:</td><td style="text-align: left">';
	$form->show_element('first_name');
	echo '</td>';
	echo '<td style="text-align: right">Last Name:</td><td style="text-align: left">';
	$form->show_element('last_name');
	echo '</td>';
	echo '<td style="text-align: right">Membership#:</td><td style="text-align: left">';
	$form->show_element('membership_number');
	echo '</td>';
	echo '<td style="text-align: right"></td><td style="text-align: left">';
	$form->show_element('Fetch');
	$form->show_element('action');
	echo '</td></tr></table>';
	$form->finish();
	
	if (isset($_GET['sid'])) {
  	$db->query('SELECT * FROM oa_members ORDER BY last_name ASC, first_name ASC, member_number ASC');
  	$members = array();
  	while ($db->next_record()) {
  		$members[] = array('value' => $db->f('id'), 'label' => $db->f('last_name') . ', ' . $db->f('first_name') . ' (' . $db->f('member_number') . ')');
  	}
    // $db->query('SELECT * FROM oa_centres WHERE zone_id = ' . $zid . ' ORDER BY name ASC');
    $db->query('SELECT * FROM oa_centres ORDER BY name ASC');
  	$centres = array();
  	while ($db->next_record()) {
  		$centres[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
  	}
	
  	$where = 'WHERE 1=1';
  	if ($_GET['sid']) {
  		// $where .= sprintf(' AND season_id = %d', $sid);
  		$where .= ' AND season_id = '.$sid;
  	}
  	if ($_GET['lid']) {
  		$where .= ' AND l.id =' .$lid;
  	}
  	if ($_GET['cid']) {
  		$where .= ' AND c.id = '.$cid;
  	}
  	if ($_GET['zid']) {
  		$where .= ' AND z.id = '.$zid;
  	}
  	if ($first_name) {
  		$where .= ' AND m.first_name LIKE "%'.$first_name.'%"';
  	}
  	if ($last_name) {
  		$where .= ' AND m.last_name LIKE "%'.$last_name.'%"';
  	}
  	if ($membership_number) {
  		$where .= ' AND CONCAT_WS(\'\', z.code, m.member_number, CONCAT_WS(\'-\', c.code, l.number)) LIKE "%'.$membership_number.'%"';
  	}
  	$sql = "SELECT mb.*, m.first_name, m.last_name, m.member_number, m.prev_member_number
  			FROM oa_member_bowlings mb
  				JOIN oa_leagues l ON l.id = mb.league_id
  				JOIN oa_centres c ON c.id = l.centre_id
  				JOIN oa_zones z ON z.id = c.zone_id
  				JOIN oa_seasons s ON s.id = mb.season_id
  				JOIN oa_members m ON m.id = mb.member_id
  			$where
  			ORDER BY s.end_date DESC, m.last_name ASC, m.first_name ASC";
  	 //  echo $sql;exit;
  	$db->query("SELECT COUNT(*) AS foundcount FROM ( $sql ) a");
  	$db->next_record();
  	$foundcount = $db->f('foundcount');
  	
  	if ($foundcount>(10+$_GET['start'])){
  		if (strpos($_SERVER['QUERY_STRING'], 'start=') === false)
  			$nextlink=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&start='.($_GET['start']+10);
  		else
  			$nextlink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$_GET['start'],'start='.($_GET['start']+10),$_SERVER['QUERY_STRING']);
  	}
  	if ($_GET['start']>0){
  		if (strpos($_SERVER['QUERY_STRING'], 'start=') === false)
  			$prelink=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&start='.($_GET['start']-10);
  		else
  			$prelink=$_SERVER['PHP_SELF'].'?'.str_replace('start='.$_GET['start'],'start='.($_GET['start']-10),$_SERVER['QUERY_STRING']);
  	}
  		
  	$db->query($sql . ' LIMIT ' . ($_GET['start']+0) . ', 10');
  ?>
  
	<TABLE BORDER=0 WIDTH="960">
	   <TR>
		  <TD WIDTH="33%">
			 <P></P>
		  </TD>
			<TD COLSPAN=2>
			 <P>Displaying records <? echo $_GET['start']+1; ?> through
			 <? echo $_GET['start']+$db->num_rows(); ?> of <? echo $foundcount; ?> records found.</P>
		  </TD>
	   </TR>
	   <TR>
		  <TD WIDTH="33%">
			 <P><? if ($prelink){echo "<a href='$prelink'>"; } ?>
				Previous Results</a>
			 </P>
		  </TD>
		  <TD ALIGN=center WIDTH="34%">
			 <P><centeR>&nbsp;</center></P>
		  </TD>
		  <TD WIDTH="33%">
			 <P ALIGN=right><? if ($nextlink){echo "<a href='$nextlink'>"; } ?>
				Next Results
			 </P>
		  </TD>
	   </TR>
	</TABLE>
	<?php
	
	echo '<table cellspacing=0 cellpadding=2>';
	echo '<tr><td><table><tr><td colspan=1></td><th width=180>Member</th><th width=100>Previous C5#</th><th width=100>Zone</th><th width=100>Centre</th><th width=100>League</th><th width=75>Category</th><th width=106>Games</th><th width=106>Pinfall</th><th width=100>Season</th><td colspan=1 width=50></td><td colspan=1 width=50></td></tr></table></td></tr>';
	
	while($db->next_record()){
		$color = ($color=='dark') ? 'light' : 'dark';
		$form = new form;
		
		$form->add_element(array('type' => 'hidden', 'name' => 'id', 'value' => $db->f('id')));
		$form->add_element(array('type' => 'select', 'name' => 'member_id', 'value' => $db->f('member_id'),
								 'extrahtml' => 'style="width:180px"',
								 'options' => $members));
		$form->add_element(array('type' => 'text', 'name' => 'category', 'value' => $db->f('category'), 'extrahtml' => 'style="width:50px"',));
		$form->add_element(array('type' => 'text', 'name' => 'num_games', 'value' => $db->f('num_games'), 'extrahtml' => 'style="width:100px"',));
		$form->add_element(array('type' => 'text', 'name' => 'num_pinfalls', 'value' => $db->f('num_pinfalls'), 'extrahtml' => 'style="width:100px"',));
		$form->add_element(array('type' => 'select', 'name' => 'season_id', 'value' => $db->f('season_id'),
								 'extrahtml' => 'style="width:100px"',
								 'options' => array_merge(array(array('value' => '0', 'label' => '----No Season----')),$seasons)));
		$form->add_element(array('type' => 'select', 'name' => 'zone_id', 'value' => $db->f('zone_id'),
								 'extrahtml' => 'style="width:100px" onchange="onZoneDropDownChange(this)"',
								 'options' => array_merge(array(array('value' => '0', 'label' => '----No Zone----')),$zones)));
	    $form->add_element(array('type' => 'select', 'name' => 'centre_id', 'value' => $db->f('centre_id'),
								 'extrahtml' => 'style="width:100px" onchange="onCentreDropDownChange(this)"',
								 'options' => array_merge(array(array('value' => '0', 'label' => '----No Centre----')),$centres2)));
	    $form->add_element(array('type' => 'select', 'name' => 'league_id', 'value' => $db->f('league_id'),
								 'extrahtml' => 'style="width:100px"',
								 'options' => array_merge(array(array('value' => '0', 'label' => '----No League----')),$leagues2)));
		$form->add_element(array("type"=>"submit", "name"=>"Submit", 'value'=>'Save'));
		$form->add_element(array("type"=>"submit", "name"=>"Delete", 'value'=>'Delete...'));
		
		echo sprintf('<tr class=%s><td>',$color);
		$form->start('','POST',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],"' onSubmit='return doConfirmation();' autocomplete='off'",'saveMemberBowlingForm');
		echo '<table><tr>';
		
		echo '<td>';
		$form->show_element('id');
		echo '</td>';
		
		echo '<td width=180>';
		$form->show_element('member_id');
		echo '</td>';
		
		echo '<td width=100>';
		echo $db->f('prev_member_number');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('zone_id');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('centre_id');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('league_id');
		echo '</td>';
	
		echo '<td width=75>';
		$form->show_element('category');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('num_games');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('num_pinfalls');
		echo '</td>';
		
		echo '<td width=100>';
		$form->show_element('season_id');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Submit');
		echo '</td>';
		
		echo '<td width=50>';
		$form->show_element('Delete');
		echo '</td>';
		
		echo '</tr></table>';
		$form->finish();
		echo '</td></tr>';
	}
	echo '</table>';
	} ?>
	<script type="text/javascript">
		/*function adjustCentreAndLeagueDropDwns() {
			var vforms = document.getElementsByName('saveMemberBowlingForm');
			for(var i=0; i < vforms.length; i++) {
				onZoneDropDownChange(vforms[i].zone_id, false);
			}
		}*/
		
		/*
		var selectedCentres = new Array();
		var selectedLeagues = new Array();
			
		function setupLeagueAndCentreDropDwns() {
			var vforms = document.getElementsByName('saveMemberBowlingForm');
			
			for(var j=0; j < vforms.length; j++) {
				leagueOptions = new Array();
				centreOptions = new Array();

				var loptions = vforms[j].league_id.options;
				var coptions = vforms[j].centre_id.options;
				var cid;
				var zid;
				var str;
				
				for (var i=0; i<loptions.length; i++) {
					cid = leaguesCentre[loptions[i].value];
					if (leagueOptions[cid] == undefined) {
						leagueOptions[cid] = new Array();
					}
					
					str = '<option value="' + loptions[i].value + '"';
					if (loptions[i].selected)
						selectedLeagues.push(leagueOptions[cid].length);
					str += '>' + loptions[i].innerHTML + '</option>';
					
					//if (j == 0)
						leagueOptions[cid].push(str);
				}
				for (var i=0; i<coptions.length; i++) {
					zid = centresZone[coptions[i].value];
					if (centreOptions[zid] == undefined) {
						centreOptions[zid] = new Array();
					}
					
					str = '<option value="' + coptions[i].value + '"';
					if (coptions[i].selected)
						selectedCentres.push(centreOptions[zid].length);
					str += '>' + coptions[i].innerHTML + '</option>';
					
					//if (j == 0)
						centreOptions[zid].push(str);
				}
				
				//onZoneDropDownChange(vforms[i].zone_id, false);
			}
			for(var i=0; i < vforms.length; i++) {
				//alert(i);
				onZoneDropDownChange(vforms[i].zone_id, false);
				vforms[i].centre_id.selectedIndex = selectedCentres[i]+1;
				onCentreDropDownChange(vforms[i].centre_id, false);
				vforms[i].league_id.selectedIndex = selectedLeagues[i]+1;
			}
		}
		
		if(/Safari/i.test(navigator.userAgent)){ //Test for Safari
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
		*/
	</script>
	<?php
}

function export_bowling_data_form() {
	?>
	<FORM autocomplete="off" method="post">
   
   <TABLE BORDER=0>
      <TR>
         <TD VALIGN=top>
            <P ALIGN=right>Association:<br>
         </TD>
         <TD>
            <SELECT NAME="association">
	<OPTION VALUE="" SELECTED>All Associations</OPTION>
<?
global $db;
$db->query('select id, name from oa_zones ORDER BY name ASC');
while ($db->next_record()){
	echo sprintf('<option value="%s">%s',$db->f('id'),$db->f('name'));
}
?>
</SELECT>
	<br>
         </TD>
      </TR>
   <tr>
	<td></td>
   	<td>
	<input type="hidden" value="export" name="action">
	<INPUT TYPE="submit" NAME="Submit" VALUE="Export">
	</td></tr>
   </TABLE>
</FORM><br>
	<?php
}

function change_settings_form() {
	global $db;
	
	$db->query('SELECT * FROM oa_seasons ORDER BY end_date DESC');
	$seasons = array();
	while ($db->next_record()) {
		$seasons[] = array('value' => $db->f('id'), 'label' => $db->f('name'));
	}
	
	$db->query("SELECT * FROM oa_settings WHERE `key` = 'SEASONS_TO_INCLUDE_IN_ROLLING_AVERAGE'");
	$db->next_record();
	?>
	<FORM autocomplete="off" method="post" name="changeSettingsForm">
   
   <TABLE BORDER=0>
      <TR>
         <TD VALIGN=top>
            <P ALIGN=right>Consider Bowling Data Of:<br>
         </TD>
         <TD>
            <input NAME="rolling_average_seasons_all" type="checkbox" value="all"
				   <?php echo 'all' == $db->f('value') ? 'checked' : '' ?>
				   onclick="checkSeasons(this.checked)" />
			All Seasons
	<br>OR The Following Individual Seasons<br/>
		<?php foreach ($seasons as $season): ?>
			<input NAME="rolling_average_seasons[]" type="checkbox"
				   value="<?php echo $season['value'] ?>"
				   <?php echo in_array($season['value'], explode(',',$db->f('value'))) ? 'checked' : '' ?>
						 onclick="this.form.rolling_average_seasons_all.checked = false" />
			<?php echo $season['label'] ?>
			&nbsp;&nbsp;&nbsp;
		<?php endforeach ?>
         </TD>
      </TR>
   <tr>
	<td></td>
   	<td>
	<input type="hidden" value="editsettings" name="action">
	<INPUT TYPE="submit" NAME="Submit" VALUE="Change">
	</td></tr>
   </TABLE>
</FORM><br>
	<?php
}

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function(){
	jQuery("input[type='submit']").each(function(){
		jQuery(this).click(function(){
			setTimeout(function(){ location.reload(); }, 3000);
		});
	});
});
</script>
